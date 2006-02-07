<?php	
//FALTA:	Chequear	que grite cuando no se graba la solicitud	completa	
class solicitud
{
	var $id;							//ID de la solicitud	
	var $info;							//Propiedeades	de	la	solicitud extraidas de la base
	var $info_objetos;					//Informacion sobre los	objetos asociados	al	item
	var $indice_objetos;				//Indice	de	objetos asociados	por CLASE
	var $objetos = array();				//Objetos standarts asociados	al	ITEM
	var $objetos_indice_actual = 0;		//Posicion actual	del array de objetos	
	var $bases_secundarias;				//Lista de las	bases	secundarias	abiertas	(Para	cerrarlas)
	var $observaciones;					//Array de observaciones realizadas	durante la solicitud	
	var $observaciones_objeto;			//Observaciones realizadas	por objetos	STANDART	
	var $tipo_actividad;				//Determina	el	tipo de ACTIVIDAD: buffer,	patron, accion	
	var $php;							//Archivo o	BUFFER que implementa la actividad
	var $en_tramite;					//Indica	si	el	ITEM comenzo a	procesarse
	var $registrar_db;					//Indica	si	se	va	a registrar	la	solicitud
	var $cronometrar;					//Indica	si	se	va	a registrar	el	cronometro de la solicitud	
	var $log;							//Objeto que mantiene el log de la ejecucion

	function __construct($item, $usuario)	
	{
		global $solicitud;
		$solicitud = $this;		//MALLL
		global $cronometro;
		$cronometro->marcar('basura',apex_nivel_nucleo);
		$this->en_tramite = false;
		$this->item = $item;
		$this->usuario = $usuario;

		//-[1]- Busco la definicion del ITEM solicitado y controlo PERMISOS de acceso
		$status = $this->cargar_definicion();
		if(!$status[0]){
			monitor::evento("falta",$status[1],$usuario);	
		}
		//ei_arbol($this->info);
		$status = $this->controlar_permisos();
		if(!$status[0]){
			monitor::evento("falta",$status[1],$usuario);	
		}
		//-[2]- Determino	la	ACTIVIDAD (El php	que ejecuta	al	ITEM)	
		//Un	ITEM siempre tiene asociada una ACTIVIDAD, escrita	directamente en PHP,	
		//QUe representa	el	procesamiento correspondiente	al	metodo procesar()	de	la	SOLICITUD
		// Existen	distintos tipos de actividades:
		//  - Si es un comportamiento generico, la actividad	se	denomina	PATRON.
		//  - Si es un comportamiento especifico	del item, la actividad se denomina ACCION.
		//  - Si se guarda como un registro en una tabla y no como	archivo se denomina BUFFER	
		//Es	un	BUFFER??	El	buffer <toba,0> representa	la	ausencia	de	BUFFER.
		if(!(($this->info['item_act_buffer']==0) 
		&& ($this->info['item_act_buffer_proyecto']=="toba"))){
					$this->tipo_actividad =	"buffer"; 
		}//Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON
		elseif(!(($this->info['item_act_patron']=="especifico") 
		&& ($this->info['item_act_patron_proyecto']=="toba"))){
					 $this->tipo_actividad = "patron";	
		}//Es una ACCION. 
		else{
			  $this->tipo_actividad	= "accion";	
		}	
				
		//-[3]- Obtengo el ID de la solicitud

		$sql = "SELECT	nextval('apex_solicitud_seq'::text);";	
		$rs =	toba::get_db("instancia")->consultar($sql, apex_db_numerico);
		if (empty($rs)) {
			monitor::evento("bug","Imposible	determinar el ID de la SOLICITUD: "	.$db["instancia"][apex_db_con]->ErrorMsg(),$usuario);	
		}
		$this->id =	$rs[0][0];

		//-[4]- Decido	si	la	solicitud se registra en la base	
		switch(apex_pa_registrar_solicitud){
			case "siempre": 
				$this->registrar_db = true;
				break;
			case "nunca":
				$this->registrar_db = false;
				break;
			case "db":
				$this->registrar_db = $this->info['item_solic_registrar'];
				break;
			default:	//Si se equivocan	en	el	punto	de	acceso
				$this->registrar_db = false;
		}

		//-[5]- Determino	si	hay que cronometrar
		switch(apex_pa_registrar_cronometro){
			case "siempre": 
				$this->cronometrar =	$this->registrar_db && true;//SI	no	hay registro de la solicitud,	NO.
				break;
			case "nunca":
				$this->cronometrar =	false;
				break;
			case "db":
				$this->cronometrar =	$this->registrar_db && $this->info['item_solic_cronometrar'];
				break;
			default:	//Si se equivocan	en	el	punto	de	acceso
				$this->cronometrar =	false;				
		}

		//-[7]- Identifico si la solicitud tiene que	realizar	observaciones
		if(isset($this->info['item_solic_obs_tipo'])){
			$tipo	= array($this->info['item_solic_obs_tipo_proyecto'],$this->info['item_solic_obs_tipo']);
			$this->observar($tipo,$this->info['item_solic_observacion'],false,false);
		}
/*
ATENCION: Esto ahora hay que preguntarselo al HILO

		if(isset($this->info['usuario_solic_obs_tipo'])){
			$tipo	= array($this->info['usuario_solic_obs_tipo_proyecto'],$this->info['usuario_solic_obs_tipo']);
			$this->observar($tipo,$this->info['usuario_solic_observacion'],false,false);
		}
*/
		//-[8]- Cargo los OBJETOS que se encuentran asociados
		$this->cargar_info_objetos();
		$this->log = toba::get_logger();
		$cronometro->marcar('SOLICITUD: Cargar	info ITEM',apex_nivel_nucleo);
	
	}
//--------------------------------------------------------------------------------------------

