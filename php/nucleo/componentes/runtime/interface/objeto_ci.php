<?php
require_once("objeto_ei.php");
require_once("nucleo/browser/interface/form.php");
require_once("nucleo/browser/clases/objeto_ei_formulario.php");
require_once("nucleo/browser/clases/objeto_ei_cuadro.php");
require_once('nucleo/lib/parser_ayuda.php');

/**
 * Controla un flujo de pantallas
 * @package Objetos
 * @subpackage Ei
 */
class objeto_ci extends objeto_ei
{
	// General
	protected $cn=null;								// Controlador de negocio asociado
	protected $nombre_formulario;					// Nombre del <form> del MT
	protected $submit;								// Boton de SUBMIT
	protected $dependencias_ci_globales = array();	// Lista de todas las dependencias CI instanciadas desde el momento 0
	protected $dependencias_ci = array();			// Lista de dependencias CI utilizadas en el REQUEST
	protected $dependencias_gi = array();						// Dependencias utilizadas para la generacion de la interface
	protected $eventos;								// Lista de eventos que expone el CI
	protected $evento_actual;						// Evento propio recuperado de la interaccion
	protected $evento_actual_param;					// Parametros del evento actual
	protected $id_en_padre;							// Id que posee este CI en su padre
	protected $posicion_botonera;					// Posicion de la botonera en la interface
	protected $gi = false;							// Indica si el CI se utiliza para la generacion de interface
	protected $objeto_js;							// Nombre del objeto js asociado
	// Pantalla
	protected $indice_etapas;
	protected $etapa_gi;			// Etapa a utilizar para generar la interface
	// Navegacion
	protected $lista_tabs;

	function __construct($id)
	{
		parent::__construct($id);
		$this->nombre_formulario = "formulario_toba" ;//Cargo el nombre del <form>
		$this->submit = "CI_" . $this->id[1] . "_submit";
		$this->recuperar_estado_sesion();		//Cargo la MEMORIA no sincronizada
		$this->cargar_info_dependencias();
		$this->posicion_botonera = ($this->info_ci['posicion_botonera'] != '') ? $this->info_ci['posicion_botonera'] : 'abajo';
		$this->objeto_js = "objeto_ci_{$this->id[1]}";		
		//-- PANTALLAS
		//Indice de etapas
		for($a = 0; $a<count($this->info_ci_me_pantalla);$a++){
			$this->indice_etapas[ $this->info_ci_me_pantalla[$a]["identificador"] ] = $a;
		}
		//Lo que sigue solo sirve para el request inicial, en los demas casos es rescrito
		// por "definir_etapa_gi_pre_eventos" o "definir_etapa_gi_post_eventos"
		$this->set_etapa_gi( $this->get_etapa_actual() );

	}

	function destruir()
	{
		if( $this->gi ){
			//Guardo INFO sobre la interface generada
			$this->memoria['dependencias_interface'] = $this->dependencias_gi;
			//Etapa utilizada para crear la interface
			$this->memoria['etapa_gi'] = $this->etapa_gi;
		}
		//Memorizo la lista de tabs enviados
		if( isset($this->lista_tabs) ){
			$this->memoria['tabs'] = array_keys($this->lista_tabs);
		}
		//Armo la lista GLOBAL de dependencias de tipo CI
		if(isset($this->dependencias_ci_globales)){
			$this->dependencias_ci_globales = array_merge($this->dependencias_ci_globales, $this->dependencias_ci);
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
	
	function inicializar($parametro=null)
	{
		if(isset($parametro)){
			$this->nombre_formulario = $parametro["nombre_formulario"];
			$this->id_en_padre = $parametro['id'];
		}else{
			$this->id_en_padre = "no_aplicable";
		}
		$this->evt__inicializar();
	}

	function evt__inicializar()
	//Antes que todo
	{
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------   PRIMITIVAS   ----------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function inicializar_dependencias( $dependencias )
	//Carga las dependencias y las inicializar
	{
		asercion::es_array($dependencias,"No hay dependencias definidas");
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
			$this->inicializar_dependencia($dep, $parametro);
		}
	}

	function inicializar_dependencia($dep, $parametro)
	{
		if ($this->dependencias[$dep] instanceof objeto_ci ){
			$this->dependencias_ci[$dep] = $this->dependencias[$dep]->get_clave_memoria_global();			
			if(isset($this->cn)){
				$this->dependencias[$dep]->asignar_controlador_negocio( $this->cn );
			}
		}
		$this->dependencias[$dep]->agregar_controlador($this); //Se hace antes para que puede acceder a su padre
		$this->dependencias[$dep]->inicializar($parametro);
	}

	/**
	 * Accede a una dependencia del objeto, opcionalmente si la dependencia no esta cargada, la carga
	 *	si la dependencia es un EI y no figura en la lista GI (generacion de interface) dispara el eventos de carga!
	 * @param string $id Identificador de la dependencia dentro del objeto actual
	 * @param boolean $cargar_en_demanda En caso de que el objeto no se encuentre cargado en memoria, lo carga
	 * @return Objeto
	 */
	function dependencia($id, $carga_en_demanda = true)
	{
		$dependencia = parent::dependencia( $id, $carga_en_demanda );
		if( $dependencia instanceof objeto_ei ) {
			// EIs que no estan en la lista GI: hay que cargar su estado y DAOS
			if ( ! in_array( $id, $this->dependencias_gi ) ) {
				$parametro['id'] = $id;
				$parametro['nombre_formulario'] = $this->nombre_formulario;
				$this->inicializar_dependencia( $id, $parametro );
				if( $dependencia instanceof objeto_ei_formulario ) {
					// Carga de combos dinamicos de formularios
					$this->cargar_daos_dinamicos_dependencia( $id );
				}			
				$dependencia->cargar_datos( $this->proveer_datos_dependencia( $id ) );
			}
		}
		return $dependencia;
	}

	//--------------------------------------------------------------
	//------  Interaccion con un CONTROLADOR de NEGOCIO ------------
	//--------------------------------------------------------------

	function asignar_controlador_negocio( $controlador )
	{
		$this->cn = $controlador;
	}

	//--  ENTRADA de DATOS ----

	function disparar_obtencion_datos_cn( $modo=null )
	{
		$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ]");
		$this->evt__obtener_datos_cn( $modo );
		$deps = $this->get_dependencias_ci();
		foreach( $deps as $dep ){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ] ejecutar '$dep'");
			$this->dependencias[$dep]->disparar_obtencion_datos_cn( $modo );
		}
	}

