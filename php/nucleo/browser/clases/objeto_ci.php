<?php
require_once("objeto.php");
require_once("nucleo/browser/interface/form.php");
require_once("nucleo/browser/clases/objeto_ei_formulario.php");
require_once("nucleo/browser/clases/objeto_ei_cuadro.php");
define("apex_ci_evento","evt");
define("apex_ci_separador","__");
/*
	- Forma en que se genera el HTML (algo anda mal con los consumos JS)
	- En general la seccion de generar la interface esta menos depurada que la de procesar eventos
*/
class objeto_ci extends objeto
{
	protected $cn;									// Controlador de negocio asociado
	protected $nombre_formulario;					// privado | string | Nombre del <form> del MT
	protected $submit;								// Boton de SUBMIT
	protected $dependencias_ci_globales = array();	// Lista de todas las dependencias CI instanciadas desde el momento 0
	protected $dependencias_ci = array();			// Lista de dependencias CI utilizadas en el REQUEST
	protected $dependencias_gi;						// Dependencias utilizadas para la generacion de la interface
	protected $eventos;								// Lista de eventos que expone el CI
	protected $evento_actual;						// Evento propio recuperado de la interaccion
	protected $observadores = array();				// Objetos que observan los eventos de este CI
	protected $id_en_padre;							// Id que posee este CI en su padre
	protected $posicion_botonera;					// Posicion de la botonera en la interface
	protected $gi = false;							// Indica si el CI se utiliza para la generacion de interface
	protected $validacion_js;						// Nombre de la funcion que valida el formulario resultante
	
	function __construct($id)
	{
		parent::objeto($id);
		$this->nombre_formulario = "CI_" . $this->id[1] ;//Cargo el nombre del <form>
		$this->submit = "CI_" . $this->id[1] . "_submit";
		$this->recuperar_estado_sesion();		//Cargo la MEMORIA no sincronizada
		$this->cargar_info_dependencias();
		$this->posicion_botonera = "abajo"; //arriba, abajo, ambos
	}