	function cargar_definicion()
	{
		//Cargar informacion de la BASE
		$sql = "SELECT	i.proyecto as							item_proyecto,	
						i.item as									item,	
						i.nombre	as									item_nombre,
						i.descripcion as							item_descripcion,	
						i.actividad_buffer_proyecto as		item_act_buffer_proyecto,
						i.actividad_buffer as					item_act_buffer,	
						i.actividad_patron_proyecto as		item_act_patron_proyecto,
						i.actividad_patron as					item_act_patron,	
						p.archivo as								item_act_patron_script,	
						i.actividad_accion as					item_act_accion_script,	
						i.solicitud_tipo as						item_solic_tipo,	
						i.solicitud_registrar as				item_solic_registrar,
						i.solicitud_obs_tipo_proyecto	as		item_solic_obs_tipo_proyecto,	
						i.solicitud_obs_tipo	as					item_solic_obs_tipo,	
						i.solicitud_observacion	as				item_solic_observacion,	
						i.solicitud_registrar_cron	as			item_solic_cronometrar,	
						i.parametro_a as							item_parametro_a,	
						i.parametro_b as							item_parametro_b,	
						i.parametro_c as							item_parametro_c,
						pt.clase_nombre	as						tipo_pagina_clase,
						pt.clase_archivo as						tipo_pagina_archivo,
						pt.include_arriba	as						item_include_arriba,	
						pt.include_abajo as						item_include_abajo,	
						i.zona_proyecto as						item_zona_proyecto,
						i.zona as									item_zona,
						z.archivo as								item_zona_archivo,
						i.publico as								item_publico,
						ii.item as									item_existe_ayuda
				FROM	apex_item i	
							LEFT OUTER JOIN apex_item_zona z	ON	( i.zona_proyecto	= z.proyecto AND i.zona	= z.zona	)
							LEFT OUTER JOIN apex_item_info ii ON (i.proyecto = ii.item_proyecto AND i.item = ii.item),
						apex_patron	p,	
						apex_pagina_tipo pt	
				WHERE		pt.pagina_tipo	= i.pagina_tipo
				AND		pt.proyecto	= i.pagina_tipo_proyecto
				AND		i.actividad_patron =	p.patron	
				AND		i.actividad_patron_proyecto =	p.proyecto
				AND		i.proyecto = '{$this->item[0]}'
				AND		i.item =	'{$this->item[1]}';";
		//echo($sql)."\n";
		$temp = toba::get_db("instancia")->consultar($sql);
		if(empty($temp)) {
				return array(false,"SOLICITUD: El ITEM '{$this->item[0]},{$this->item[1]}' No existe");
		} else {
			$this->info	= $temp[0];	
			return array(true,"OK!");
		}
	}
//--------------------------------------------------------------------------------------------

