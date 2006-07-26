<?php
require_once("nucleo/lib/hilo.php");
require_once("nucleo/lib/vinculador.php");

class solicitud
{
	var $id;							//ID de la solicitud	
	var $info;							//Propiedeades	de	la	solicitud extraidas de la base
	var $info_objetos;					//Informacion sobre los	objetos asociados	al	item
	var $indice_objetos;				//Indice	de	objetos asociados	por CLASE
	var $objetos = array();				//Objetos standarts asociados	al	ITEM
	var $objetos_indice_actual = 0;		//Posicion actual	del array de objetos	
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
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		$this->en_tramite = false;
		$this->item = $item;
		$this->usuario = $usuario;

		for($a=0;$a<count($this->info['objetos']);$a++){
			$indice = $this->info['objetos'][$a]["clase"];
			$this->indice_objetos[$indice][]=$a;
			$objetos[] = $this->info['objetos'][$a]["objeto"];	
		}

		$this->id =	info_instancia::get_id_solicitud();


		//-[2]- Determino	la	ACTIVIDAD (El php	que ejecuta	al	ITEM)	
		//Un	ITEM siempre tiene asociada una ACTIVIDAD, escrita	directamente en PHP,	
		//QUe representa	el	procesamiento correspondiente	al	metodo procesar()	de	la	SOLICITUD
		// Existen	distintos tipos de actividades:
		//  - Si es un comportamiento generico, la actividad	se	denomina	PATRON.
		//  - Si es un comportamiento especifico	del item, la actividad se denomina ACCION.
		//  - Si se guarda como un registro en una tabla y no como	archivo se denomina BUFFER	
		//Es	un	BUFFER??	El	buffer <toba,0> representa	la	ausencia	de	BUFFER.
		if(!(($this->info['basica']['item_act_buffer']==0) 
		&& ($this->info['basica']['item_act_buffer_proyecto']=="toba"))){
					$this->tipo_actividad =	"buffer"; 
		}//Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON
		elseif(!(($this->info['basica']['item_act_patron']=="especifico") 
		&& ($this->info['basica']['item_act_patron_proyecto']=="toba"))){
					 $this->tipo_actividad = "patron";	
		}//Es una ACCION. 
		else{
			  $this->tipo_actividad	= "accion";	
		}	
				
		//-[3]- Obtengo el ID de la solicitud
/*
		//-[4]- Decido	si	la	solicitud se registra en la base	
		switch(apex_pa_registrar_solicitud){
			case "siempre": 
				$this->registrar_db = true;
				break;
			case "nunca":
				$this->registrar_db = false;
				break;
			case "db":
				$this->registrar_db = $this->info['basica']['item_solic_registrar'];
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
				$this->cronometrar =	$this->registrar_db && $this->info['basica']['item_solic_cronometrar'];
				break;
			default:	//Si se equivocan	en	el	punto	de	acceso
				$this->cronometrar =	false;				
		}
*/
		//-[7]- Identifico si la solicitud tiene que	realizar	observaciones
		if(isset($this->info['basica']['item_solic_obs_tipo'])){
			$tipo	= array($this->info['basica']['item_solic_obs_tipo_proyecto'],$this->info['basica']['item_solic_obs_tipo']);
			$this->observar($tipo,$this->info['basica']['item_solic_observacion'],false,false);
		}
/*
		ATENCION: Esto ahora hay que preguntarselo al HILO

		if(isset($this->info['basica']['usuario_solic_obs_tipo'])){
			$tipo	= array($this->info['basica']['usuario_solic_obs_tipo_proyecto'],$this->info['basica']['usuario_solic_obs_tipo']);
			$this->observar($tipo,$this->info['basica']['usuario_solic_observacion'],false,false);
		}
*/
		//-[8]- Cargo los OBJETOS que se encuentran asociados
		$this->log = toba::get_logger();
		toba::get_cronometro()->marcar('SOLICITUD: Cargar	info ITEM',apex_nivel_nucleo);
	}
	
