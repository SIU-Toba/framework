<?php
require_once("objeto.php");
require_once("nucleo/browser/interface/form.php");
require_once("nucleo/browser/clases/objeto_ei_formulario.php");

class objeto_ci extends objeto
/*
 	@@acceso: nucleo
	@@desc: Descripcion

	ATENCION: Falta el control del estado en el servidor
*/
{
	var $nombre_formulario;			// privado | string | Nombre del <form> del MT
	var $cn;								// Controlador de negocio asociado
	var $debug_eventos = false;	// Modo debug de eventos
	var $ruteo_eventos;				// lugar donde se guardan los eventos ruteados en modo debug
	var $datos_cargados;		
	var $submit;						// Boton de SUBMIT
	var $submit_etiq;					// Etiqueta del boton SUBMIT
	var $cancelar_etiq;
	var $cancelar_operacion;		//Flag de GET que cancela la operacion
	var $dependencias_actual = array();

	
	function objeto_ci($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto($id);
		//Inicializo VARIOS
		$this->nombre_formulario = "CI_" . $this->id[1] ;//Cargo el nombre del <form>
		$this->submit = $this->nombre_formulario . "_submit";
  		$this->cargar_memoria(); 			//Cargo la MEMORIA sincronizada
		$this->flag_no_propagacion = "no_prop" . $this->id[1];
		//Cargo las DEPENDENCIAS
		$this->cargar_info_dependencias();
		if($this->info_ci["debug_eventos"]=="1"){
			$this->debug_eventos = true;
		}else{
			$this->debug_eventos = false;
		}
		//Boton SUBMIT
		if($this->info_ci['ev_procesar_etiq']){
			$this->submit_etiq = $this->info_ci['ev_procesar_etiq'];
		}else{
			$this->submit_etiq = "Procesar";
		}
		//Boton CANCELAR
		if($this->info_ci['ev_cancelar_etiq']){
			$this->cancelar_etiq = $this->info_ci['ev_cancelar_etiq'];
		}else{
			$this->cancelar_etiq = "cancelar";
		}
		$this->cancelar_operacion = "ci_canop";
	}

	function destruir()
	{
		parent::destruir();
		$this->memorizar();					//GUARDO Memoria sincronizada
	}
	
	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//-- CI ----------------------
		$sql["info_ci"]["sql"] = "SELECT		incremental	as	incremental,
												debug_eventos 			as debug_eventos,
												ev_procesar				as	ev_procesar,
												ev_procesar_etiq		as	ev_procesar_etiq,
												ev_cancelar				as	ev_cancelar,
												ev_cancelar_etiq		as	ev_cancelar_etiq,
												objetos					as	objetos,			
												ancho						as	ancho,			
												alto						as	alto			
										FROM	apex_objeto_mt_me
										WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
										AND	objeto_mt_me='".$this->id[1]."';";
		$sql["info_ci"]["tipo"]="1";
		$sql["info_ci"]["estricto"]="1";
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//----------------------------  PROCESO   ----------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function procesar()
	{
		// 1 - Cargo las dependencias
		if(isset($this->info_ci["objetos"])){
			$dependencias = explode(",",$this->info_ci["objetos"]);
			$this->dependencias_actual = array_map("trim",$dependencias);
		}
		$this->cargar_dependencias($this->dependencias_actual);
		// 2 - Busco eventos en los EI
		$this->controlar_eventos($this->dependencias_actual);
		// 3 - Cargo las interfaces de los EI
		$this->cargar_datos_dependencias();
		// 4 - Proceso la operacion
		if($this->controlar_activacion()){ //Procesar el Marco transaccional
			//$this->cargar_dependencias_inactivas();
			$this->procesar_operacion();
		// 5- Cargo los DAOS
		$this->cargar_daos();
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias($dependencias)
	//Hay logica para subir al padre
	{
		//Parametros a los formularios
		$parametro["nombre_formulario"] = $this->nombre_formulario;
		//Cargo dependencias
		foreach($dependencias as $dep){
			//Crear
			$this->cargar_dependencia($dep);		
//			Inicializar
			$this->dependencias[$dep]->inicializar($parametro);
		}
	}
	
	function cargar_daos()
	{
		//Manejo de DAOS
		//Solo para formularios!!
		//Clase que pueden tener DAOS para combos
		//$clases_dao[]="objeto_ei_formulario, objeto_ei_cuadro";
		foreach ($this->dependencias as $dependencia){		
			if( $dao_form = $dependencia->obtener_consumo_dao() ){
				//ei_arbol($dao_form,"DAO");
				//Por cada elemento de formulario que necesita DAOS
				foreach($dao_form as $ef => $dao){
					$sentencia = "\$datos = \$this->cn->{$dao}();";
					//echo $sentencia;
					eval($sentencia);
					//ei_arbol($datos,"DATOS $ef");
					//El cuadro carga sus daos de otra forma
					$dependencia->ejecutar_metodo_ef($ef,"cargar_datos",$datos);
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_datos_dependencias()
	//Carga los datos de las dependencias actuales
	{
		foreach($this->dependencias_actual as $dep)
		{			
			//En el PARAMETRO B de la dependencia se especifica el metodo
			//Del controlador de negocio que carga a la dependencia
			if($info = $this->consultar_info_dependencia($dep,"parametros_b") )
			{
				$temp = explode(",", $info);
				$metodo = trim($temp[0]);
				//Parametros al metodo de carga
				if(isset($temp[1])){
					$parametros = explode("|",$temp[1]);
					$parametros = array_map("trim",$parametros);
				}else{
					$parametros = null;
				}
				//echo "Cargando dependencia : $dep";
				//SI el CN me devuelve un DATO para la dependencia
				if( $dato = $this->cn->$metodo($parametros) ){
					//ei_arbol($dato, $dep);
					$this->dependencias[$dep]->cargar_datos( $dato );
					$param = $parametros;
					$this->log->debug("CI [cargar_dependencia] DEP: $dep, METODO CN: $metodo, PARAMETROS CN: " . $param );
					if($this->debug_eventos){
						$this->datos_cargados[$dep] = $dato;
					}

				}
			}
			//ei_arbol($this->dependencias[$dependencia]->info_estado());
		}			
	}

	//-------------------------------------------------------------------------------

	function controlar_activacion()
/*
 	@@acceso: interno
	@@desc: Determina si se activo este marco transaccional (si el submit se disparo desde el formulario HTML del mismo)
*/
	{
		if(isset($_POST[$this->submit])){
			//Apretaron el SUBMIT de este FORM
			return true;		
		}else{
			//El submit no es de este formulario, la atencion esta en otro lugar...
			return false;	
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------  Relacion con el Controlador de Negocio   ------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function asignar_controlador_negocio( $controlador )
	{
		$this->cn = $controlador;
	}

	//-------------------------------------------------------------------------------

	function procesar_operacion()
	{
		//Se dispara el procesamiento del controlador de negocio
		$this->cn->procesar();
	}
	
	function operacion_cancelada()
	{
		if($this->solicitud->hilo->obtener_parametro($this->cancelar_operacion)){
			return true;
		}
		return false;
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------  PROCESAMIENTO de EVENTOS  ------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
/*
	- ATENCION: el orden de los eventos no es indiferente:
		- ej: 	cuadro -> seleccion registro
				form -> modificar registro
			Si se selecciona un registro distinto, antes de modificar se modifica una incorrecto		
*/
	function controlar_eventos($dependencias)
	//Escanea las dependencias buscando eventos
	{
		foreach($dependencias as $dep)
		{
			//-[1]- Cargo la actividad del usuario
			$this->dependencias[$dep]->recuperar_interaccion();
			//-[2]- Valido el ESTADO
			//$this->dependencias[$dep]->validar_estado();
			//-[3]- Controlo los eventos
			if($evento = $this->dependencias[$dep]->obtener_evento() ){
				$this->procesar_evento($dep, $evento);
			}
			//Se proceso el evento... si es un formulario limpio la interface
			if($this->dependencias[$dep] instanceof objeto_ei_formulario ){
				$this->dependencias[$dep]->limpiar_interface();
			}
		}
	}
	//-------------------------------------------------------------------------------

	function procesar_evento($dep, $evento)
	{
		//Busco un plan de ruteo para el evento reportado
		if($temp = $this->consultar_info_dependencia($dep,"parametros_a") ){
			if($plan_ruteo = parsear_propiedades_array($temp))
			{
				//if($this->debug_eventos) ei_arbol($plan_ruteo, "DEPENDENCIA: $dep EVENTO: $evento");
				if(isset($plan_ruteo[$evento])){
					//FALTAN CONTROLES de SINTAXIS!!
					$metodo_dep = trim($plan_ruteo[$evento][0]);//echo "Mdep: $metodo_dep";
					$metodo_cn = trim($plan_ruteo[$evento][1]);//echo "Mcn: $metodo_cn";	
					//Tomo el resto de los valores como parametros}
					if(count($plan_ruteo[$evento])>2){
						$temp = trim($plan_ruteo[$evento][2]);
						$parametros = explode("|",$temp);
						$parametros = array_map("trim",$parametros);
					}else{
						$parametros = null;
					}
					try
					{
						if($metodo_dep=="null"){
							if($this->debug_eventos){
								$this->ruteo_eventos[$dep]["CN"] = $metodo_cn;
							}
							$this->cn->$metodo_cn($parametros);
						}else{
							$datos_evento = $this->dependencias[$dep]->$metodo_dep();
							$this->cn->$metodo_cn( $datos_evento, $parametros );
							if($this->debug_eventos){
								$this->ruteo_eventos[$dep.":".$evento]["EI"]["metodo"] = $metodo_dep;
								$this->ruteo_eventos[$dep.":".$evento]["EI"]["retorno"] = $datos_evento;
								$this->ruteo_eventos[$dep.":".$evento]["CN"] = $metodo_cn;
							}
						}
					}catch (excepcion_toba $e)
					{
						$this->solicitud->log->debug($e);
					}
				}else{
					//Este evento no esta mapeado
					if($this->debug_eventos){
						$this->ruteo_eventos[$dep] = "El evento no $evento no posee un plan de ruteo";
					}
				}
			}else{
				if($this->debug_eventos){
					$this->ruteo_eventos[$dep] = "Mapeo INVALIDO";
				}
			}
		}else{
			if($this->debug_eventos){
				$this->ruteo_eventos[$dep] = "No hay plan de ruteo para la dependencia (evento: $evento)";
			}
		}
	}
	//-------------------------------------------------------------------------------

	function mostrar_eventos()
	{
		if($this->debug_eventos){
			//EVENTOS
			if(isset($this->ruteo_eventos)){
				ei_arbol($this->ruteo_eventos,"Ruteo de EVENTOS");
			}
			//CARGA de DATOS
			if(isset($this->datos_cargados)){
				ei_arbol($this->datos_cargados,"DATOS cargados en el EI");
			}
		}			
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------  SALIDA --------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------
	//---- HTML ---------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
 	@@acceso: interno
	@@desc: Devuelve la interface del Marco Transaccional
*/
	{
		//-[1]- Muestro la cola de mensajes
		$this->solicitud->cola_mensajes->mostrar();
		
		//-[2]- Genero la SALIDA
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
		echo "\n<!-- ################################## Inicio CI ( ".$this->id[1]." ) ######################## -->\n\n\n\n";

		$this->obtener_javascript_global_consumido($this->dependencias_actual);

		echo "<br>\n";
		$javascript_submit = " onSubmit='return validar_ci_".$this->nombre_formulario."(this)' ";
		echo form::abrir($this->nombre_formulario, $vinculo, $javascript_submit);
		echo "<div align='center'>\n";
		$ancho = isset($this->info_ci["ancho"]) ? $this->info_ci["ancho"] : "500";
		echo "<table width='$ancho' class='objeto-base'>\n";

		//--> Barra SUPERIOR
		echo "<tr><td>";
		$this->barra_superior();
		echo "</td></tr>\n";

		//--> Interface especifica del CI
		echo "<tr><td class='ci-cuerpo'>";
		$this->obtener_interface();
		echo "</td></tr>\n";

		//--> Pie del CI
		echo "<tr><td class='abm-zona-botones'>";
		$this->obtener_pie();
		echo "</td></tr>\n";

		echo "</table>\n";
		echo "</div>\n";
		echo form::cerrar();
		$this->obtener_javascript_validador_form($this->dependencias_actual);
		echo "<br>\n";
		echo "\n<!-- ###################################  Fin CI  ( ".$this->id[1]." ) ######################## -->\n\n";
		$this->mostrar_eventos();
	}
	//-------------------------------------------------------------------------------

	function obtener_interface()
/*
 	@@acceso: interno
	@@desc: Genera la INTERFACE
*/
	{
		$existe_previo = 0;
		echo "<table class='tabla-0'  width='100%'>\n";
		foreach(array_keys($this->dependencias) as $dep){
			if($existe_previo){
				echo "<tr><td class='celda-vacia'><hr></td></tr>\n";
			}
			echo "<tr><td class='celda-vacia'>";
			$this->dependencias[$dep]->obtener_html();	
			echo "</td></tr>\n";
			$existe_previo = 1;
		}
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_pie()
/*
 	@@acceso: interno
	@@desc: Genera los BOTONES del Marco Transaccional
*/
	{
		if($this->info_ci['ev_procesar']){
			echo form::submit($this->submit,$this->submit_etiq,"abm-input");
		}
		if($this->info_ci['ev_cancelar']){
			echo "&nbsp;" . form::button("boton", $this->cancelar_etiq ,"onclick=\"document.location.href='".$this->solicitud->vinculador->generar_solicitud(null,null,array($this->cancelar_operacion=>1),true)."';\"","abm-input");
		}
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_javascript_global_consumido($dependencias)
/*
 	@@acceso: interno
	@@desc: Genera el javascript GLOBAL que se consumen los EF. El javascript GLOBAL esta compuesto
	@@desc: por porciones de codigo reutilizadas entre distintos subelementos.
*/
	{
		$consumo_js = $this->consumo_javascript_global();
		if(is_array($dependencias)){
			foreach($dependencias as $ei){
				//Es un formulario?
				if($this->dependencias[$ei] instanceof objeto_ei_formulario ){
					$temp = $this->dependencias[$ei]->consumo_javascript_global();
					if(isset($temp)) $consumo_js = array_merge($consumo_js, $temp);
				}else{
					//echo "no es un formulario";
				}
			}
		}
		$consumo_js = array_unique($consumo_js);
		//--> Expresion regular que machea NULOS
		if(in_array("ereg_nulo",$consumo_js)){
			echo "\n<script language='javascript'> ereg_nulo = /^\s*$/;</script>\n"; 
		}
		//--> Expresion regular que machea NUMEROS
		if(in_array("ereg_numero",$consumo_js)){
			echo "\n<script language='javascript'> ereg_numero = /^[1234567890,.-]*$/;</script>\n"; 
		}
		//--> Codigo necesario para los ef_fecha
		if(in_array("fecha",$consumo_js)){
			echo "\n\n<SCRIPT language='javascript' src='".recurso::js("calendario_es.js")."'></SCRIPT>\n";
			echo "<SCRIPT language='javascript'>document.write(getCalendarStyles());</SCRIPT>\n";
			echo "<SCRIPT language='javascript'>var calendario = new CalendarPopup('div_calendario');calendario.showYearNavigation();calendario.showYearNavigationInput();</SCRIPT>\n";
            echo  file_get_contents("nucleo/browser/interface/validacion_fecha.inc",true);
			echo "<DIV id='div_calendario'  style='VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: white; layer-background-color: white'></DIV>\n";
		}
		//--> Codigo necesario para el EDITOR HTML embebido
        if (in_array("fck_editor", $consumo_js)){
			echo "\n\n<SCRIPT type='text/javascript' src='".recurso::js("fckeditor/fckeditor.js")."'></SCRIPT>\n";
        }
        //--> Codigo necesario para los EF que necesitan ventanas flotantes
        if (in_array("popup", $consumo_js)){
			echo "\n\n<SCRIPT language='javascript' src='".recurso::js("popup.js")."'></SCRIPT>\n";        
        }
        if (in_array("financiero", $consumo_js)){
			echo "\n\n<SCRIPT language='javascript' src='".recurso::js("financiero.js")."'></SCRIPT>\n";        
        }		
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript_validador_form($dependencias)
/*
 	@@acceso: interno
	@@desc: Javascript asociado al SUBMIT del FROM


	Hay que separar la validacion de cada ei_formulario en una funcion,
	llamadas desde esta???
*/
	{
		//-[2]- Incluyo el JAVASCRIPT de CONTROLA el FORM
		echo "\n<script language='javascript'>\n";
		echo "//----------- Funcion VALIDADORA del FORM ----------\n";
		echo "function validar_ci_{$this->nombre_formulario}(formulario){\n";
//		echo "alert(\"estoy aca!!\");return false;\n";

		//Cargo el JAVASCRIPT de las dependencias
		foreach($dependencias as $ei){
			if($this->dependencias[$ei] instanceof objeto_ei_formulario ){
				echo $this->dependencias[$ei]->obtener_javascript();
			}
		}
		//Control especifico del CI
		$this->obtener_javascript();

		echo "\n\nreturn true;\n";//Todo OK, salgo de la validacion del formulario
		echo "}\n</script>\n\n";
	}

	function consumo_javascript_global()
/*
 	@@acceso: interno
	@@desc: Javascript global requerido por los HIJOS de este CI
*/
	{
		return array();
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript()
	//Javascript que los HIJOS incorporan en la validacion del formulario
	{
	}
	//-------------------------------------------------------------------------------

}
?>