	function controlar_permisos()
	{
		//Controlar si el ITEM es PUBLICO
		if($this->info['item_publico'] == "1"){
			return array(true,"OK! (el item es publico)");
		}
		//Controlar PERMISOS desde la DB
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE	= ADODB_FETCH_ASSOC;
		$sql = "SELECT u.usuario as usuario	
					FROM	apex_usuario_grupo_acc_item ui,
							apex_usuario_proyecto up,
							apex_usuario u	
					WHERE	ui.usuario_grupo_acc	= up.usuario_grupo_acc
					AND	ui.proyecto	= up.proyecto
					AND	up.usuario = u.usuario
					AND	u.usuario =	'{$this->usuario}'
					AND	ui.proyecto = '{$this->item[0]}'
					AND	ui.item =	'{$this->item[1]}';";
		$rs = toba::get_db("instancia")->consultar($sql);
		if(empty($rs)){
			return array(false,"EL usuario no posee permisos para acceder al ITEM solicitado");
		}else{
			return array(true,"OK! (el usurio posee permisos)");
		}
	}
	//--------------------------------------------------------------------------------------------

	function finalizar_objetos()
	{
		//--- Finalizo objetos TOBA ----------
		//echo "Empiezo a finalizar los objetos...<br>";
		for($a=0;$a<count($this->objetos);$a++){
			$this->objetos[$a]->destruir();
		}
		//--- Finalizo objetos BASICOS -------
		$this->hilo->destruir();
		//dump_session();
	}	
	//--------------------------------------------------------------------------------------------

	function finalizar_solicitud()
	{
		//-[1]- Cierro	conexiones secundarias
		if(isset($this->bases_secundarias)){
			foreach($this->bases_secundarias	as	$base){
				$db[$base][apex_db_con]->close();
			}
		}
		exit();
		//$cronometro->marcar('SOLICITUD: Finalizar el CONTEXTO',apex_nivel_nucleo);
	}
	//--------------------------------------------------------------------------------------------

	function info()
	//Informacion completa
	{
		$this->info_definicion();
		$this->info_estado();
	}
	//--------------------------------------------------------------------------------------------

	function info_definicion()	
	//Informa en pantalla la definicion	del ITEM, OBJETOS, etc.	
	{
		$dump["info"]=$this->info;	
		$dump["info_objetos"]=$this->info_objetos;
		$dump["indice_objetos"]=$this->indice_objetos;
		ei_arbol($dump,"DEFINICION	de	la	SOLICITUD");
	}
	//--------------------------------------------------------------------------------------------
	
	function info_estado()
	//Informa en pantalla el estado interno
	{
		$dump["id"]= $this->id;	
		$dump["objetos"] = $this->objetos;
		$dump["en_tramite"]=	$this->en_tramite;
		$dump["registrar"]= $this->registrar_db;
		$dump["cronometrar"]=$this->cronometrar;
		$dump["bases_secundarias"]=$this->bases_secundarias;
		$dump["observaciones"]=$this->observaciones;	
		$dump["observaciones_objeto"]=$this->observaciones_objeto;
		ei_arbol($dump,"ESTADO de la SOLICITUD");	
	}
	//--------------------------------------------------------------------------------------------