	function evt__obtener_datos_cn( $modo=null )
	{
		//Esta funcion hay que redefinirla en un hijo para OBTENER datos
		$this->log->warning($this->get_txt() . "[ evt__obtener_datos_cn ] No fue redefinido!");
	}

	//--  SALIDA de DATOS ----

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

	function get_dependencias_ci()
	// Avisa que dependencias son CI, si hay una regla ad-hoc que define que CIs cargar
	// (osea: si se utilizo el metodo 'get_lista_ei' para dicidir cual de dos dependencias de tipo CI cargar)
	// hay que redeclarar este metodo con la misma regla utilizada en 
	// por la operacion
	{
		return $this->get_dependencias_clase('objeto_ci');
	}
	
	//--------------------------------------------------------------
	//---------  Limpieza de MEMORIA -------------------------------
	//--------------------------------------------------------------
		
	function disparar_limpieza_memoria()
	//Borra la memoria de todos los CI
	{
		$this->log->debug( $this->get_txt() . "[ disparar_limpieza_memoria ]");
		foreach($this->get_dependencias_ci() as $dep){
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
		$this->set_etapa_gi( $this->get_etapa_inicial() );
		$this->borrar_memoria();
		$this->eliminar_estado_sesion($no_borrar);
		$this->evt__inicializar();
	}

	//--------------------------------------------------------
	//--  MANEJO de PANTALLAS  -------------------------------
	//--------------------------------------------------------

	protected function get_pantalla_inicial()
	{
		return $this->info_ci_me_pantalla[0]["identificador"];
	}
	
	/**
	*	@deprecated Desde 0.8.3
	*	@see objeto_ci::get_pantalla_inicial()
	*/	
	protected function get_etapa_inicial()
	{
		return $this->get_pantalla_inicial();
	}

	/**
	 * Determina que pantalla se muestra en este request
	 * Redefinir en caso de incluir una navegación personalizada
	 * @return Id. de la pantalla actual
	 */
	function get_pantalla_actual()
	{
		//¿Se pidio un cambio de pantalla al CI? 
		if (isset($_POST[$this->submit])) {
			$submit = $_POST[$this->submit];
			//Se pidio explicitamente un id de pantalla o navegar atras-adelante?
			$tab = (strpos($submit, 'cambiar_tab_') !== false) ? str_replace('cambiar_tab_', '', $submit) : false;
			if ($tab == '_siguiente' || $tab == '_anterior') {
				return $this->ir_a_limitrofe($tab);
			} 
			if ($tab !== false && $this->puede_ir_a_pantalla($tab)) {
				if(in_array($tab, $this->memoria['tabs'])){
					return $tab;
				}else{
					$this->log->error($this->get_txt() . "Se solicito un TAB inexistente.");
					//Error, voy a etapa inicial
					return $this->get_etapa_inicial();
				}
			}
		}
		
		//El post fue generado por otro componente ??
		if(isset( $this->memoria['etapa_gi'] )){
			return $this->memoria['etapa_gi'];
		}else{
			//Pantalla inicial
			return $this->get_etapa_inicial();
		}
	}
	
	/**
	*	@deprecated Desde 0.8.3
	*	@see objeto_ci::get_pantalla_actual()
	*/
	protected function get_etapa_actual()
	{
		return $this->get_pantalla_actual();
	}
		
	/**
	 * Busca alguna regla particular para determinar si la navegación hacia una pantalla es válida
	 * El método a definir para incidir en esta regla es evt__puede_mostrar_pantalla y recibe la pantalla como parámetro
	 * @return boolean
	 */
	protected function puede_ir_a_pantalla($tab)
	{
		$evento_mostrar = apex_ei_evento . apex_ei_separador . "puede_mostrar_pantalla";
		if(method_exists($this, $evento_mostrar)){
			return $this->$evento_mostrar($tab);
		}
		return true;
	}
	
	/**
	 * Recorre las pantallas en un sentido buscando una válida para mostrar
	 * @param string $sentido "_anterior" o "_siguiente"
	 */
	protected function ir_a_limitrofe($sentido)
	{
		$indice = ($sentido == '_anterior') ? 0 : 1;	//Para generalizar la busquda de siguiente o anterior
		$candidato = $this->memoria['etapa_gi'];
		while ($candidato !== false) {
			$limitrofes = $this->pantallas_limitrofes($candidato);
			$candidato = $limitrofes[$indice];
			if ($this->puede_ir_a_pantalla($candidato))
				return $candidato;
		}
		//Si no se encuentra ninguno, no se cambia
		return $this->memoria['etapa_gi'];
	}
	
	
	//-------------------------------------------------------------------------------
	/**
	 * Determina la etapa anterior y siguiente a la dada 
	 */
	function pantallas_limitrofes($actual)
	{
		$this->lista_tabs = $this->get_lista_tabs();
		reset($this->lista_tabs);
		$pantalla = current($this->lista_tabs);
		$anterior = false;
		$siguiente = false;
		while ($pantalla !== false) {
			if (key($this->lista_tabs) == $actual) {  //Es la etapa actual?
				if (next($this->lista_tabs) !== false)
					$siguiente = key($this->lista_tabs);
				else
					$siguiente = false;
				break;
			}
			$anterior = key($this->lista_tabs);
			$pantalla = next($this->lista_tabs);
		}
		return array($anterior, $siguiente);	
	}	

	//-------------------------------------------------------------------------------	
	protected function set_etapa_gi($etapa)
	{
		$this->etapa_gi	= $etapa;
	}

	protected function get_etapa_gi()
	{
		return $this->etapa_gi;	
	}

	/**
	 * Define la etapa de Generacion de Interface del request ANTERIOR
	 */
	function definir_etapa_gi_pre_eventos()
	{
		$this->log->debug( $this->get_txt() . "[ definir_etapa_gi_pre_eventos ]");
		if( isset($this->memoria['etapa_gi']) ){
			// Habia una etapa anterior
			$this->set_etapa_gi( $this->memoria['etapa_gi'] );
			// 
		}else{
			// Request inicial
			// Esto no deberia pasar nunca, porque en el request inicial no se disparan los eventos
			// porque el CI no se encuentra entre las dependencias previas
			$this->set_etapa_gi( $this->get_etapa_actual() );
		}
		$this->log->debug( $this->get_txt() . "etapa_gi_PRE_eventos: {$this->etapa_gi}");
	}
	//-------------------------------------------------------------------------------

	/**
	 * Define la etapa de Generacion de Interface correspondiente al procesamiento del evento ACTUAL
	 * ATENCION: esto se esta ejecutando despues de los eventos propios... 
	 * puede traer problemas de ejecucion de eventos antes de validar la salida de etapas
	 */
	function definir_etapa_gi_post_eventos()
	{
		$etapa_previa = (isset($this->memoria['etapa_gi'])) ? $this->memoria['etapa_gi'] : null;
		$etapa_actual = $this->get_etapa_actual();
		$this->log->debug( $this->get_txt() . "[ definir_etapa_gi_post_eventos ]");
		if($etapa_previa !== $etapa_actual){ //¿Se cambio de etapa?
			// -[ 1 ]-  Controlo que se pueda salir de la etapa anterior
			// Esto no lo tengo que subir al metodo anterior?
			if( isset($this->memoria['etapa_gi']) ){
				// Habia una etapa anterior
				$evento_salida = apex_ei_evento . apex_ei_separador . "salida" . apex_ei_separador . $this->memoria['etapa_gi'];
				//Evento SALIDA
				if(method_exists($this, $evento_salida)){
					$this->$evento_salida();
				}
			}	
			// -[ 2 ]-  Controlo que se pueda ingresar a la etapa propuesta como ACTUAL
			$evento_entrada = apex_ei_evento . apex_ei_separador . "entrada" . apex_ei_separador . $etapa_actual;
			if(method_exists($this, $evento_entrada)){
				$this->$evento_entrada();
			}
		}
		// -[ 3 ]-  Seteo la etapa PROPUESTA
		$this->set_etapa_gi($etapa_actual);
		$this->log->debug( $this->get_txt() . "etapa_gi_POST_eventos: {$this->etapa_gi}");
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
			$this->controlador = $this;	//El CI exterior es su propio controlador
			$this->inicializar();
			$this->disparar_eventos();
		}catch(excepcion_toba $e){
			$this->log->debug($e);			
			$this->informar_msg($e->getMessage(), 'error');
		}
	}