	function destruir()
	{
		if( $this->gi ){
			//Guardo INFO sobre la interface generada
			$this->memoria['dependencias_interface'] = $this->dependencias_gi;
			$this->memoria['eventos'] = array_keys($this->eventos);
		}
		//Matenimiento en memoria de los CIs que no se instancian
		//Armo la lista GLOBAL de dependencias de tipo CI
		if(isset($this->dependencias_ci_globales)){
			$this->dependencias_ci_globales = array_merge($this->dependencias_ci_globales, $this->dependencias_ci);
			foreach($this->dependencias_ci_globales as $dep){
				$this->solicitud->hilo->dato_global_activo($dep);
			}
		}
		parent::destruir();
		$this->guardar_estado_sesion();		//GUARDO Memoria NO sincronizada
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "dependencias_ci_globales";
		return $estado;
	}
	
	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		$sql["info_ci"]["sql"] = "SELECT		incremental	as	incremental,
												ev_procesar				as	ev_procesar,
												ev_procesar_etiq		as	ev_procesar_etiq,
												ev_cancelar				as	ev_cancelar,
												ev_cancelar_etiq		as	ev_cancelar_etiq,
												objetos					as	objetos,
												ancho					as	ancho,			
												alto					as	alto
										FROM	apex_objeto_mt_me
										WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
										AND	objeto_mt_me='".$this->id[1]."';";
		$sql["info_ci"]["tipo"]="1";
		$sql["info_ci"]["estricto"]="1";
		return $sql;
	}

	function inicializar($parametro=null)
	{
		if(isset($parametro)){
			$this->nombre_formulario = $parametro["nombre_formulario"];
			$this->id_en_padre = $parametro['id'];
		}else{
			$this->id_en_padre = "no_aplicable";
		}
		$this->validacion_js = "validar_ci_" . $this->nombre_formulario;
		$this->evt__inicializar();
	}

	function evt__inicializar()
	//Antes que todo
	{
	}

	function asignar_controlador_negocio( $controlador )
	{
		$this->cn = $controlador;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------   PRIMITIVAS   ----------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function inicializar_dependencias( $dependencias )
	//Carga las dependencias y las inicializar
	{
		$this->log->debug( $this->get_txt() . "[ inicializar_dependencias ]\n" . var_export($dependencias, true));
		//Parametros a generales
		$parametro["nombre_formulario"] = $this->nombre_formulario;
		foreach($dependencias as $dep)
		{
			if(isset($this->dependencias[$dep])){
				//La dependencia ya se encuentra cargada
				continue;
			}
			//-[0]- Creo la dependencia
			$this->cargar_dependencia($dep);		
			//-[1]- La inicializo
			$parametro['id'] = $dep;
			//-- Si es un CI --
			if($this->dependencias[$dep] instanceof objeto_ci ){
				//Guardo la clave de memoria de la dependencia para no perder su memoria cuando no se instancie
				$this->dependencias_ci[$dep] = $this->dependencias[$dep]->get_clave_memoria_global();
				$this->dependencias[$dep]->asignar_controlador_negocio( $this->cn );
				$this->dependencias[$dep]->inicializar($parametro);
				$this->dependencias[$dep]->agregar_observador($this);
			}else{
				$this->dependencias[$dep]->inicializar($parametro);
				$this->dependencias[$dep]->agregar_observador($this);
			}
		}
	}

	function get_dependencias_ci()
	//Avisa que dependencias son CI, si hay una regla ad-hoc que define que CIs cargar
	// hay que redeclarar este metodo para que devuelva el conjunto correcto de CIs utilizados
	{
		//ATENCION, esto presupone que la clase cumple con esta regla de nomenclatura
		return $this->get_dependencias_clase("ci_");
	}

	//--------------------------------------------------------
	//---------  Limpieza de MEMORIA -------------------------
	//--------------------------------------------------------
		
	function disparar_limpieza_memoria()
	//Borra la memoria de todos los CI
	{
		$this->log->debug( $this->get_txt() . "[ disparar_limpieza_memoria ]");
		foreach($this->dependencias_ci_globales as $dep => $x){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->dependencias[$dep]->disparar_limpieza_memoria();
		}
		$this->evt__limpieza_memoria();
	}
	
	function evt__limpieza_memoria($no_borrar=null)
	//Borra la memoria de este CI, despues vuelve a inicializar los elementos
	{
		$this->borrar_memoria();
		$this->eliminar_estado_sesion($no_borrar);
		$this->evt__inicializar();
	}

	//--------------------------------------------------------
	//---------  Relacion con el CN --------------------------
	//--------------------------------------------------------

	//--  ENTRADA  ----

	function disparar_obtencion_datos_cn()
	{
		$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ]");
		$this->evt__obtener_datos_cn();
		$deps = $this->get_dependencias_ci();
		foreach( $deps as $dep ){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ] ejecutar '$dep'");
			$this->dependencias[$dep]->disparar_obtencion_datos_cn();
		}
	}

	function evt__obtener_datos_cn()
	{
		//Esta funcion hay que redefinirla en un hijo para OBTENER datos
		$this->log->warning($this->get_txt() . "[ evt__obtener_datos_cn ] No fue redefinido!");
	}

	//--  SALIDA  ----

	function disparar_entrega_datos_cn()
	{
		$this->log->debug( $this->get_txt() . "[ disparar_entrega_datos_cn ]");
		//DUDA: Validar aca es redundante?
		$this->evt__validar_datos();
		$this->evt__entregar_datos_cn();
		$deps = $this->get_dependencias_ci();
		foreach( $deps as $dep ){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->log->debug( $this->get_txt() . "[ disparar_entrega_datos_cn ] ejecutar '$dep'");
			$this->dependencias[$dep]->disparar_entrega_datos_cn();
		}
	}

	function evt__entregar_datos_cn()
	{
		//Esta funcion hay que redefinirla en un hijo para ENTREGAR datos
		$this->log->warning($this->get_txt() . "[ evt__entregar_datos_cn ] No fue redefinido!");
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------   PROCESAMIENTO de EVENTOS   --------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	//----  Codigo MASTER  -----
	
	function procesar_eventos()
	//Gatillo del procesamiento de eventos desde el nivel exterior
	{
		$this->log->debug($this->get_txt() . "_____________________________________________________[ procesar_eventos ]");
		try{
			$this->inicializar();
			$this->disparar_eventos();
		}catch(excepcion_toba $e){
			$this->log->debug($e);			
			$this->informar_msg($e->getMessage(), 'error');
		}
	}

	protected function disparar_eventos()
	// Se les ordena a las dependencias que gatillen sus eventos
	// Cualquier error que aparezca, sea donde sea, se atrapa en el ultimo nivel.
	//  Esto es fuerte porque hace que cuando se detecta el primer error, no se sigan procesando las cosas
	{
		$this->log->debug( $this->get_txt() . "[ disparar_eventos ]");
		$this->controlar_eventos_propios();
		//El evento CANCELAR tiene que controlarse antes
		if( $this->evento_actual == "cancelar"){
			$this->disparar_evento_propio();
		}else{
			//Disparo los eventos de las dependencias
			foreach( $this->get_dependencias_interface_previa() as $dep)
			{
				//El try/catch deberia estar aca?
				$this->dependencias[$dep]->disparar_eventos();
			}
			$this->disparar_evento_propio();
			$this->evt__post_recuperar_interaccion();
		}
	}

	function controlar_eventos_propios()
	//Indica si se ejecuto un evento propio
	{
		$this->evento_actual = "";
		if(isset($_POST[$this->submit])){
			$evento = $_POST[$this->submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if(isset(  $this->memoria['eventos'] )){
				if(in_array( $evento, $this->memoria['eventos'])){
					$this->evento_actual = $evento;
				}	
			}
		}
	}

	function disparar_evento_propio()
	//Dispara un evento propio
	{
		if($this->evento_actual != "")
		{
			$metodo = apex_ci_evento . apex_ci_separador . $this->evento_actual;
			if(method_exists($this, $metodo)){
				//Ejecuto el metodo que implementa al evento
				$this->log->debug( $this->get_txt() . "[ disparar_evento_propio ] '{$this->evento_actual}' -> [ $metodo ]");
				$this->$metodo();
				//Comunico el evento al contenedor
				$this->reportar_evento( $this->evento_actual );
			}else{
				$this->log->warning($this->get_txt() . "[ disparar_evento_propio ]  El METODO [ $metodo ] no existe - '{$this->evento_actual}' no fue atrapado");
			}
		}
	}

	protected function get_dependencias_interface_previa()
	//Devuelve la lista de dependencias que se utlizaron para general la interface anterior
	{
		//Memoria sobre dependencias que fueron a la interface
		if( isset($this->memoria['dependencias_interface']) ){
			$dependencias = $this->memoria['dependencias_interface'];
			//Necesito cargar los daos dinamicos?
			//Esto es posible si los EF chequean que su valor se encuentre entre los posibles
			$this->inicializar_dependencias( $dependencias );
			return $dependencias;
		}else{
			return array();
		}
	}

	public function registrar_evento($id, $evento, $parametros=null)
	//Se disparan eventos dentro del nivel actual
	{
		$metodo = apex_ci_evento . apex_ci_separador . $id . apex_ci_separador . $evento;
		if(method_exists($this, $metodo)){
			$this->log->debug( $this->get_txt() . "[ registrar_evento ] '$evento' -> [ $metodo ]\n" . var_export($parametros, true));
			$this->$metodo( $parametros );
		}else{
			$this->log->warning($this->get_txt() . "[ registrar_evento ]  El METODO [ $metodo ] no existe - '$evento' no fue atrapado");
			//Puede implementarse un metodo generico de manejo de eventos? 
		}
	}

	//---- EVENTOS BASICOS ------

	function evt__cancelar()
	{
		$this->log->debug($this->get_txt() . "[ evt__cancelar ]");
		$this->cn->cancelar();
		$this->disparar_limpieza_memoria();
	}

	function evt__procesar()
	{
		$this->log->debug($this->get_txt() . "[ evt__procesar ]");
		$this->disparar_entrega_datos_cn();
		$this->cn->procesar();
		$this->disparar_limpieza_memoria();
	}
	
	function evt__post_recuperar_interaccion()
	//Despues de recuperar la interaccion con el usuario
	{
		$this->evt__validar_datos();
	}

	function evt__validar_datos()
	//Validar el estado interno, dispara una excepcion si falla
	{
	}

	function evt__error_proceso_hijo( $dependencia )
	//Disparada cuando un hijo falla en su procesamiento
	{
		$this->error_proceso_hijo[] = $dependencia;
	}

	//------  Codigo SLAVE -----

	public function agregar_observador($observador)
	{
		$this->observadores[] = $observador;
	}

	function eliminar_observador($observador){}

	private function reportar_evento($evento, $parametros=null)
	//Registro un evento en todos mis observadores
	{
		foreach(array_keys($this->observadores) as $id){
			$this->observadores[$id]->registrar_evento( $this->id_en_padre, $evento, $parametros );
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------   Generacion de la INTERFACE GRAFICA   ----------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_interface_grafica()
	//Esta funcion dispara la generacion de TODA la interface.
	//Solo es llamado por el CI EXTERIOR. La composicion recursiva es a travez de 'obtener_html'
	{
		$this->log->debug($this->get_txt() . "____________________________________________[ generar_interface_grafica ]");
		try{
			//Cargar todos los EI que componen la interface
			$this->cargar_dependencias_gi();
			$this->obtener_html_base();
		}catch(excepcion_toba $e){
			$this->log->debug($e);
			$this->informar_msg($e->getMessage(), 'error');
			$this->solicitud->cola_mensajes->mostrar();
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias_gi()
	//Cargar las depedencias a utilizar para generar la interface
	{
		$this->log->debug($this->get_txt() . "[ cargar_dependencias_gi ]");
		//Busco la lista de las dependencias que necesito para cargar esta interface
		$this->dependencias_gi = $this->get_lista_ei();
		//Creo las dependencias
		$this->inicializar_dependencias( $this->dependencias_gi );
		$this->evt__pre_cargar_datos_dependencias();
		$this->cargar_datos_dependencias();
	}
	//-------------------------------------------------------------------------------

	function get_lista_ei()
	{
		if( trim($this->info_ci["objetos"]) != "" )
		{
			//Se escribio una lista de objetos a cargar
			$dependencias = explode(",",$this->info_ci["objetos"]);
			return array_map("trim", $dependencias);
		}else{
			return array();
		}
	}
	//-------------------------------------------------------------------------------

	function evt__pre_cargar_datos_dependencias()
	//Antes de cargar las dependencias
	{
	}
	//-------------------------------------------------------------------------------

	function cargar_datos_dependencias()
	{
		//Disparo la carga de dependencias en los CI que me componen
		foreach($this->dependencias_gi as $dep)
		{
			if(	$this->dependencias[$dep] instanceof objeto_ci ){
				//CI
				//	Hago que cargue sus dependencias
				$this->dependencias[$dep]->cargar_dependencias_gi();
			}else{
				//EI
				if( $this->dependencias[$dep] instanceof objeto_ei_formulario ){
					//-- EI_FORM --
					//	Un EF-COMBO puede solicitar la carga al CI que los contiene si sus valores no son estaticos
					if( $dao_form = $this->dependencias[$dep]->obtener_consumo_dao() ){
						//ei_arbol($dao_form,"DAO");
						//Por cada elemento de formulario que necesita DAOS
						foreach($dao_form as $ef => $dao){
							$datos = $this->$dao();
							//ei_arbol($datos,"DATOS $ef");
							$this->dependencias[$dep]->ejecutar_metodo_ef($ef,"cargar_datos",$datos);
						}
					}
				}
				//-- Inyecto DATOS en los EIs, si es que existe un metodo para cargarlos --
				$metodo = apex_ci_evento . apex_ci_separador . $dep . apex_ci_separador . "carga";
				if(method_exists($this, $metodo)){
					$this->dependencias[$dep]->cargar_datos( $this->$metodo() );
					$this->log->debug($this->get_txt() . "[ cargar_datos_dependencia ] '$dep' -> [ $metodo ] ");
				}else{
					$this->log->warning($this->get_txt() . "[ cargar_datos_dependencia ] El METODO [ $metodo ] no existe - '$dep' no fue cargada");
					//Puede implementarse un metodo generico de manejo de eventos? 
				}
			}
		}
	}	
	//-------------------------------------------------------------------------------

	function obtener_html_base()
	{
		$this->get_info_post_proceso();
		//-[1]- Muestro la cola de mensajes
		$this->solicitud->cola_mensajes->mostrar();
		//-[2]- Genero la SALIDA
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
		echo "\n<!-- ################################## Inicio CI ( ".$this->id[1]." ) ######################## -->\n\n\n\n";
		$this->obtener_javascript_global_consumido();
		echo "<br>\n";
		$javascript_submit = " onSubmit='return ".$this->validacion_js."(this)' ";
		echo form::abrir($this->nombre_formulario, $vinculo, $javascript_submit);
		echo "<div align='center'>\n";
		$this->obtener_html();
		echo "</div>\n";
		echo form::cerrar();
		$this->obtener_javascript_validador_form();
		echo "<br>\n";
		echo "\n<!-- ###################################  Fin CI  ( ".$this->id[1]." ) ######################## -->\n\n";		
	}
	//-------------------------------------------------------------------------------

	function get_info_post_proceso()
	{
		return "";		
	}
	//-------------------------------------------------------------------------------

	function obtener_html()
	{
		$this->eventos = $this->get_lista_eventos();
		$ancho = isset($this->info_ci["ancho"]) ? "width='" . $this->info_ci["ancho"] . "'" : "";
		$alto = isset($this->info_ci["alto"]) ? "height='" . $this->info_ci["alto"] . "'" : "";
		echo "<table $ancho $alto class='objeto-base'>\n";
		//--> Barra SUPERIOR
		echo "<tr><td class='celda-vacia'>";
		$this->barra_superior(null,true,"objeto-ci-barra-superior");
		echo "</td></tr>\n";

		//--> Botonera
		if( count($this->eventos) > 0){
			if( ($this->posicion_botonera == "arriba") || ($this->posicion_botonera == "ambos") ){
				echo "<tr><td class='abm-zona-botones'>";
				$this->generar_botonera();
				echo "</td></tr>\n";
			}
		}
		//--> Cuerpo del CI
		echo "<tr><td  class='ci-cuerpo' height='100%'>";
		$this->obtener_html_contenido();
		echo "</td></tr>\n";

		//--> Botonera
		if( count($this->eventos) > 0){
			if( ($this->posicion_botonera == "abajo") || ($this->posicion_botonera == "ambos") ){
				echo "<tr><td class='abm-zona-botones'>";
				$this->generar_botonera();
				echo "</td></tr>\n";
			}
		}

		echo "</table>\n";
		$this->gi = true;
	}
	//-------------------------------------------------------------------------------

	function generar_botonera()
	{
		//-[ 0 ]- Javascript que setea el evento y hace el submit del FORM

		$funcion = "set_evento_" . $this->submit;
		echo form::hidden($this->submit, '');		
		echo js::abrir();
		echo "	function $funcion(evento, confirmacion, validar){
		if ( confirmacion != ''){
			if (!confirm( confirmacion )) {
				return false;
			}
		}
		document.{$this->nombre_formulario}.{$this->submit}.value = evento;
		if( validar ){
			if( {$this->validacion_js}( document.{$this->nombre_formulario} ) ){
				document.{$this->nombre_formulario}.submit();
			}
		}else{
			document.{$this->nombre_formulario}.submit();
		}
		//return true;
	}";
		echo js::cerrar();

		//-[ 1 ]- Botonera propiamente dicha

		echo "<table class='tabla-0' align='center' width='100%'>\n";
		echo "<tr><td align='right'>";
		foreach($this->eventos as $id => $evento )
		{
			$tip = '';
			$clase = 'ef-boton';
			$tab_order = 0;
			$acceso = tecla_acceso( $evento["etiqueta"] );
			$html = $acceso[0]; //Falta concatenar la imagen
			$tecla = $acceso[1];
			$js_confirm = isset( $evento['confirmacion'] ) ? "'{$evento['confirmacion']}'" : "''";
			$js_validar = isset( $evento['validar'] ) ? "{$evento['validar']}" : "true";
			$js = "onclick=\"$funcion('$id',$js_confirm, $js_validar )\"";
			echo "&nbsp;" . form::button_html( $this->submit.$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase);
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function get_lista_eventos()
	{
		$eventos = array();
		//Evento PROCESAR
		if($this->info_ci['ev_procesar'])
		{
			$eventos = array_merge($eventos,  $this->get_boton_procesar($this->info_ci['ev_procesar_etiq']) );		
		}
		//Evento CANCELAR
		if($this->info_ci['ev_cancelar'])
		{
			$eventos = array_merge($eventos,  $this->get_boton_cancelar($this->info_ci['ev_cancelar_etiq']) );		
		}
		return $eventos;
	}

	function get_boton_cancelar($etiqueta=null)
	{
		$evento['cancelar']['etiqueta'] = isset($etiqueta) ? $etiqueta : "&Cancelar";
		$evento['cancelar']['validar'] = "false";
		//$evento['cancelar']['confirmacion'] = "¿Esta seguro que desea cancelar?";
		return $evento;
	}

	function get_boton_procesar($etiqueta=null)
	{
		$evento['procesar']['etiqueta'] = isset($etiqueta) ? $etiqueta : "Proce&sar";
		return $evento;
	}
	//-------------------------------------------------------------------------------

	protected function obtener_html_contenido()
	//Genera el HTML de las dependencias
	{
		$existe_previo = 0;
		foreach($this->dependencias_gi as $dep)
		{
			if($existe_previo){ //Separador
				echo "<hr>\n";
			}
			//echo "<tr><td class='celda-vacia'>";
			$this->dependencias[$dep]->obtener_html();	
			//echo "</td></tr>\n";
			$existe_previo = 1;
		}
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_javascript_global_consumido()
/*
 	@@acceso: interno
	@@desc: Genera el javascript GLOBAL que se consumen los EF. El javascript GLOBAL esta compuesto
	@@desc: por porciones de codigo reutilizadas entre distintos subelementos.
*/
	{
		js::cargar_consumos_globales($this->consumo_javascript_global());
	}
	//-------------------------------------------------------------------------------

	function consumo_javascript_global()
/*
 	@@acceso: interno
	@@desc: Javascript global requerido por los HIJOS de este CI
*/
	{
		$consumo_js = array();
		foreach($this->dependencias_gi as $dep){
			//Es un formulario?
			if(	$this->dependencias[$dep] instanceof objeto_ei_formulario ||
				$this->dependencias[$dep] instanceof objeto_ci )
			{
				$temp = $this->dependencias[$dep]->consumo_javascript_global();
				if(isset($temp)) $consumo_js = array_merge($consumo_js, $temp);
			}else{
					//echo "no es un formulario";
			}
		}
		return $consumo_js;
	}
	//-------------------------------------------------------------------------------
	
	function obtener_javascript_validador_form()
/*
 	@@acceso: interno
	@@desc: Javascript asociado al SUBMIT del FROM
*/
	{
		//-[2]- Incluyo el JAVASCRIPT de CONTROLA el FORM
		echo "\n<script language='javascript'>\n";
		echo "//----------- Funcion VALIDADORA del FORM ----------\n";
		echo "function {$this->validacion_js}(formulario){\n";
//		echo "alert(\"estoy aca!!\");return false;\n";
		$this->obtener_javascript();
		echo "\n\nreturn true;\n";//Todo OK, salgo de la validacion del formulario
		echo "}\n</script>\n\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript()
	//Javascript que los HIJOS incorporan en la validacion del formulario
	{
		foreach($this->dependencias_gi as $dep)
		{
			if(	$this->dependencias[$dep] instanceof objeto_ei_formulario ||
				$this->dependencias[$dep] instanceof objeto_ci )
			{
				echo $this->dependencias[$dep]->obtener_javascript();
			}
		}
	}
	//-------------------------------------------------------------------------------
}

/*
		//-[0]- Si el CN tiene 
		if( $this->cn->get_estado_proceso() ){
			if(trim($this->info_ci['post_procesar'])!="")
			{
				$metodo = $this->info_ci['post_procesar'];
				if( defined("apex_pa_ci_mensaje") )
				{
					if( apex_pa_ci_mensaje == "pantalla")
					{
						//Pantalla de POST procesamiento. Si las cosas no salieron ok, no se llega aca
						echo "<br>\n";
						echo "<div align='center'>\n";
						$ancho = isset($this->info_ci["ancho"]) ? $this->info_ci["ancho"] : "500";
						$alto = isset($this->info_ci["alto"]) ? "height='" . $this->info_ci["alto"] . "'" : "";
						echo "<table width='$ancho' $alto class='objeto-base'>\n";
						echo "<tr><td>";
						$this->barra_superior();
						echo "</td></tr>\n";
						echo "<tr><td class='ci-cuerpo' height='100%'>";
						$metodo = $this->info_ci['post_procesar'];
						echo ei_mensaje( $this->cn->$metodo() );
						echo "</td></tr>\n";
						echo "<tr><td class='abm-zona-botones'>";
						echo form::button("boton", "Volver a comenzar" ,"onclick=\"document.location.href='".$this->solicitud->vinculador->generar_solicitud(null,null,null,true)."';\"","abm-input");
						echo "</td></tr>\n";		
						echo "</table>\n";
						echo "</div>\n";		
						return;					
					}
					elseif(apex_pa_ci_mensaje == "mensaje")
					{
						$this->informar_msg( $this->cn->$metodo() );
					}
				}
			}
		}
*/

?>