	function procesar()
/*	
	 @@acceso: core 
	 @@desc:	Ejecuta la actividad	asociada	al	ITEM solicitado
	 @@param: array |	sentencias WHERE a acoplar	
	 @@param: array |	Sentencias FROM a	acoplar	
	 @@param: boolean	| Desactivar la paginacion	
	 @@retorno:	boolean | Estado resultante de la operacion	
*/	
	{
		global $db,	$cronometro	,$ADODB_FETCH_MODE;
		$cronometro->marcar('basura',apex_nivel_nucleo);
		$this->en_tramite=true;	
		$cronometro->marcar('SOLICITUD: -->	INICIO ACTIVIDAD!',apex_nivel_nucleo);	
		  //------------------------------------------------------------
		  //--------  PASO el control	a la ACTIVIDAD	 ------------------
		  //------------------------------------------------------------
		  switch	($this->tipo_actividad){
				//***************************	
				case "accion":	 //--> Disparo	la	ACCION
					 if(trim($this->info['item_act_accion_script'])!=""){	
					$this->php = $this->info['item_act_accion_script'];
						  include($this->info['item_act_accion_script']);
					 }else{//Accion no definida
					monitor::evento("bug","SOLICITUD: La ACCION no se encuentra	definida	ITEM:	'{$this->info['item_proyecto']},{$this->info['item']}'");
					 }	
					 break;
				//***************************	
				case "patron":	 //--> Disparo	el	PATRON
					 if(trim($this->info['item_act_patron_script'])!=""){	
					$this->php = $this->info['item_act_patron_script'];
					include($this->info['item_act_patron_script']);	
					 }else{//Patron no definido
					monitor::evento("bug","SOLICITUD: El PATRON no se encuentra	definida	ITEM:	'{$this->info['item_proyecto']},{$this->info['item']}'");
					 }	
					 break;
				//***************************	
				case "buffer":	 //--> Disparo	el	BUFFER
				$ADODB_FETCH_MODE	= ADODB_FETCH_NUM;
				$sql = "SELECT	cuerpo FROM	apex_buffer	WHERE	buffer =	'".$this->info["item_act_buffer"]."' AND proyecto =  '".$this->info["item_act_buffer_proyecto"]."';";	
				$rs =	$db["instancia"][apex_db_con]->Execute($sql);
					 //SQL:sol_buffer:1
				 if(!$rs){
					monitor::evento("bug","SOLICITUD: No se pudo	obtener el cuerpo	del BUFFER correspondiente	al	'$item' del	item:	".$db["instancia"][apex_db_con]->ErrorMsg());
				}else{
					if($rs->EOF){
						//Supuestamente esto	no	pasa...
						monitor::evento("bug","SOLICITUD: EL BUFFER Solicitado NO EXISTE");
					} else {	
								if(trim($rs->fields[0])==""){	
									 monitor::evento("falta","SOLICITUD: EL BUFFER solicitado se encuentra VACIO");
								}else{
								//Ejecuto el codigo PHP	de	la	base
							$this->php = $this->info["item_act_buffer_proyecto"].",".$this->info["item_act_buffer"];
							eval($rs->fields[0]);
								}
					}
				}			
					 break;
				}		
		  //------------------------------------------------------------
		$cronometro->marcar('SOLICITUD: -->	FIN ACTIVIDAD!',apex_nivel_nucleo);	
	}

//*******************************************************************************************
//**********************************<	AUDITORIA Y	LOG	>***********************************
//*******************************************************************************************