	/**
	 * Se les ordena a las dependencias que gatillen sus eventos
	 * Cualquier error que aparezca, sea donde sea, se atrapa en el ultimo nivel.
	 * @todo Esto esta bien? --> cuando aparece el primer error no se sigan procesando las cosas... solo se puede atrapar un error.
	 */
	protected function disparar_eventos()
	{
		$this->log->debug( $this->get_txt() . "[ disparar_eventos ]");

		//PANTALLA
		$this->definir_etapa_gi_pre_eventos();

		$this->controlar_eventos_propios();
		//Los eventos que no manejan dato tienen que controlarse antes
		if( isset($this->memoria['eventos'][$this->evento_actual]) && 
				$this->memoria['eventos'][$this->evento_actual] == false ) {
			$this->disparar_evento_propio();
		}else{
			//Disparo los eventos de las dependencias
			foreach( $this->get_dependencias_interface_previa() as $dep)
			{
				$this->dependencias[$dep]->disparar_eventos();
			}
			$this->disparar_evento_propio();
			$this->evt__post_recuperar_interaccion();
		}

		//PANTALLA
		$this->definir_etapa_gi_post_eventos();
	}

	/**
	 * Reconoce que evento del CI se ejecuto
	 */
	protected function controlar_eventos_propios()
	{
		$this->evento_actual = "";
		if(isset($_POST[$this->submit])){
			$evento = $_POST[$this->submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if(isset(  $this->memoria['eventos'][$evento] )){
				$this->evento_actual = $evento;
				$this->evento_actual_param = $_POST[$this->submit."__param"];
			}
		}
	}

	protected function disparar_evento_propio()
	{
		if($this->evento_actual != "")
		{
			$metodo = apex_ei_evento . apex_ei_separador . $this->evento_actual;
			if(method_exists($this, $metodo)){
				//Ejecuto el metodo que implementa al evento
				$this->log->debug( $this->get_txt() . "[ disparar_evento_propio ] '{$this->evento_actual}' -> [ $metodo ]");
				$this->$metodo($this->evento_actual_param);
				//Comunico el evento al contenedor
				$this->reportar_evento( $this->evento_actual );
			}else{
				$this->log->info($this->get_txt() . "[ disparar_evento_propio ]  El METODO [ $metodo ] no existe - '{$this->evento_actual}' no fue atrapado");
			}
		}
	}

	/**
	 * Devuelve la lista de dependencias que se utlizaron para generar la interface anterior
	 * @return unknown
	 */
	protected function get_dependencias_interface_previa()
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

	/**
	 * Se disparan eventos dentro del nivel actual
	 * Puede recibir N parametros adicionales
	 * @param string $id Id. o rol que tiene la dependencia en este objeto
	 * @param string $evento Id. del evento
	 */
	function registrar_evento($id, $evento) 
	{
		$parametros	= func_get_args();
		array_splice($parametros, 0 , 2);
		$metodo = apex_ei_evento . apex_ei_separador . $id . apex_ei_separador . $evento;
		if(method_exists($this, $metodo)){
			$this->log->debug( $this->get_txt() . "[ registrar_evento ] '$evento' -> [ $metodo ]\n" . var_export($parametros, true));
			return call_user_func_array(array($this, $metodo), $parametros);
		}else{
			$this->log->info($this->get_txt() . "[ registrar_evento ]  El METODO [ $metodo ] no existe - '$evento' no fue atrapado");
			//Puede implementarse un metodo generico de manejo de eventos? 
		}
	}

	//---- EVENTOS BASICOS ------

	/**
	 * Despues de recuperar la interaccion con el usuario
	 */
	function evt__post_recuperar_interaccion()
	{
		/*
		$this->evt__validar_datos();
		*/
	}

	/**
	 * Validar el estado interno, dispara una excepcion si falla
	 */
	function evt__validar_datos()
	{
	}

	/**
	 * Disparada cuando un hijo falla en su procesamiento
	 */
	function evt__error_proceso_hijo( $dependencia )
	{
		$this->error_proceso_hijo[] = $dependencia;
	}

	/**
	 * Evento predefinido de cancelar, limpia este objeto, y en caso de exisitr, cancela al cn asociado
	 */
	function evt__cancelar()
	{
		$this->log->debug($this->get_txt() . "[ evt__cancelar ]");
		$this->disparar_limpieza_memoria();
		if(isset($this->cn)){
			$this->cn->cancelar();			
		}
	}

	/**
	 * Evento predefinido de procesar, en caso de existir el cn le entrega los datos y limpia la memoria
	 */
	function evt__procesar()
	{
		$this->log->debug($this->get_txt() . "[ evt__procesar ]");
		if(isset($this->cn)){
			$this->disparar_entrega_datos_cn();
			$this->cn->procesar();
		}
		$this->disparar_limpieza_memoria();
	}	

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------   Generacion de la INTERFACE GRAFICA   ----------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna la descripción de una pantalla particular definida en el administrador
	 * Redefinir en caso de que la descripción sea dinámica
	 */
	function obtener_descripcion_pantalla($pantalla)
	{
		return trim($this->info_ci_me_pantalla[$this->indice_etapas[$pantalla]]["descripcion"]);
	}
	
	
	/**
	 * Cargar las dependencias a utilizar para generar la interface de este objeto
	 */
	function cargar_dependencias_gi()
	{
		$this->log->debug($this->get_txt() . "[ cargar_dependencias_gi ]");
		//Busco la lista de las dependencias que necesito para cargar esta interface
		$this->dependencias_gi = $this->get_lista_ei();
		//Creo las dependencias
		$this->inicializar_dependencias( $this->dependencias_gi );
		$this->evt__pre_cargar_datos_dependencias();
		$this->cargar_datos_dependencias();
		$this->evt__post_cargar_datos_dependencias();
	}
	//-------------------------------------------------------------------------------

	/**
	 * Determina la lista de elementos de interface (ei) que se muestran en esta pantalla
	 * Para redefinir esta lista para una pantalla particular hay que definir un metodo get_lista_ei__PANTALLA, 
	 * donde PANTALLA es la buscada.
	 * @return array Arreglo de elementos a mostrar en esta pantalla
	 */
	function get_lista_ei()
	{
		//Existe una definicion especifica para esta etapa?
		$metodo_especifico = "get_lista_ei" . apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $metodo_especifico)){
			return $this->$metodo_especifico();	
		}		
		//Busco la definicion standard para la etapa
		$objetos = trim( $this->info_ci_me_pantalla[ $this->indice_etapas[ $this->etapa_gi ] ]["objetos"] );
		if( $objetos != "" ){
			return array_map("trim", explode(",", $objetos ) );
		}else{
			return array();
		}
	}
	//-------------------------------------------------------------------------------

	/**
	 * Método que se ejecuta antes de que se carguen los datos de las dependencias
	 * Para incorporar algún comportamiento previo a la carga en una pantalla particular definir un método
	 * evt__pre_cargar_datos_dependencias__PANTALLA donde PANTALLA es la pantalla buscada
	 */
	function evt__pre_cargar_datos_dependencias()
	{
		//Existe una definicion especifica para esta etapa?
		$metodo_especifico = "evt__pre_cargar_datos_dependencias" . apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $metodo_especifico)){
			$this->$metodo_especifico();	
		}		
	}
	//-------------------------------------------------------------------------------

	/**
	 * Dispara la carga de datos de las dependencias del objeto
	 */
	protected function cargar_datos_dependencias()
	{
		//Disparo la carga de dependencias en los CI que me componen
		foreach($this->dependencias_gi as $dep)
		{
			if(	$this->dependencias[$dep] instanceof objeto_ci ){		
				//	Hago que cargue sus dependencias
				$this->dependencias[$dep]->cargar_dependencias_gi();
			}else{														
				if( $this->dependencias[$dep] instanceof objeto_ei_formulario ){
					//-- Carda de combos dinamicos
					$this->cargar_daos_dinamicos_dependencia( $dep );
				}
				//-- Inyecto DATOS en los EIs, si es que existe un metodo para cargarlos --
				$this->dependencias[$dep]->cargar_datos( $this->proveer_datos_dependencia($dep) );
				$this->dependencias[$dep]->definir_eventos();
			}
		}
	}	

	protected function proveer_datos_dependencia( $dependencia )
	{
		$metodo = apex_ei_evento . apex_ei_separador . $dependencia . apex_ei_separador . "carga";
		if(method_exists($this, $metodo)){
			$this->log->debug($this->get_txt() . "[ cargar_datos_dependencia ] '$dependencia' -> [ $metodo ] ");
			return $this->$metodo();
		}else{
			$this->log->info($this->get_txt() . "[ cargar_datos_dependencia ] El METODO [ $metodo ] no existe - '$dependencia' no fue cargada");
			return null;
		}
	}
	
	protected function cargar_daos_dinamicos_dependencia( $dep )
	{
		//Un EF-COMBO puede solicitar la carga al CI que los contiene si sus valores no son estaticos
		if( $dao_form = $this->dependencias[$dep]->obtener_consumo_dao() ){
			//ei_arbol($dao_form,"DAO");
			//Por cada elemento de formulario que necesita DAOS
			foreach($dao_form as $ef => $dao){
				if(method_exists($this, $dao)){
					$datos = $this->$dao();
					//ei_arbol($datos,"DATOS $ef");
					$this->dependencias[$dep]->ejecutar_metodo_ef($ef,"cargar_datos",$datos);
				}else{
					throw new excepcion_toba_def("El METODO '$dao' ha sido declarado como DAO y no se encuentra en el CI");
				}
			}
		}
	}
	
	//-------------------------------------------------------------------------------

	/**
	 * Método que se ejecuta luego de que se carguen los datos de las dependencias
	 * Para incorporar algún comportamiento luego de la carga en una pantalla particular definir un método
	 * evt__post_cargar_datos_dependencias__PANTALLA donde PANTALLA es la pantalla buscada
	 */	
	function evt__post_cargar_datos_dependencias()
	{
		//Existe una definicion especifica para esta etapa?
		$metodo_especifico = "evt__post_cargar_datos_dependencias" . apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $metodo_especifico)){
			$this->$metodo_especifico();	
		}		
	}

	//-------------------------------------------------------------------------------
	
	/**
	 * Obtiene la lista de eventos definidos desde el administrador 
	 * Se redefine el método para dejar sólo aquellos eventos definidos en esta pantalla
	 * @return unknown
	 */
	protected function get_lista_eventos_definidos()
	{
		$eventos = array();
		$ev_totales = parent::get_lista_eventos_definidos();
		$ev_etapa = explode(',', $this->info_ci_me_pantalla[ $this->indice_etapas[$this->etapa_gi] ]['eventos']);
		foreach (array_keys($ev_totales) as $id) {
			if (! in_array($id, $ev_etapa)) {
				unset($ev_totales[$id]);
			}
		}
		return $ev_totales;
	}

	
	/**
	 * Retorna la lista TOTAL de eventos de este objeto
	 * @return array
	 */
	function get_lista_eventos()
	{
		$eventos = array();
		// Eventos de TABS
		switch($this->info_ci['tipo_navegacion'])
		{
			case "tab_h":
			case "tab_v":
				foreach ($this->get_lista_tabs() as $id => $tab) {
					$eventos += eventos::ci_cambiar_tab($id);
				}
				break;
			case "wizard":
				list($anterior, $siguiente) = $this->pantallas_limitrofes($this->etapa_gi);
				if ($anterior !== false)
					$eventos += eventos::ci_pantalla_anterior($anterior);
				if ($siguiente !== false)
					$eventos += eventos::ci_pantalla_siguiente($siguiente);
				break;
		}
		$eventos = array_merge($eventos, parent::get_lista_eventos() );
		return $eventos;
	}

	//---------------------------------------------------------------
	//-------------------------- SALIDA HTML --------------------------
	//----------------------------------------------------------------
	
	function obtener_html()
	{
		echo "\n<!-- ################################## Inicio CI ( ".$this->id[1]." ) ######################## -->\n\n";		
		//-->Listener de eventos
		$this->eventos = $this->get_lista_eventos();
		if( count($this->eventos) > 0){
			echo form::hidden($this->submit, '');
			echo form::hidden($this->submit."__param", '');
		}
		$ancho = isset($this->info_ci["ancho"]) ? "width='" . $this->info_ci["ancho"] . "'" : "";
		$alto = isset($this->info_ci["alto"]) ? "height='" . $this->info_ci["alto"] . "'" : "";
		echo "<table $ancho $alto class='objeto-base' id='{$this->objeto_js}_cont'>\n";
		//--> Barra SUPERIOR
		echo "<tr><td class='celda-vacia'>";
		$this->barra_superior(null,true,"objeto-ci-barra-superior");
		echo "</td></tr>\n";
		$colapsado = (isset($this->colapsado) && $this->colapsado) ? "style='display:none'" : "";
		echo "<tbody $colapsado id='cuerpo_{$this->objeto_js}'>\n";
		$this->obtener_html_cuerpo();
		echo "</tbody>";
		echo "</table>\n";
		$this->gi = true;
		echo "\n<!-- ###################################  Fin CI  ( ".$this->id[1]." ) ######################## -->\n\n";
	}
	
	protected function obtener_html_cuerpo()
	{	
		//--> Botonera
		$con_botonera = $this->hay_botones();
		if($con_botonera && ($this->posicion_botonera == "arriba" || $this->posicion_botonera == "ambos") ){
			echo "<tr><td class='abm-zona-botones'\n>";
			$this->obtener_botones();
			echo "</td></tr>\n";
		}
		//--> Cuerpo del CI
		echo "<tr><td class='ci-cuerpo' height='100%'>\n";
		$this->obtener_html_pantalla();
		echo "</td></tr>\n";
		//--> Botonera
		if($con_botonera && ($this->posicion_botonera == "abajo" || $this->posicion_botonera == "ambos") ){
			echo "<tr><td class='abm-zona-botones'>\n";
			$this->obtener_botones();
			echo "</td></tr>\n";
		}
	}
	
	private function obtener_html_pantalla()
	{
		switch($this->info_ci['tipo_navegacion'])
		{
			case "tab_h":									//*** TABs horizontales
				echo "<table class='tabla-0' width='100%'>\n";
				//Tabs
				echo "<tr><td class='celda-vacia'>";
				$this->obtener_tabs_horizontales();
				echo "</td></tr>\n";
				//Interface de la etapa correspondiente
				echo "<tr><td class='tabs-contenedor' height='100%'>";
				$this->obtener_html_pantalla_contenido();
				echo "</td></tr>\n";
				echo "</table>\n";
				break;				
			case "tab_v": 									//*** TABs verticales
				echo "<table class='tabla-0' width='100%'>\n";
				echo "<tr><td  class='celda-vacia' height='100%'>";
				$this->obtener_tabs_verticales();
				echo "</td>";
				echo "<td class='tabs-v-contenedor' height='100%'>";
				$this->obtener_html_pantalla_contenido();
				echo "</td></tr>\n";
				echo "</table>\n";
				break;				
			case "wizard": 									//*** Wizard (secuencia estricta hacia adelante)
				echo "<table class='tabla-0' >\n";
				echo "<tr><td class='celda-vacia'  height='100%'>";
				if ($this->info_ci['con_toc']) {
					$this->wizard_mostrar_toc();
				}
				echo "</td>";
				echo "<td width='100%' class='tabs-contenedor' height='100%'>";
				$this->obtener_html_pantalla_contenido();
				echo "</td></tr>\n";
				echo "</table>\n";
				break;				
			default:										//*** Sin mecanismo de navegacion
				$this->obtener_html_pantalla_contenido();
		}
	}

	/**
	 * Grafica el contenido de la pantalla actual
	 */
	protected function obtener_html_pantalla_contenido()
	{
		//--- Descripcion de la PANTALLA
		$descripcion = $this->obtener_descripcion_pantalla($this->etapa_gi);
		$es_wizard = $this->info_ci['tipo_navegacion'] == 'wizard';
		if($descripcion !="" || $es_wizard) {
			$imagen = recurso::imagen_apl("info_chico.gif",true);
			$descripcion = parser_ayuda::parsear($descripcion);
			if ($es_wizard) {
				$html = "<div class='wizard-encabezado'><div class='wizard-titulo'>";
				$html .= $this->info_ci_me_pantalla[ $this->indice_etapas[ $this->etapa_gi ] ]["etiqueta"];
				$html .= "</div><div class='wizard-descripcion'>$descripcion</div></div>";
				echo $html;
			} else {
				echo "<div class='txt-info'>$imagen&nbsp;$descripcion</div>\n";
			}
			echo "<hr>\n";
		}
		//--- Controla la existencia de una funcion que redeclare la generacion de una PANTALLA puntual
		$interface_especifica = "obtener_html_contenido". apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $interface_especifica)){
			$this->$interface_especifica();
		}else{
			//--- Solicita el HTML de todas las dependencias que forman parte de la generacion de la interface
			$this->obtener_html_dependencias();
		}
	}
	
	/**
	 * Dispara la generación de html de los objetos contenidos en esta pantalla
	 * Para redefinir la generación de una pantalla puntual, hay que definir un método:
	 * 		obtener_html_contenido__PANTALLA 
	 */	
	function obtener_html_dependencias()
	{
		$existe_previo = 0;
		foreach($this->dependencias_gi as $dep)
		{
			if($existe_previo){ //Separador
				echo "<hr>\n";
			}
			$this->dependencias[$dep]->obtener_html();	
			$existe_previo = 1;
		}
	}

	protected function wizard_mostrar_toc()
	{
		$this->lista_tabs = $this->get_lista_tabs();
		echo "<ol class='wizard-pantallas'>";
		$pasada = true;
		foreach ($this->lista_tabs as $id => $pantalla) {
			if ($pasada)
				$clase = 'wizard-pantallas-pasada';
			else
				$clase = 'wizard-pantallas-futuro';			
			if ($id == $this->etapa_gi) {
				$clase = 'wizard-pantallas-actual';
				$pasada = false;
			}
			echo "<li class='$clase'>";
			echo $pantalla['etiqueta'];
			echo "</li>";
		}		
		echo "</ol>";
	}

	protected function obtener_tabs_horizontales()
	{
		$this->lista_tabs = $this->get_lista_tabs();
		echo "<table width='100%' class='tabla-0'>\n";
		echo "<tr>";
		//echo "<td width='1'  class='tabs-solapa-hueco'>".gif_nulo(6,1)."</td>";
		foreach( $this->lista_tabs as $id => $tab )
		{
			$tip = $tab["tip"];
			$clase = 'tabs-boton';
			$tab_order = 0;
			$acceso = tecla_acceso( $tab["etiqueta"] );
			$html = '';
			if(isset($tab['imagen'])) {
				$html = recurso::imagen($tab['imagen'], null, null, null, null, null, 'vertical-align: middle;' ).' ';
			}
			$html .= $acceso[0];
			$tecla = $acceso[1];
			$js = "onclick=\"{$this->objeto_js}.ir_a_pantalla('$id');\"";
			if( $this->etapa_gi == $id ){
				//TAB actual
				echo "<td class='tabs-solapa-sel'>";
				echo form::button_html( "actual", $html, '', $tab_order, null, '', 'button', '', "tabs-boton-sel");
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}else{
				echo "<td class='tabs-solapa'>";
				echo form::button_html( $this->submit.'_cambiar_tab_'.$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase);
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}
		}
		echo "<td width='90%'  class='tabs-solapa-hueco'>".gif_nulo()."</td>\n";
		echo "</tr>";
		echo "</table>\n";
	}

	function obtener_tabs_verticales()
	{
		$this->lista_tabs = $this->get_lista_tabs();
		echo "<div  class='tabs-v-solapa' style='height:20px'> </div>";
		foreach( $this->lista_tabs as $id => $tab )
		{
			$clase = 'tabs-boton';
			$tab_order = 0;
			$acceso = tecla_acceso( $tab["etiqueta"] );
			$tip = $tab["tip"];
			$html = '';
			if(isset($tab['imagen'])) 
				$html = recurso::imagen($tab['imagen'], null, null, null, null, null, 'vertical-align: middle;' ).' ';
			$html .= $acceso[0];
			$tecla = $acceso[1];
			$js = "onclick=\"{$this->objeto_js}.set_evento( new evento_ei('cambiar_tab_$id', true, ''));\"";
			if ( $this->etapa_gi == $id ) {
				echo "<div class='tabs-v-solapa-sel'><div class='tabs-v-boton-sel'>$html</div></div>";
			} else {
				$atajo = recurso::ayuda($tecla, $tip);
				echo "<div class='tabs-v-solapa'>";
				echo "<a id='".$this->submit.'_cambiar_tab_'.$id."' href='#' $atajo class='tabs-v-boton' $js>$html</a>";
				echo "</div>";
			}
		}
		echo "<div class='tabs-v-solapa' style='height:99%;'></div>";
	}
	
	/**
	 * Retorna la lista de botones que representan a las pestañas o tabs que se muestran en la pantalla actual
	 * Para inhabilitar algún tab, heredar, llamar a este método y sacar el tab del arreglo resultante
	 * @return array
	 */
	function get_lista_tabs()
	{
		$tab = array();
		for($a = 0; $a<count($this->info_ci_me_pantalla);$a++)
		{
			$id = $this->info_ci_me_pantalla[$a]["identificador"];
			$tab[$id]['etiqueta'] = $this->info_ci_me_pantalla[$a]["etiqueta"];
			$tab[$id]['tip'] = $this->obtener_descripcion_pantalla($id);
			if ($this->info_ci_me_pantalla[$a]["imagen_recurso_origen"]) {
				if ($this->info_ci_me_pantalla[$a]["imagen_recurso_origen"] == 'apex') 
					$tab[$id]['imagen'] = recurso::imagen_apl($this->info_ci_me_pantalla[$a]["imagen"], false);
				else
					$tab[$id]['imagen'] = recurso::imagen_pro($this->info_ci_me_pantalla[$a]["imagen"], false);
			}
		}
		return $tab;
	}

	//---------------------------------------------------------------
	//-------------------------- HTML PARCIAL (AJAX) -----------------
	//----------------------------------------------------------------
	
	function servicio__html_parcial()
	{
		$this->eventos = $this->get_lista_eventos();
		$this->obtener_html_cuerpo();
		$this->gi = true;
	}
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna los consumos javascript requerido por este objeto y sus dependencias
	 * @return array
	 */
	function consumo_javascript_global()
	{
		$consumo_js = parent::consumo_javascript_global();
		$consumo_js[] = 'clases/objeto_ci';
		foreach($this->dependencias_gi as $dep){
			$temp = $this->dependencias[$dep]->consumo_javascript_global();
			if(isset($temp))
				$consumo_js = array_merge($consumo_js, $temp);
		}
		return $consumo_js;
	}

	function crear_objeto_js()
	{
		$identado = js::instancia()->identado();	
		//Crea le objeto CI
		echo $identado."window.{$this->objeto_js} = new objeto_ci('{$this->objeto_js}', '{$this->nombre_formulario}', '{$this->submit}');\n";

		//Crea los objetos hijos
		$objetos = array();
		js::instancia()->identar(1);		
		foreach($this->dependencias_gi as $dep)	{
			$objetos[$dep] = $this->dependencias[$dep]->obtener_javascript();
		}
		$identado = js::instancia()->identar(-1);		
		//Agrega a los objetos hijos
		//ATENCION: Esto no permite tener el mismo formulario instanciado dos veces
		echo "\n";
		foreach ($objetos as $id => $objeto) {
			echo $identado."{$this->objeto_js}.agregar_objeto($objeto, '$id');\n";
		}
	}
	
	//---------------------------------------------------------------
	//------------------------ SALIDA Impresion ---------------------
	//---------------------------------------------------------------
	
	function vista_impresion( impresion $salida )
	{
		$salida->titulo( $this->get_titulo() );
		foreach($this->dependencias_gi as $dep) {
			$this->dependencias[$dep]->vista_impresion( $salida );
		}
	}
	
	//---------------------------------------------------------------
	//-------------------------- OBSOLETOS --------------------------
	//----------------------------------------------------------------
	
	/**
	 * Esta funcion DISPARABA la generacion de TODA la interface.
	 * Solo es llamado por el CI EXTERIOR. La composicion recursiva es a travez de 'obtener_html'
	 * @deprecated Desde 0.9.0, se debe utilizar la solicitud_web
	 */
	function generar_interface_grafica()
	{
		toba::get_logger()->obsoleto(__CLASS__, __METHOD__, "0.8.4", "El ítem debería tener una solicitud_web asociada");
		$this->log->debug($this->get_txt() . "____________________________________________[ generar_interface_grafica ]");
		try{
			//Cargar todos los EI que componen la interface
			$this->cargar_dependencias_gi();
			$this->obtener_html_base();
		}catch(excepcion_toba $e){
			$this->log->debug($e);
			$this->informar_msg($e->getMessage(), 'error');
			toba::get_cola_mensajes()->mostrar();
		}
	}
	
	/**
	 * @deprecated Desde 0.9.0, se debe utilizar la solicitud_web
	 */
	protected function obtener_html_base()
	{
		//-[1]- Muestro la cola de mensajes
		//-[2]- Genero la SALIDA
		$vinculo = toba::get_vinculador()->generar_solicitud(null,null,null,true);
		$this->obtener_javascript_global_consumido();
		echo "<br>\n";
		echo form::abrir($this->nombre_formulario, $vinculo);
		echo "<div align='center'>\n";
		$this->obtener_html();
		echo "</div>\n";
		echo form::cerrar();
		
		echo js::abrir();
		$this->obtener_javascript();
		$identado = js::instancia()->identado();
		echo $identado."{$this->objeto_js}.iniciar();\n";
		echo js::cerrar();
		echo "<br>\n";
		toba::get_cola_mensajes()->mostrar();	
	}
	
	
	/**
	 * @deprecated Desde 0.8.4, se debe utilizar la solicitud_web
	 */
	function obtener_javascript_global_consumido()
	{
		js::cargar_consumos_globales($this->consumo_javascript_global());
	}
	
	
	
}
?>
