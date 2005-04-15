<?
require_once("objeto.php");	//Ancestro de todos los	OE
require_once("nucleo/browser/interface/ef.php");//	Elementos de interface

/*
	Los EF deberian cargar su estado en el momento de obtener la
	interface, no en su creacion.

*/

class objeto_ei_formulario extends objeto
/*
	@@acceso: actividad
	@@desc: Esta clase contruye la Interface Grafica de un registro de una tabla
*/
{
	var $elemento_formulario;		//	interno | array |	Rererencias	a los	ELEMENTOS de FORMULARIO
	var $nombre_formulario;			//	interno | string | Nombre del	FORMULARIO en el cliente
	var $prefijo;						//Prefijo de todos los objetos creados por este FORMs
	var $lista_ef = array();		//	interno | array |	Lista	completa	de	a los	EF
	var $lista_ef_post = array();	//	interno | array |	Lista	de	elementos que se reciben por POST
	var $lista_ef_dao = array();
	var $lista_ef_ocultos = array();
	var $nombre_ef_cli = array(); // interno | array | ID html de los elementos
	var $parametros;
	var $modelo_eventos;
	var $flag_out = false;			//indica si el formulario genero output
	var $evento_mod_estricto;		// Solo dispara la modificacion si se apreto el boton procesar
	var $rango_tabs;				//Rango de números disponibles para asignar al taborder
	var $objeto_js;	
	
	function __construct($id)
/*
	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::objeto($id);
		//Elementos basicos del formulario
		$this->etapa = "agregar";
		$this->submit = "ei_form".$this->id[1];
  		$this->cargar_memoria(); 			//Cargo la MEMORIA sincronizada
		//Nombre de los botones de javascript
		$this->js_eliminar = "eliminar_ei_{$this->id[1]}";
		$this->js_agregar = "agregar_ei_{$this->id[1]}";
		$this->evento_mod_estricto = true;
		$this->objeto_js = "objeto_{$id[1]}";		
		$this->rango_tabs = manejador_tabs::instancia()->reservar(50);		
	}
	//-------------------------------------------------------------------------------

	function destruir()
	{
		parent::destruir();
		$this->memorizar();
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//Formulario
		$sql["info_formulario"]["sql"] = "SELECT	auto_reset as	auto_reset,						
										ancho 						as		ancho,
										ev_agregar					as 	ev_agregar,				
										ev_agregar_etiq			as 	ev_agregar_etiq,
										ev_mod_modificar			as 	ev_mod_modificar,		
										ev_mod_modificar_etiq	as 	ev_mod_modificar_etiq,
										ev_mod_eliminar         as 	ev_mod_eliminar,
										ev_mod_eliminar_etiq		as 	ev_mod_eliminar_etiq,
										ev_mod_limpiar	        	as 	ev_mod_limpiar,
										campo_bl						as		campo_bl,
										ev_mod_limpiar_etiq		as 	ev_mod_limpiar_etiq
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND		objeto_ut_formulario='".$this->id[1]."';";
		$sql["info_formulario"]["tipo"]="1";
		$sql["info_formulario"]["estricto"]="1";
		//EF
		$sql["info_formulario_ef"]["sql"] = "SELECT	identificador as identificador,
										columnas						as		columnas,
										obligatorio					as		obligatorio,
										elemento_formulario 		as		elemento_formulario,
										inicializacion				as		inicializacion,
										etiqueta						as		etiqueta,
										descripcion					as		descripcion,
										clave_primaria				as		clave_primaria,
										orden							as		orden
								FROM	apex_objeto_ut_formulario_ef
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND	objeto_ut_formulario='".$this->id[1]."'
								AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql["info_formulario_ef"]["tipo"]="x";
		$sql["info_formulario_ef"]["estricto"]="1";
		return $sql;
	}
//--------------------------------------------------------------------------------------------

	function inicializar($parametros)
/*
	@@acceso: objeto
	@@desc: Dispara la creacion de los elementos	de	formulario (EF)
*/
	{
		$this->parametros = $parametros;
		$this->nombre_formulario =	$parametros["nombre_formulario"];
		$this->prefijo = $this->nombre_formulario . "_" . $this->id[1];
		//Creo el array de objetos EF (Elementos de Formulario) que conforman	el	ABM
		$this->crear_elementos_formulario();
		//Cargo IDs en el CLIENTE
		foreach ($this->lista_ef_post	as	$ef){
			$this->nombre_ef_cli[$ef] = $this->elemento_formulario[$ef]->obtener_id_form();
		}
		//Registar dependencias (SLAVE) en los MASTER
		$this->registrar_dependencias();
		//Inicializacion de especifica de cada tipo de formulario
		$this->inicializar_especifico();
	}
	//-------------------------------------------------------------------------------
	
	function crear_elementos_formulario()
/*
	@@acceso: interno
	@@desc: Genera	el	array	de	objetos EF que	constituye la columna vertebral del	ABM
*/
	{
		for($a=0;$a<count($this->info_formulario_ef);$a++)
		{
			//-[1]- Armo las listas	que determinan	el	plan de accion	del ABM
			$this->lista_ef[]	= $this->info_formulario_ef[$a]["identificador"];
			switch ($this->info_formulario_ef[$a]["elemento_formulario"]) {
				case	"ef_oculto":
				case	"ef_oculto_secuencia":
				case	"ef_oculto_proyecto":
				case	"ef_oculto_usuario":
					$this->lista_ef_ocultos[] = $this->info_formulario_ef[$a]["identificador"];
					break;
				case "ef_combo_dao":
					  $this->lista_ef_post[] =	$this->info_formulario_ef[$a]["identificador"];
					  $this->lista_ef_dao[] =	$this->info_formulario_ef[$a]["identificador"];
					break;
				 default:
					  $this->lista_ef_post[] =	$this->info_formulario_ef[$a]["identificador"];
			}
			$parametros	= parsear_propiedades($this->info_formulario_ef[$a]["inicializacion"]);
			if(isset($parametros["sql"]) && !isset($parametros["fuente"])){
				$parametros["fuente"]=$this->info["fuente"];
			}

			//Preparo el identificador	del dato	que maneja el EF.
			//Esta parametro puede ser	un	ARRAY	o un string: exiten EF complejos	que manejan	mas de una
			//Columna de la tabla a	la	que esta	asociada	el	ABM
			if(ereg(",",$this->info_formulario_ef[$a]["columnas"])){
				 $dato =	explode(",",$this->info_formulario_ef[$a]["columnas"]);
				for($d=0;$d<count($dato);$d++){//Elimino espacios en las	claves
					$dato[$d]=trim($dato[$d]);
				}
			}else{
				 $dato =	$this->info_formulario_ef[$a]["columnas"];
			}
			//Nombre	del formulario.
			$sentencia_creacion_ef = "\$this->elemento_formulario['".$this->info_formulario_ef[$a]["identificador"]."']	=&	new ".
														$this->info_formulario_ef[$a]["elemento_formulario"] ."(	\$this->id,	".
														"'" .	$this->nombre_formulario ."',	'". 
														$this->info_formulario_ef[$a]["identificador"] ."', '". 
														$this->info_formulario_ef[$a]["etiqueta"]	."', '".	
														$this->info_formulario_ef[$a]["descripcion"]	."', ". 
														"\$dato,	'". 
														$this->info_formulario_ef[$a]["obligatorio"]	."', ".
														"\$parametros);";
			//echo $sentencia_creacion_ef	. "<br>";
			eval($sentencia_creacion_ef);
		}	
	}
	//-------------------------------------------------------------------------------

	function inicializar_especifico()
	{
		//---------- Nombre de botones ---------------------
		//Agregar
		if($this->info_formulario['ev_agregar_etiq']){
			$this->submit_agregar = $this->info_formulario['ev_agregar_etiq'];
		}else{
			$this->submit_agregar = "&Agregar";
		}
		//Modificar
		if($this->info_formulario['ev_mod_modificar_etiq']){
			$this->submit_modificar = $this->info_formulario['ev_mod_modificar_etiq'];
		}else{
			$this->submit_modificar = "&Modificar";
		}
		//Eliminar
		if($this->info_formulario['ev_mod_eliminar_etiq']){
			$this->submit_eliminar = $this->info_formulario['ev_mod_eliminar_etiq'];
		}else{
			$this->submit_eliminar = "&Eliminar";
		}
		//Limpiar
		if($this->info_formulario['ev_mod_limpiar_etiq']){
			$this->submit_limpiar = $this->info_formulario['ev_mod_limpiar_etiq'];
		}else{
			$this->submit_limpiar = "&Limpiar";
		}

		//Defino del modo de manejo de EVENTOS
		//Opciones: 1) Trabaja con un listado (ML), recibe eventos precisos: MULTI
		//				2) Trabaja solo, envia eventos de modificacion siempre: OMNI
		if($this->info_formulario['ev_agregar']){
			$this->modelo_eventos = "multi";
		}else{
			$this->modelo_eventos = "omni";
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------	INFORMACION	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function info_estado_ef()
/*
	@@acceso: actividad
	@@desc: Muestra el estado de los	EF
*/
	{
		foreach ($this->lista_ef as $ef){
			$temp1[$ef]	= $this->elemento_formulario[$ef]->obtener_estado();
			$temp2[$ef]	= $this->elemento_formulario[$ef]->obtener_dato();
		}
		$temp["DATOS"]=$temp2;
		$temp["ESTADO"]=$temp1;
		ei_arbol($temp,"Estado actual	de	los ELEMENTOS de FORMULARIO");
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	DEPENDENCIAS  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function procesar_dependencias()
	{
		foreach ($this->lista_ef as $ef){
			$dependencias = $this->elemento_formulario[$ef]->obtener_dependencias();
			if(is_array($dependencias)){
				//echo "entre $ef<br>";
				$estado = array();
				foreach( $dependencias as $dep ){
					//echo "entre $dep<br>";
					if(is_object($this->elemento_formulario[$dep])){
						if($temp = $this->elemento_formulario[$dep]->obtener_estado()){
							if($temp != "NULL") $estado[$dep] = $temp;
						}
					}else{
						echo ei_mensaje("La dependencia '$dep' es invalida");
					}
				}
				$this->elemento_formulario[$ef]->cargar_datos_dependencias($estado);
			}
		}
	}
	//-------------------------------------------------------------------------------

	function registrar_dependencias()
	{
		foreach ($this->lista_ef as $ef)
		{
			if($dependencias = $this->elemento_formulario[$ef]->obtener_dependencias())
			{
				foreach( $dependencias as $dep )
				{
					if(is_object($this->elemento_formulario[$dep])){
						//Se le notifican a un maestro sus slaves
						$id_form_dep = $this->elemento_formulario[$ef]->obtener_id_form();
						$this->elemento_formulario[$dep]->registrar_ef_dependiente($ef, $id_form_dep);
						
						//Se le notifican a un slave todos sus maestros
						$id_form_master = $this->elemento_formulario[$dep]->obtener_id_form();
						$this->elemento_formulario[$ef]->registrar_ef_maestro($dep, $id_form_master);
					}else{
						echo ei_mensaje("La dependencia '$dep' es invalida");
					}
				}
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_evento()
	{
		if($this->controlar_agregar())
		{
			return "alta";
		}
		if($this->controlar_eliminar())
		{
			unset($this->memoria['datos']);
			return "baja";
		}
		if($this->controlar_limpiar())
		{
			unset($this->memoria['datos']);
			return "limpiar";
		}
		if($this->controlar_modificacion())
		{
			unset($this->memoria['datos']);
			return "modificacion";
		}
		return null;
	}
	//-------------------------------------------------------------------------------

	function controlar_modificacion()
	{
		if($this->modelo_eventos=="multi")
		{	
			//----> MODO MULTI <------
			//Se apreto el boton?
			if(isset($_POST[$this->submit])){
				if( $_POST[$this->submit]==$this->submit_modificar ){
					 return true;
				}
			}
			//SI la modificacion se mapea en forma estricta, salgo porque no se apreto el boton
			if($this->evento_mod_estricto){
				return false;	
			}
			//Se modificaron datos (y la navegacion se dio por otro boton??)
			if(isset($this->memoria['datos'])){
				if(is_array($this->memoria['datos'])){
					$datos_actuales = $this->obtener_datos();
					foreach($datos_actuales as $clave => $dato){
						//ATENCION: Comportamiento erroneo EF
						if(isset($this->memoria['datos'][$clave])){
							if($this->memoria['datos'][$clave]=="NULL"){
								$this->memoria['datos'][$clave] = null;	
							}
							if( $this->memoria['datos'][$clave] != $dato){
								return true;
							}
						}else{
							if(trim($dato)!="") return true;
						}
					}
					//ei_arbol( $datos_actuales, "INTERFACE" );
					//ei_arbol( $this->memoria["datos"], "MEMORIA" );
				}else{
					return false;
				}
			}
			return false;
		}else{
			//----> MODO OMNI <-------
			if(acceso_post()){
				return true;
			}
		}
	}
	//-------------------------------------------------------------------------------
	
	function controlar_agregar()
	{
		if(isset($_POST[$this->submit])){
			return ( trim($_POST[$this->submit]) == trim($this->submit_agregar) );
		}
		return false;
	}
	//-------------------------------------------------------------------------------
	
	function controlar_eliminar()
	{
		if(isset($_POST[$this->submit])){
			return ($_POST[$this->submit]==$this->submit_eliminar);
		}
		return false;
	}
	//-------------------------------------------------------------------------------
	
	function controlar_limpiar()
	{
		if(isset($_POST[$this->submit])){
			return ($_POST[$this->submit]==$this->submit_limpiar);
		}
		return false;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function recuperar_interaccion()
	{
		if($this->cargar_post()==true){
			//$this->validar_estado();	
			//Se modificaron los datos?
		}else{
			echo ei_mensaje("No se cargo el POST");		
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_post()
/*
	@@acceso: interno
	@@desc: Carga el estado	de	cada EF a partir del	POST!
*/
	{
		$estado = true;
		foreach ($this->lista_ef as $ef){
			$x	= $this->elemento_formulario[$ef]->cargar_estado();
			if	(!$x){
				//$estado = false;
				//echo "ERROR en $ef <br>";
			}
		}
		return $estado;
	}
	//-------------------------------------------------------------------------------

	function validar_estado()
	{
		$status =	true;
		//Valida	el	estado de los ELEMENTOS	de	FORMULARIO
		foreach ($this->lista_ef as $ef)
		{
			//En la refactorizacion de EFs, el EF directamente dispara una excepcion
			$temp = $this->elemento_formulario[$ef]->validar_estado();
			if(!$temp[0]){
				$mensaje = "Error en el elemento de formulario '" . $this->elemento_formulario[$ef]->obtener_etiqueta() ."' - ". $temp[1];
				throw new excepcion_toba($mensaje);
			}
		}
	}
	//-------------------------------------------------------------------------------

	function limpiar_interface()
/*
	@@acceso: actividad
	@@desc: Resetea los elementos	de	formulario
*/
	{
		foreach ($this->lista_ef as $ef) {
			$this->elemento_formulario[$ef]->resetear_estado();
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_estado_ef($array_ef)
/*
	@@acceso: actividad
	@@desc: Esta funcion permite establecer el valor de un elemento del FORMULARIO (Visible u	Oculto)
	@@param:	array	| una	entrada por	EF	(id->valor)	que quiera cargar. los EF compuestos tienen que	recibir como valor un ARRAY con la forma que	espera el EF destino
*/
	{
		if(is_array($array_ef)){
			foreach($array_ef	as	$ef => $valor){
				if(isset($this->elemento_formulario[$ef])){
					$this->elemento_formulario[$ef]->cargar_estado($valor);
				}else{
					$this->registrar_info_proceso("[cargar_estado_ef] No existe	un	elemento	de	formulario identificado	'$ef'","error");
				}
			}
		}else{
			$this->registrar_info_proceso("[cargar_estado_ef] Los	EF	se	cargan a	travez de un array asociativo	(\"clave\"=>\"dato a	cargar\")!","error");
		}
	}
	//-------------------------------------------------------------------------------

	function	ejecutar_metodo_ef($ef,	$metodo, $parametro=null)
/*
	@@acceso: actividad
	@@desc: Esto sirve para	comunicarse	con EF que pueden	cambiar en tiempo	de	ejecucion
	@@desc: EJ:	un	combo	que necesita cambiar	una propiedad del	WHERE	segun	la	solicitud
	@@param:	string |	elemento	de	formulario a llamar
	@@param:	string |	metodo a	llamar en el EF
	@@param:	array	| Argumentos de la funcion
*/
	{
		if(isset($this->elemento_formulario[$ef])){
			return $this->elemento_formulario[$ef]->$metodo($parametro);
		}else{
			echo ei_mensaje("El EF identificado	'$ef'	no	existe.");
		}
	}
	//-------------------------------------------------------------------------------

	function	obtener_nombres_ef()
/*
	@@acceso: actividad
	@@desc: Recupera la lista de nombres de EF
	@@retorno: array | Listado	de	cada elemento de formulario
*/
	{
		foreach ($this->lista_ef_post	as	$ef){
			$nombres_ef[$ef] = $this->elemento_formulario[$ef]->obtener_id_form();		}
		return $nombres_ef;
	}
	//-------------------------------------------------------------------------------

	function	obtener_consumo_dao()
/*
	@@acceso: actividad
	@@desc: Recupera la lista de consumo de DAOs
	@@retorno: array | Asociativo (nombre/dao)
*/
	{
		$dao = null;
		foreach ($this->lista_ef_dao as $ef){
			if($temp = $this->elemento_formulario[$ef]->obtener_dao()){
				$dao[$ef] = $temp;
			}
		}
		return $dao;
	}
	//-------------------------------------------------------------------------------
	
	function deshabilitar_efs($efs)
	//Establece el grupo de EFs especificados como SOLO LECTURA
	{
		foreach ($efs as $ef){
			if(isset($this->elemento_formulario[$ef])){
				$this->elemento_formulario[$ef]->establecer_solo_lectura();						
			}else{
				$log = toba::get_logger();
				$log->error("DESABILITAR EF: el EF '$ef' no existe");
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	obtener_datos()
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario. Genera un array asociativo de una dimension
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef)
		{
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					echo ei_mensaje("obtener_datos: Error de consistencia	interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$registro[$dato[$x]]	= $estado[$dato[$x]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$registro[$dato] = $estado;
			}
		}
		//ATENCION, esta truchada es para evitar el comportamiento de los EF de retornar NULL
		foreach(array_keys($registro) as $columna){
			if($registro[$columna]=="NULL"){
				$registro[$columna]=null;
			}	
		}
		return $registro;
	}
	//-------------------------------------------------------------------------------

	function cargar_datos($datos)
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		if(isset($datos)){
			//ei_arbol($datos,"DATOS para llenar el EI_FORM");
			//Seteo los	EF	con el valor recuperado
			foreach ($this->lista_ef as $ef){	//Tengo que	recorrer	todos	los EF...
				$temp = null;
				$dato = $this->elemento_formulario[$ef]->obtener_dato();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					$temp = array();
					for($x=0;$x<count($dato);$x++){
						if(isset($datos[$dato[$x]])){
							$temp[$dato[$x]]=stripslashes($datos[$dato[$x]]);
						}
					}
					if(count($temp)>0){
						
					}
				}else{					//El EF maneja	un	DATO SIMPLE
					if(isset($datos[$dato])){
						$temp = stripslashes($datos[$dato]);
					}
				}
				if(isset($temp)){
					$this->elemento_formulario[$ef]->cargar_estado($temp);
				}
			}
			//Memorizo que clave cargue de la base
			//guardo los datos en la memoria
			//para compararlos y saber si se modificaron
			$this->memoria["datos"] = $datos;
			$this->etapa = "modificar";
			$this->procesar_dependencias();
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Genero la interface
		if($this->estado_proceso!="INFRACCION")
		{
			echo "\n\n<!-- ***************** Inicio EI FORMULARIO (	".	$this->id[1] ." )	***********	-->\n\n";
			//A los ocultos se les deja incluir javascript
			foreach ($this->lista_ef_ocultos as $ef) {
				echo $this->elemento_formulario[$ef]->obtener_javascript_general();
			}
			echo "<table class='objeto-base'>";
			echo "<tr><td>";
			$this->barra_superior(null, true,"objeto-ei-barra-superior");
			echo "</td></tr>\n";
			echo "<tr><td>";
			$this->generar_formulario();	
			echo "</td></tr>\n";
			echo "</table>\n";
			//Funciones que necesita este form
			echo "\n<!-- ------------ Funciones JAVASCRIPT (". $this->id[1] .")	--------------	-->\n\n";
			$this->obtener_funciones_javascript();	
			echo "\n<!-- ****************** Fin EI FORMULARIO (". $this->id[1] .") ******************** -->\n\n";
			$this->flag_out = true;
		}
	}
	//-------------------------------------------------------------------------------

	function generar_formulario()
/*
	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Genero	la	interface
		if($this->estado_proceso!="INFRACCION")
		{
			echo "<table class='tabla-0'  width='{$this->info_formulario['ancho']}'>";
			foreach ($this->lista_ef_post	as	$ef){
				echo "<tr><td class='abm-fila'>\n";
				$this->elemento_formulario[$ef]->obtener_interface_ei();
				echo "</td></tr>\n";
			}
			echo "<tr><td class='ei-base'>\n";
			$this->obtener_botones();
			echo "</td></tr>\n";
			echo "</table>\n";
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_botones()
/*
 	@@acceso: interno
	@@desc: Genera los botones de ABM
*/
	{
		//----------- Generacion
		echo "<table class='tabla-0' align='center' width='100%'>\n";
		echo "<tr><td align='right'>";
		echo form::hidden($this->submit, '');
		if($this->etapa=="agregar"){
			if($this->info_formulario['ev_agregar']){
				$acceso = tecla_acceso($this->submit_agregar);	
				echo form::submit($this->submit."_agr" ,$acceso[0],"abm-input"," onclick='{$this->objeto_js}.set_evento(\"A\", \"{$this->submit_agregar}\")'", $acceso[1]);
			}
		}elseif($this->etapa=="modificar"){
			if($this->info_formulario['ev_mod_eliminar']){
				$acceso = tecla_acceso($this->submit_eliminar);
				echo form::submit($this->submit."_eli",$acceso[0],"abm-input"," onclick='{$this->objeto_js}.set_evento(\"E\",\"{$this->submit_eliminar}\")'", $acceso[1]);
			}
			if($this->info_formulario['ev_mod_modificar']){
				$acceso = tecla_acceso($this->submit_modificar);
				echo form::submit($this->submit."_mod", $acceso[0],"abm-input", "onclick='{$this->objeto_js}.set_evento(\"M\",\"{$this->submit_modificar}\")'", $acceso[1]);
			}
			if($this->info_formulario['ev_mod_limpiar']){
				$acceso = tecla_acceso($this->submit_limpiar);
				echo form::submit($this->submit."_lim",$acceso[0],"abm-input", "onclick='{$this->objeto_js}.set_evento(\"L\",\"{$this->submit_limpiar}\")'", $acceso[1]);
			}
		}else{
			echo "Atencion: la proxima etapa no se encuentra definida!";
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_funciones_javascript()
	{
		echo js::abrir();
		$this->crear_objeto_js();
		$this->extender_objeto_js();
		$this->iniciar_objeto_js();		
		echo js::cerrar();
	}	
	
	function crear_objeto_js()
	{
		$rango_tabs = "new Array({$this->rango_tabs[0]}, {$this->rango_tabs[1]})";
		$evento_def = ($this->modelo_eventos == 'multi') ? "" : "M";	//Si no hay eventos definidos, siempre es modificación
		echo "var {$this->objeto_js} = new objeto_ei_formulario('{$this->objeto_js}', null, $rango_tabs, '{$this->submit}', '$evento_def');\n";
		foreach ($this->lista_ef_post as $ef){
			echo "{$this->objeto_js}.agregar_ef({$this->elemento_formulario[$ef]->crear_objeto_js()}, '$ef');\n";
		}
	}
	
	function extender_objeto_js()
	{
	}
	
	function iniciar_objeto_js()
	{
		echo "{$this->objeto_js}.iniciar();\n";	
	}

 	//-------------------------------------------------------------------------------

	function consumo_javascript_global()
	{
		//Busco las	dependencias
		$consumo = array('clases/objeto_ei_formulario');
		foreach ($this->lista_ef_post	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->obtener_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo = array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}
	//-------------------------------------------------------------------------------

	
	function obtener_javascript()
/*
	@@acceso: interno
	@@desc: devuelve JAVASCRIPT que se ejecuta en el onSUBMIT del FORMULARIO
*/
	{
		echo "\nif (! {$this->objeto_js}.submit()) return false;";
	}	
}
?>