	function	registrar($proyecto)	
	//Atencion:	el	tiempo consumido en el LOGGING y	BENCHMARK no quedan registrados...
	{
		global $cronometro, $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE	= ADODB_FETCH_NUM;
		if($this->registrar_db){
			//-[1]- Registro la solicitud	
			$cronometro->marcar('SOLICITUD: Fin	del registro',"nucleo");
			$tiempo = $cronometro->tiempo_acumulado();
			$sql = "INSERT	INTO apex_solicitud (proyecto, solicitud, solicitud_tipo,	item_proyecto,	item,	tiempo_respuesta)	
					VALUES ('$proyecto','$this->id','".apex_solicitud_tipo."','{$this->info['item_proyecto']}','{$this->info['item']}','$tiempo');";	
			if	($db["instancia"][apex_db_con]->Execute($sql) === false){
				monitor::evento("bug","SOLICITUD: No se pudo	registrar la solicitud:	" .$db["instancia"][apex_db_con]->ErrorMsg());
			}else{
				//-[2]- Registro el cronometro
				if($this->cronometrar){	
					$cronometro->registrar($this->id);
				}
				//-[3]- Registro las	observaciones
				if(count($this->observaciones)>0)
				{
					for($i=0;$i<count($this->observaciones);$i++){
						$sql = "INSERT	INTO apex_solicitud_observacion (solicitud,solicitud_obs_tipo_proyecto,solicitud_obs_tipo,observacion) 
								VALUES ('{$this->id}','".$this->observaciones[$i][0][0]."','".$this->observaciones[$i][0][1]."','".addslashes($this->observaciones[$i][1])."');";
						//echo $sql;
						if	($db["instancia"][apex_db_con]->Execute($sql) === false){
							monitor::evento("bug","SOLICITUD: No se pudo	registrar la observacion: " .$db["instancia"][apex_db_con]->ErrorMsg() . "-" . $sql);
						}
					}
				}
				//-[4]- Registro las	observaciones de los	OBJETOS
				if(count($this->observaciones_objeto)>0)
				{
					for($i=0;$i<count($this->observaciones_objeto);$i++){	
						$sql = "INSERT	INTO apex_solicitud_obj_observacion	(solicitud,	objeto, objeto_proyecto, solicitud_obj_obs_tipo,observacion) 
								VALUES ('{$this->id}','".$this->observaciones_objeto[$i][0][1]."','".$this->observaciones_objeto[$i][0][0]."','".$this->observaciones_objeto[$i][1]."','".addslashes($this->observaciones_objeto[$i][2])."');";
						if	($db["instancia"][apex_db_con]->Execute($sql) === false){
							monitor::evento("bug","SOLICITUD: No se pudo	registrar la observacion de los OBJETOS: " .$db["instancia"][apex_db_con]->ErrorMsg());
						}
					}
				}
			}
		}
	}
//--------------------------------------------------------------------------------------------

	function	observar($tipo,$observacion,$forzar_registro=true,$mostrar=true,$cortar_ejecucion=false)
/*	
	 @@acceso: publico
	 @@desc:	Sistema de registro de OBSERVACIONES
	 @@param: string | Tipo	de	observacion	
	 @@param: string | Cuerpo de la observacion
	 @@param: boolean	| Forzar	el	registro	de	la	solicitud |	true
	 @@param: boolean	| Mostrar el mensaje	en	la	pantalla	| true
	 @@param: boolean	| Cortar	la	ejecucion del script	| false
*/	
	{
		if(!is_array($tipo)){
			$tipo	= array("toba","error");
		}
		if($forzar_registro)	$this->registrar_db=true;
		if($mostrar){
			if(apex_solicitud_tipo =="consola"){
				echo $observacion	."\n";	
			}elseif(apex_solicitud_tipo =="browser"){	
				echo ei_mensaje($observacion,$tipo);
			}
		}	
		$this->observaciones[] = array($tipo,$observacion);
		//ei_arbol($this->observaciones);
		if($cortar_ejecucion){
			//Corto la ejecucion	de	la	solicitud
			$this->finalizar_solicitud();	
			$this->registrar_db();
			exit();
		}
	}
//--------------------------------------------------------------------------------------------

	function	observar_objeto($objeto, $tipo, $observacion, $forzar_registro=true,	$mostrar=true,	$cortar_ejecucion=false)
	//Un objeto	standart	informa a la solicitud!	
	{
		if($forzar_registro)	$this->registrar_db=true;
		if($mostrar) echo	ei_mensaje($observacion,$tipo);
		$this->observaciones_objeto[]	= array($objeto,$tipo,$observacion);
		if($cortar_ejecucion){
			//Corto la ejecucion	de	la	solicitud
			$this->finalizar_solicitud();	
			$this->registrar_db();
			exit();
		}
	}

//*******************************************************************************************
//**********************************<	Preguntas genericas	 >**********************************
//*******************************************************************************************

	function existe_ayuda()	
	{
		return (trim($this->info['item_existe_ayuda'])!="");	
	}
	
	function tipo_solicitud()
	{
		return $this->info['item_solic_tipo'];	
	}
	