//--------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------

	function finalizar_objetos()
	{
		//--- Finalizo objetos TOBA ----------
		//echo "Empiezo a finalizar los objetos...<br>";
		for($a=0;$a<count($this->objetos);$a++){
			$this->objetos[$a]->destruir();
		}
		cargador_toba::instancia()->destruir();
		
		//--- Finalizo objetos BASICOS -------
		toba::get_hilo()->destruir();
		//dump_session();
	}	
	//--------------------------------------------------------------------------------------------

	function get_tipo()
	{
		return $this->info['basica']['item_solic_tipo'];
	}
	
	function es_item_publico()
	{
		return $this->info['basica']['item_publico'];	
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
		$dump["info"]=$this->info['basica'];	
		$dump["info_objetos"]=$this->info['objetos'];
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
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		$this->en_tramite=true;	
		toba::get_cronometro()->marcar('SOLICITUD: -->	INICIO ACTIVIDAD!',apex_nivel_nucleo);	
		//------------------------------------------------------------
		//--------  PASO el control	a la ACTIVIDAD	 ------------------
		//------------------------------------------------------------
		switch ($this->tipo_actividad) {
			case "accion":	 //--> Disparo	la	ACCION
				 if(trim($this->info['basica']['item_act_accion_script'])!=""){	
				$this->php = $this->info['basica']['item_act_accion_script'];
					  include($this->info['basica']['item_act_accion_script']);
				 }else{//Accion no definida
					throw new excepcion_toba('La accion no se encuentra definida');
				 }	
				 break;
			//***************************	
			case "patron":	 //--> Disparo	el	PATRON
				 if(trim($this->info['basica']['item_act_patron_script'])!=""){	
				$this->php = $this->info['basica']['item_act_patron_script'];
				include($this->info['basica']['item_act_patron_script']);	
				 }else{//Patron no definido
					throw new excepcion_toba('El patron no se encuentra definido');
				 }	
				 break;
			//***************************	
			case "buffer":	 //--> Disparo	el	BUFFER
				$sql = "SELECT	cuerpo FROM	apex_buffer	WHERE buffer = '".$this->info['basica']["item_act_buffer"]."' AND proyecto =  '".$this->info['basica']["item_act_buffer_proyecto"]."';";
				$rs = info_instancia::get_db()->consultar($sql,apex_db_numerico);
				if(!$rs) throw new excepcion_toba('BUFFER vacio...');
				//Ejecuto el codigo PHP	de	la	base
				$this->php = $this->info['basica']["item_act_buffer_proyecto"].",".$this->info['basica']["item_act_buffer"];
				eval($rs[0][0]);
				break;
		}
		toba::get_cronometro()->marcar('SOLICITUD: -->	FIN ACTIVIDAD!',apex_nivel_nucleo);	
	}

//*******************************************************************************************
//**********************************<	AUDITORIA Y	LOG	>***********************************
//*******************************************************************************************

	function registrar()	
	{
		if($this->registrar_db) {
			toba::get_cronometro()->marcar('SOLICITUD: Fin	del registro','nucleo');
			// Solicitud
			info_instancia::registrar_solicitud(	$this->id, $this->info['basica']['item_proyecto'], 
												$this->info['basica']['item'], $this->get_tipo());
			// Cronometro
			if($this->cronometrar){	
				toba::get_cronometro()->registrar($this->id);
			}
			// Observaciones
			if(count($this->observaciones)>0) {
				for($i=0;$i<count($this->observaciones);$i++) {
					$tipo[0] = $this->observaciones[$i][0][0];
					$tipo[1] = $this->observaciones[$i][0][1];
					info_instancia::registrar_solicitud_observaciones($this->id, $tipo, $this->observaciones[$i][1]);
				}
			}
		}
	}

	function observar($tipo,$observacion,$forzar_registro=true,$mostrar=true,$cortar_ejecucion=false)
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
			if( $this->get_tipo() =="consola"){
				echo $observacion	."\n";	
			}else {	
				echo ei_mensaje($observacion,$tipo);
			}
		}	
		$this->observaciones[] = array($tipo,$observacion);
		//ei_arbol($this->observaciones);
		if($cortar_ejecucion){
			//Corto la ejecucion	de	la	solicitud
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
			$this->registrar_db();
			exit();
		}
	}

//*******************************************************************************************
//**********************************<	Preguntas genericas	 >**********************************
//*******************************************************************************************

	function existe_ayuda()	
	{
		return (trim($this->info['basica']['item_existe_ayuda'])!="");	
	}
	
	/**
	* Retorna un arreglo de datos básicos del item que se esta ejecutando
	* @param string $prop Propiedad a obtener (opcional)
	*/
	function get_datos_item($prop=null)
	{
		if (isset($prop)) {
			return $this->info['basica'][$prop];	
		}
		return $this->info['basica'];	
	}
	
	function get_id()
	{
		return $this->id;	
	}

//*******************************************************************************************
//**********************************<	OBJETOS STANDART	 >**********************************
//*******************************************************************************************

	function cargar_objeto($clase,$posicion,$parametros=null)
	//Se indica	una posicion del INDICE	de	objetos ($this->indice_objetos[$clase][$posicion]).
	//El indice	apunta a	la	definicion del	objeto a	cargar ($this->info_objeto).
	//Devuelve un indice	al	objeto creado (En	el	array	$this->objetos)
	 //ATENCION: la clase se especifica	como 'proyecto,clase'
	{
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		//-[1]- El indice	es	valido?
		if(!isset($this->indice_objetos[$clase][$posicion])){	
			$this->observar(array("toba","error"),"SOLICITUD [obtener_id_objeto]: No EXISTE un OBJETO	asociado	al	indice [$clase][$posicion].",false,true,true);
			return -1;
		}
		$posicion =	$this->indice_objetos[$clase][$posicion];	
		$indice = $this->objetos_indice_actual;

		$clave['proyecto'] = $this->info['objetos'][$posicion]['objeto_proyecto'];
		$clave['componente'] = $this->info['objetos'][$posicion]['objeto'];
		$this->objetos[$indice] = constructor_toba::get_runtime( $clave, $clase );

		toba::get_cronometro()->marcar('SOLICITUD: Crear OBJETO	['. $this->info['objetos'][$posicion]['objeto']	.']',apex_nivel_nucleo);
		$this->objetos_indice_actual++;
		return $indice;
	}

	function obtener_indice_objetos()
	{
		return $this->indice_objetos;	
	}
}
?>