	/**
	*	Retorna un arreglo de datos básicos del item que se esta ejecutando
	*/
	function get_datos_item()
	{
		return $this->info;	
	}
	
	function id()
	{
		return $this->id;	
	}

//*******************************************************************************************
//**********************************<	OBJETOS STANDART	 >**********************************
//*******************************************************************************************

	function	cargar_info_objetos()
	{
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		//-[1]- Cargo objetos
		$sql =	"SELECT	o.proyecto as						  objeto_proyecto,
						o.objeto	as						  	objeto,
						o.nombre	as						  	objeto_nombre,
						o.subclase as						objeto_subclase,
						o.subclase_archivo as			objeto_subclase_archivo,
						io.orden	as						  	orden,	
						c.proyecto as					  	clase_proyecto,	
						c.clase as						  	clase,	
						c.archivo as					  	clase_archivo,
						d.proyecto as						fuente_proyecto,
						d.fuente_datos	as				  	fuente,
						d.fuente_datos_motor	as			fuente_motor,
						d.host as						  	fuente_host,	
						d.usuario as					  	fuente_usuario,	
						d.clave as						  	fuente_clave,
						d.base as						  	fuente_base
				FROM	apex_item_objeto io,	
						apex_objeto	o,	
						apex_fuente_datos	d,	
						apex_clase c
				WHERE	io.objeto =	o.objeto	
					 AND		io.proyecto	= o.proyecto
				AND		o.clase = c.clase	
				AND		o.clase_proyecto = c.proyecto	
				AND		o.fuente_datos	= d.fuente_datos
				AND		o.fuente_datos_proyecto	= d.proyecto
				AND		io.item = '".$this->info["item"]."'	
				AND		io.proyecto	= '".$this->info["item_proyecto"]."'
				ORDER	BY	io.orden;";	
		$rs = toba::get_db("instancia")->consultar($sql);
		if(empty($rs)){
			$objetos = null;
			//No hay	OBJETOS standart asociados	al	item
		}else{
			$this->info_objetos = $rs;	
			for($a=0;$a<count($this->info_objetos);$a++){
				$indice = $this->info_objetos[$a]["clase"];
				$this->indice_objetos[$indice][]=$a;
				$objetos[] = $this->info_objetos[$a]["objeto"];	
			}
		}
		toba::get_cronometro()->marcar('SOLICITUD: Cargar	info OBJETOS',apex_nivel_nucleo);
		return $objetos;
	}
//--------------------------------------------------------------------------------------------

	function cargar_objeto($clase,$posicion,$parametros=null)
	//Se indica	una posicion del INDICE	de	objetos ($this->indice_objetos[$clase][$posicion]).
	//El indice	apunta a	la	definicion del	objeto a	cargar ($this->info_objeto).
	//Devuelve un indice	al	objeto creado (En	el	array	$this->objetos)
	 //ATENCION: la clase se especifica	como 'proyecto,clase'
	{
		global $cronometro, $db;
		$cronometro->marcar('basura',apex_nivel_nucleo);
		//-[1]- El indice	es	valido?
		if(!isset($this->indice_objetos[$clase][$posicion])){	
			$this->observar(array("toba","error"),"SOLICITUD [obtener_id_objeto]: No EXISTE un OBJETO	asociado	al	indice [$clase][$posicion].",false,true,true);
			return -1;
		}
		$posicion =	$this->indice_objetos[$clase][$posicion];	
		$indice = $this->objetos_indice_actual;

		$clave['proyecto'] = $this->info_objetos[$posicion]['objeto_proyecto'];
		$clave['componente'] = $this->info_objetos[$posicion]['objeto'];
		$this->objetos[$indice] = constructor_toba::get_runtime( $clave, $clase );

		$cronometro->marcar('SOLICITUD: Crear OBJETO	['. $this->info_objetos[$posicion]['objeto']	.']',apex_nivel_nucleo);
		$this->objetos_indice_actual++;
		return $indice;
	}

	function obtener_indice_objetos()
	{
		return $this->indice_objetos;	
	}
}
?>