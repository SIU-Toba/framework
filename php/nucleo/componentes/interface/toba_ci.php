<?php
/**
 * Controlador de Interface: Componente responsable de manejar las pantallas y sus distintos elementos
 * 
 * Este componente puede mantener sus propiedades en sesion con solo prefijar los nombres de variables con s__ (por ej. protected $s__cuit )
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ci ci
 * @wiki Referencia/Objetos/ci
 */
class toba_ci extends toba_ei
{
	// General
	protected $_info_ci = array();
	protected $_info_ci_me_pantalla = array();
	protected $_info_obj_pantalla = array();			//Lista de los objetos asociados a las pantallas
	protected $_info_evt_pantalla = array();			//Lista de los eventos asociados a las pantallas
 	protected $_prefijo = 'ci';
	protected $_cn=null;								// Controlador de negocio asociado
	protected $_dependencias_ci = array();			// Lista de dependencias CI utilizadas en el REQUEST
	protected $_dependencias_gi = array();			// Dependencias utilizadas para la generacion de la interface
	protected $_dependencias_inicializadas = array();// Lista de dependencias inicializadas
	protected $_dependencias_configuradas = array();
	protected $_eventos;								// Lista de eventos que expone el CI
	protected $_evento_actual;						// Evento propio recuperado de la interaccion
	protected $_evento_actual_param;					// Parametros del evento actual
	// Pantalla
	protected $_pantalla_id_eventos;					// Id de la pantalla que se atienden eventos
	private   $_pantalla_id_servicio;					// Id de la pantalla a mostrar en el servicio
	protected $_pantalla_servicio;					// Comp. pantalla que se muestra en el servicio 
	protected $_es_pantalla_inicial = false;
	protected $_en_servicio = false;					// Indica que se ha entrado en la etapa de servicios
	protected $_ini_operacion = true;				// Indica si la operación recién se inicia
	protected $_wizard_sentido_navegacion;			// Indica si el wizard avanza o no
	protected $_metodos_ajax;						//Metodos AJAX que se pueden invocar en este pedido de página
	
	static $_navegacion_ajax = false;
	
	final function __construct($id)
	{
		$this->set_propiedades_sesion(array('_ini_operacion','_dependencias_ci', '_metodos_ajax'));		
		parent::__construct($id);
		$this->_nombre_formulario = "formulario_toba" ;//Cargo el nombre del <form>
	}
	
	static function set_navegacion_ajax($set=true)
	{
		self::$_navegacion_ajax = $set;
	}


	/**
	 * Destructor del componente
	 */	
	function destruir()
	{
		$this->fin();
		if( isset($this->_pantalla_servicio) ){
			//Guardo INFO sobre la interface generada
			$this->_memoria['pantalla_dep'] = $this->_pantalla_servicio->get_lista_dependencias();
			$this->_memoria['pantalla_servicio'] = $this->_pantalla_id_servicio;
			$this->_memoria['tabs'] = array_keys($this->_pantalla_servicio->get_lista_tabs());
			$this->_eventos_usuario_utilizados = $this->_pantalla_servicio->get_lista_eventos_usuario();
			$this->_eventos = $this->_pantalla_servicio->get_lista_eventos_internos();
			//Guarda aquellos metodos ajax que se pueden invocar en el pedido siguiente
			$this->_metodos_ajax = reflexion_buscar_metodos($this, 'ajax__');
	
		}
		parent::destruir();
	}
	
	/**
	 * Ventana de extensión previa a la destrucción del componente, al final de la atención de los servicios
	 * @ventana
	 */
	function fin() {}

	/**
	 * @ignore 
	 */
	function inicializar($parametro=array())
	{
		$this->_inicializado = true;
		$this->recuperar_estado_sesion();	//RECUPERO Memoria desincronizada		
		if(isset($parametro['nombre_formulario'])){
			$this->_nombre_formulario = $parametro['nombre_formulario'];
		}
		if ($this->_ini_operacion) {
			$this->_log->debug($this->get_txt(). "[callback][ ini__operacion ]", 'toba');
			$this->ini__operacion();
			$this->_ini_operacion = false;
			$this->_es_pantalla_inicial = true;
		}
		$this->ini();
		$this->definir_pantalla_eventos();		
	}

	/**
	 * Ventana de extensión que se ejecuta cuando el componente se inicia en la operación.
	 * Su utilidad recide en por ejemplo inicializar un conjunto de variables de sesion y evitar
	 * el chequeo continuo de las mismas.
	 * Este momento generalmente se corresponde con el inicio de la operación, aunque existen excepciones:
	 *  - Si el componente es un ci dentro de otro ci, recien se ejecuta cuando entra a la operacion que no necesariamente es al inicio,
	 * 		si por ejemplo se encuentra en la 3er pantalla del ci principal.
	 *  - Si se ejecuta una limpieza de memoria (comportamiento por defecto del evt__cancelar)
	 * 
	 * @ventana
	 */
	function ini__operacion() {}
	
	/**
	 * Ventana de extensión que se ejecuta al iniciar el componente en todos los pedidos en los que participa.
	 * Como la ventana es previa a la atención de eventos y servicios es un punto ideal para la configuración global del componente
	 * @ventana
	 */
	function ini() {}
	
	//--------------------------------------------------------------
	//---------  Manejo de MEMORIA -------------------------------
	//--------------------------------------------------------------
		
	/**
	 * Borra la memoria de todas las dependencias, la propia y luego ejecuta ini__operacion
	 */
	function disparar_limpieza_memoria($no_borrar = array())
	{
		$this->_log->debug( $this->get_txt() . "[callback][ disparar_limpieza_memoria ]", 'toba');
		//Itero los CIs instanciados durante la operacion para limpiarles la memoria
		if (isset($this->_dependencias_ci)) {
			foreach($this->_dependencias_ci as $dep){
				$this->dependencia($dep)->disparar_limpieza_memoria();
			}
		}

		array_push($no_borrar, '_ini_operacion');
		$this->limpiar_memoria($no_borrar);
		unset($this->_pantalla_id_eventos);		
		$this->_log->debug($this->get_txt(). "[callback][ ini__operacion ]", 'toba');
		$this->ini__operacion();
	}
	
	/**
	 * Borra la memoria de este CI y lo reinicializa
	 * @param array $no_borrar Excepciones, propiedades que no se van a poner en null
	 */
	function limpiar_memoria($no_borrar=null)
	{
		$this->_log->debug( $this->get_txt() . "[callback][ limpiar_memoria ]", 'toba');
		$this->borrar_memoria();
		$this->eliminar_estado_sesion($no_borrar);
		$this->ini();
	}
		
	//--------------------------------------------------------------
	//------  Interaccion con un CONTROLADOR de NEGOCIO ------------
	//--------------------------------------------------------------

	/**
	 * Retorna el componente de negocio asociado a este ci
	 * @return toba_cn
	 */
	function cn()
	{
		return $this->_cn;	
	}

	/**
	 * Asocia al componente un controlador de negocio
	 * @param toba_cn $controlador
	 * @see toba_cn
	 */
	function asignar_controlador_negocio( $controlador )
	{
		$this->_cn = $controlador;
	}

	//------------------------------------------------
	//--  ETAPA EVENTOS   ----------------------------
	//------------------------------------------------
	
	/**
	 * Se disparan los eventos propios y se les ordena a las dependencias que gatillen sus eventos
	 * Cualquier error de usuario que aparezca, sea donde sea, se atrapa en la solicitud
	 * @todo Esto esta bien? --> cuando aparece el primer error no se sigan procesando las cosas... solo se puede atrapar un error.
	 * @ignore 
	 */
	function disparar_eventos()
	{
		//$this->_log->debug( $this->get_txt() . " disparar_eventos", 'toba');

		//--- Si no hubo servicio anterior, no se atienden eventos
		if (isset($this->_pantalla_id_eventos)) {
			$this->controlar_eventos_propios();
			//Los eventos que no manejan dato tienen que controlarse antes
			$existe_evento = isset($this->_memoria['eventos'][$this->_evento_actual]);
			if($existe_evento && $this->_memoria['eventos'][$this->_evento_actual] == apex_ei_evt_no_maneja_datos ) {
				$this->disparar_evento_propio();
			} else {
				//Disparo los eventos de las dependencias
				foreach($this->get_dependencias_eventos() as $dep) {
					$this->_dependencias[$dep]->disparar_eventos();
				}
				if ($existe_evento) {
					$this->disparar_evento_propio();
				}
			}
		} else {
 			$this->_log->info( $this->get_txt() . "No hay señales de un servicio anterior, no se atrapan eventos", 'toba');
		}
		$this->post_eventos();		
		$this->controlar_cambio_pantalla();
		$this->borrar_memoria_eventos_atendidos();
	}
	
	/**
	 * Ventana que se ejecuta una vez que todos los eventos se han disparado para este objeto
	 * @ventana
	 */
	protected function post_eventos() {}
	
	/**
	 * Si existio un cambio explicito de pantalla se notifican las callbacks de entrada-salida
	 * @ignore 
	 */
	protected function controlar_cambio_pantalla()
	{
		$cambio_pantalla_explicito = isset($this->_pantalla_id_servicio) && 
										($this->_es_pantalla_inicial ||
										(isset($this->_pantalla_id_eventos) &&
				 						$this->_pantalla_id_servicio !== $this->_pantalla_id_eventos));
		
		//--- Se da la oportunidad de que alguien rechaze el seteo, y vuelva todo para atras
		if ($cambio_pantalla_explicito) { 
			try {
				if (! $this->_es_pantalla_inicial) {
					// -[ 1 ]-  Controlo que se pueda salir de la pantalla anterior
					$evento_salida = apex_ei_evento . apex_ei_separador . $this->_pantalla_id_eventos . apex_ei_separador . "salida";
					$this->invocar_callback($evento_salida);				
				}
	
				// -[ 2 ]-  Controlo que se pueda ingresar a la etapa propuesta como ACTUAL
				$evento_entrada = apex_ei_evento . apex_ei_separador . $this->_pantalla_id_servicio . apex_ei_separador . "entrada";
				$this->invocar_callback($evento_entrada);
			} catch (toba_error $e) {
				//--- Si se lanza una excepción se recupera el id de la pantalla original
				$this->_pantalla_id_servicio = $this->_pantalla_id_eventos;
				throw $e;	
			}
		}
	}

	/**
	 * Reconoce que evento del CI se ejecuto
	 * @ignore 
	 */
	protected function controlar_eventos_propios()
	{
		$this->_evento_actual = "";
		if (isset($_POST[$this->_submit]) && $_POST[$this->_submit] != '') {
			$evento = $_POST[$this->_submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if (isset( $this->_memoria['eventos'][$evento] )) {
				$this->_evento_actual = $evento;
				$this->_evento_actual_param = $_POST[$this->_submit."__param"];
			} else {
				$this->_log->warning( $this->get_txt() . "Se recibio el evento $evento pero el mismo no está entre los disponibles", 'toba');
			}
		}
	}

	/**
	 * Dispara los eventos de usuarios o el de cambio de tab
	 * @ignore 
	 */
	protected function disparar_evento_propio()
	{
		if($this->_evento_actual != "")	{
			$metodo = apex_ei_evento . apex_ei_separador . $this->_evento_actual;
			if(method_exists($this, $metodo)){
				//Ejecuto el metodo que implementa al evento
				$this->_log->debug( $this->get_txt() . "[ evento ] '{$this->_evento_actual}' -> [ $metodo ]", 'toba');
				$this->$metodo($this->_evento_actual_param);
			
				//Comunico el evento al contenedor
				$this->reportar_evento_interno( $this->_evento_actual );
			}else{
				$this->_log->info($this->get_txt() . "[ evento ]  El METODO [ $metodo ] no existe - '{$this->_evento_actual}' no fue atrapado", 'toba');
			}
			
			//Se pidio explicitamente un id de pantalla o navegar atras-adelante?
			$tab = (strpos($this->_evento_actual, 'cambiar_tab_') !== false) ? str_replace('cambiar_tab_', '', $this->_evento_actual) : false;
			if ($tab == '_siguiente' || $tab == '_anterior') {
				$this->_wizard_sentido_navegacion = ($tab == '_anterior') ? 0 : 1;
				$this->set_pantalla($this->ir_a_limitrofe());
			} elseif ($tab !== false) {
				//--- Se pidio un cambio explicito de tab
				$this->set_pantalla($tab);
			}
		}
	}
		
	
	/**
	 * Dispara un evento dentro del nivel actual
	 * Puede recibir N parametros adicionales (ej <pre>$this->registrar_evento('form', ',modificacion', $datos, $fila,...)</pre>)
	 * @param string $id Id. o rol que tiene la dependencia en este objeto
	 * @param string $evento Id. del evento
	 * @ignore 
	 */
	function registrar_evento($id, $evento) 
	{
		$parametros	= func_get_args();
		array_splice($parametros, 0 , 2);
		$metodo = apex_ei_evento . apex_ei_separador . $id . apex_ei_separador . $evento;
		if (method_exists($this, $metodo)) {
			$this->_log->debug( $this->get_txt() . "[ registrar_evento ] '$evento' -> [ $metodo ]\n" . var_export($parametros, true), 'toba');
			$componente = $this->dep($id);
			//if ($this->debe_disparar_evento($componente, $evento)) {			//Por si se requiere el esquema en PHP
      		if ($componente->tiene_puntos_control($evento)) {
  				toba::puntos_control()->ejecutar_puntos_control($componente, $evento, $parametros);
      		}
		    return call_user_func_array(array($this, $metodo), $parametros);
			//}
		} else {
			$this->_log->info($this->get_txt() . "[ registrar_evento ]  El METODO [ $metodo ] no existe - '$evento' no fue atrapado", 'toba');
			return apex_ei_evt_sin_rpta;
		}
	}	

	/**
	 * Dispara un evento interno dentro del nivel actual
	 * Puede recibir N parametros adicionales (ej <pre>$this->registrar_evento_interno('form', ',modificacion', $datos, $fila,...)</pre>)
	 * @param string $id Id. o rol que tiene la dependencia en este objeto
	 * @param string $evento Id. del evento
	 * @ignore 
	 */	
	function registrar_evento_interno($id, $evento)
	{
		$parametros	= func_get_args();
		array_splice($parametros, 0 , 2);
		$metodo = apex_ei_evento . apex_ei_separador . $id . apex_ei_separador . $evento;
		if (method_exists($this, $metodo)) {
			$this->_log->debug( $this->get_txt() . "[ registrar_evento ] '$evento' -> [ $metodo ]\n" . var_export($parametros, true), 'toba');
			return call_user_func_array(array($this, $metodo), $parametros);
		} else {
			$this->_log->info($this->get_txt() . "[ registrar_evento ]  El METODO [ $metodo ] no existe - '$evento' no fue atrapado", 'toba');
			return apex_ei_evt_sin_rpta;
		}		
	}

	/**
	 * Define si el evento en cuestion debe ser disparado para el componente
	 * @param toba_ei_componente $componente
	 * @param string $evento
	 * @return boolean
	 */
	private function debe_disparar_evento($componente, $evento)
	{
		//Nuevo esquema de disparo de eventos			
		// se debe disparar unicamente si hubo cambios en los datos del comp., es implicito y esta setedo el disparo selectivo.
		$es_evt_implicito =  $componente->evento($evento)->es_implicito();
		if ($this->_disparo_evento_condicionado_a_datos && $es_evt_implicito) {
			return $componente->hay_cambios();			
		}
		return true;
	}
	//------------------------------------------------
	//--  Eventos Predefinidos------------------------
	//------------------------------------------------
	
  /**
   *  Este evento se invoca por cada control que falla.  
   *  Como el resultado de la ejecucion del control se toma
   *  despues de invocar a este metodo, se puede alterar
   *  desde aqui el comportarmiento del control y su resultado.
	 * @param string $punto_control Punto de control en ejecucion.
	 * @param toba_control $control Referencia al control que falló.
   */
  function evt__falla_punto_control($punto_control, &$control)
  {
  
  }

	/**
	 * Evento predefinido de cancelar, limpia este objeto, y en caso de exisitr, cancela al cn asociado
	 */
	function evt__cancelar()
	{
		$this->_log->debug($this->get_txt() . "[callback][ evt__cancelar ]", 'toba');
		$this->disparar_limpieza_memoria();
		if(isset($this->_cn)){
			$this->_cn->cancelar();			
		}
	}

	/**
	 * Evento predefinido de procesar, en caso de existir el cn le entrega los datos y limpia la memoria
	 */
	function evt__procesar()
	{
		$this->_log->debug($this->get_txt() . "[callback][ evt__procesar ]", 'toba');
		if(isset($this->_cn)){
			$this->disparar_entrega_datos_cn();
			$this->_cn->procesar();
		}
	}

	
	//----------------------------------------------------
	//------------   Manejo de Dependencias  -------------
	//----------------------------------------------------

	/**
	 * Carga las dependencias y las inicializa
	 * @param unknown_type $dependencias
	 * @ignore 
	 */
	protected function inicializar_dependencias( $dependencias )
	{
		toba_asercion::es_array($dependencias,"[Inicializar_dependencias] No se definio la lista de dependencias a inicializar");
		$this->_log->debug( $this->get_txt() . "[ inicializar_dependencias ]\n" . var_export($dependencias, true), 'toba');
		//Parametros a generales
		$parametro["nombre_formulario"] = $this->_nombre_formulario;
		foreach($dependencias as $dep)
		{
			if (isset($this->_dependencias[$dep])) {
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

	/**
	 * Método interno de inicialización de una dependencia
	 * @ignore 
	 */
	protected function inicializar_dependencia($dep, $parametro)
	{
		if( in_array( $dep, $this->_dependencias_inicializadas ) )  return;
		if ($this->_dependencias[$dep] instanceof toba_ci ){
			$this->_dependencias_ci[$dep] = $dep;
			if(isset($this->_cn)){
				$this->_dependencias[$dep]->asignar_controlador_negocio( $this->_cn );
			}
		}
		$this->_dependencias[$dep]->set_controlador($this, $dep); //Se hace antes para que puede acceder a su padre
		$this->_dependencias[$dep]->inicializar($parametro);
		$this->_dependencias_inicializadas[] = $dep;
	}

	/**
	 * Accede a una dependencia del objeto, opcionalmente si la dependencia no esta cargada, la carga
	 *	si la dependencia es un EI y no figura en la lista GI (generacion de interface) dispara el evento de carga!
	 * @param string $id Identificador de la dependencia dentro del objeto actual
	 * @param boolean $cargar_en_demanda En caso de que el objeto no se encuentre cargado en memoria, lo carga
	 * @return toba_componente
	 */
	function dependencia($id, $carga_en_demanda = true)
	{
		$dependencia = parent::dependencia( $id, $carga_en_demanda );
		if (! in_array( $id, $this->_dependencias_inicializadas ) ) {
			$parametro['id'] = $id;
			$parametro['nombre_formulario'] = $this->_nombre_formulario;
			$this->inicializar_dependencia( $id, $parametro );
		}
		//--- A los eis se les debe configurar cuando estan en servicio
		if (	$this->_en_servicio
				&& $this->_dependencias[$id] instanceof toba_ei 
				&& ! $this->dependencia_esta_configurada($id) ) {
			$this->configurar_dep($id);
		}
		return $dependencia;
	}
	

	/**
	 * Devuelve la lista de dependencias que se utlizaron para generar el servicio anterior (atender los eventos actuales)
	 * @ignore 
	 */
	protected function get_dependencias_eventos()
	{
		//Memoria sobre dependencias que fueron a la interface
		if( isset($this->_memoria['pantalla_dep']) ){
			$dependencias = $this->_memoria['pantalla_dep'];
			foreach (array_keys($dependencias) as $id) {
				if(!isset($this->_indice_dependencias[$dependencias[$id]])){
					//--- Cuando la dependencia se agregó dinámicamente en un pedido de página es posible que no se tenga registro en este pedido
					toba::logger()->warning("No se encuentra la dependencia {$dependencias[$id]}, no es posible atender sus eventos.");
					unset($dependencias[$id]);
				}
			}

			
			//Necesito cargar los daos dinamicos?
			//Esto es posible si los EF chequean que su valor se encuentre entre los posibles
			$this->inicializar_dependencias( $dependencias );
			
			//Se ordenan las dependencias: Por ultimo se atienden los cuadros y antes los ML
			//Porque pueden contener eventos a nivel de fila que cambien algun cursor
			//y cambien el procesamiento de los otros eventos
			$cuadros = array();
			$form_ml = array();
			$otros = array();
			foreach ($dependencias as $dep) {
				if ($this->_dependencias[$dep] instanceof toba_ei_cuadro) {
					$cuadros[] = $dep;
				} elseif ($this->_dependencias[$dep] instanceof toba_ei_formulario_ml) {
					$form_ml[] = $dep;
				} else {
					$otros[] = $dep;	
				}
			}
			return array_merge($otros, $form_ml, $cuadros);
		} else {
			return array();
		}
	}
		
	//--------------------------------------------------------
	//--  MANEJO de PANTALLAS  -------------------------------
	//--------------------------------------------------------

	/**
	 * Define la pantalla de eventos (servicio del request anterior)
	 * @ignore 
	 */
	protected function definir_pantalla_eventos()
	{
		//--- La pantalla anterior de servicio ahora se convierte en la potencial pantalla de eventos
		if (isset($this->_memoria['pantalla_servicio'])) {
			$this->_pantalla_id_eventos = $this->_memoria['pantalla_servicio'];
			unset($this->_memoria['pantalla_servicio']);
			$this->_log->debug( $this->get_txt() . "Pantalla de eventos: '{$this->_pantalla_id_eventos}'", 'toba');
		}
	}


	/**
	 * Retorna la pantalla que se muestra al iniciar el componente en la operación
	 * Por defecto retorna la primer pantalla definida en el editor salvo que la rf la oculte
	 * Extender para definir una pantalla distinta a través de un método dinámico
	 * @return string Identificador de la pantalla
	 */
	function get_pantalla_inicial()
	{
		$no_visibles = toba::perfil_funcional()->get_rf_pantallas_no_visibles($this->_id[1]);
		for ($a = 0; $a<count($this->_info_ci_me_pantalla);$a++)	{
			if (! in_array($this->_info_ci_me_pantalla[$a]['pantalla'], $no_visibles)) {
				return $this->_info_ci_me_pantalla[$a]["identificador"];
			}
		}
		throw new toba_error_def('No se encuentra una pantalla libre de restricción funcional para asignar como inicial');
	}
	
	
	/**
	 * Recorre las pantallas en el sentido actual buscando una válida para mostrar
	 */
	protected function ir_a_limitrofe()
	{
		if (!isset($this->_pantalla_id_eventos)) {
			toba::logger()->crit("No se pudo determinar la pantalla anterior, no se encuentra en la memoria sincronizada");
			return $this->get_pantalla_inicial();
		}
		$limitrofes = array_elem_limitrofes($this->_memoria['tabs'], $this->_pantalla_id_eventos);
		return $limitrofes[$this->_wizard_sentido_navegacion];
	}
	
	/**
	 * Retorna true si la navegación por wizard recibio un 'siguiente' en la ultima solicitud
	 * @return boolean
	 */
	protected function wizard_avanza()
	{
		return isset($this->_wizard_sentido_navegacion) && ($this->_wizard_sentido_navegacion == 1);
	}

	/**
	 * Resetea el Cambio de Pantalla, se recupera el id de la pantalla original
	 * @ignore
	 */
	protected function resetear_cambio_pantalla()
	{
		$this->_pantalla_id_servicio = $this->_pantalla_id_eventos;
	}


	//------------------------------------------------
	//--  ETAPA SERVICIO  ----------------------------
	//------------------------------------------------
	
	/**
	 * Momento donde se decide finalmente la pantalla a graficar y se configuran las dependencias
	 * @ignore 
	 */
	function pre_configurar()
	{
		$this->_en_servicio = true;
		//--- Es posible que nadie haya decidido aun la pantalla ,se decide aca
		if (! isset($this->_pantalla_id_servicio)) {
			if (isset( $this->_pantalla_id_eventos )) {
				$this->_pantalla_id_servicio =  $this->_pantalla_id_eventos;
			} else {
				$this->_pantalla_id_servicio = $this->get_pantalla_inicial();
			}
		}		
		
		//--- Configuracion pers. propia		
		$this->conf();
		
		//--- Configuracion pers. pantalla actual
		$this->invocar_callback('conf__'.$this->_pantalla_id_servicio, $this->pantalla());
		$this->pantalla()->post_configurar();		
	}
	
	/**
	 * Se configura una dependencia, se busca un callback `conf__` y si este callback responde cargar estos datos la dependencia
	 * @ignore 
	 */
	protected function configurar_dep($dep)
	{
		if ($this->dependencia_esta_configurada($dep)) {
			throw new toba_error_def("La dependencia '$dep' ya ha sido configurada anteriormente");
		}
		$this->_dependencias_configuradas[] = $dep;		
		//--- Config. por defecto
		$this->_dependencias[$dep]->pre_configurar();
		//--- Config. personalizada
		//ei_arbol($this->_dependencias, $dep);return;
		$rpta = $this->invocar_callback('conf__'.$dep, $this->_dependencias[$dep]);
		//--- Por comodidad y compat.hacia atras, si se responde con algo se asume que es para cargarle datos
		if (isset($rpta) && $rpta !== apex_callback_sin_rpta) {
			$this->_dependencias[$dep]->set_datos($rpta);
		}		
		
		//--- Config. por defecto
		$this->_dependencias[$dep]->post_configurar();
	}
	
	/**
	 * Una dependencia ya ha sido configurada por este CI?
	 * @ignore 
	 */
	protected function dependencia_esta_configurada($id)
	{
		return in_array($id, $this->_dependencias_configuradas);
	}
	
	/**
	 * Ventana para insertar lógica de la configuración del ci y sus dependencias
	 * @ventana 
	 */
	function post_configurar(){}

	/**
	 * Ventana para hacer una configuración personalizada del ci
	 * @ventana
	 */
	protected function conf() {}
	
	/**
	 * Retorna los metadatos de una pantalla específica perteneciente a este ci
	 * @param string $id Identificador de pantalla
	 * @return array
	 */
	protected function get_info_pantalla($id)
	{
		foreach($this->_info_ci_me_pantalla as $info_pantalla) {
			if ($info_pantalla['identificador'] == $id) {
				return $info_pantalla;	
			}
		}
	}
	
	/**
	 * Retorna los objetos asociados a una pantalla especifica perteneciente a este ci
	 * @param string $id Identificador de la pantalla
	 * @return array
	 */
	protected function get_info_objetos_asoc_pantalla($id)
	{
		$obj = array();
		foreach($this->_info_obj_pantalla as $info_pantalla) {
			if ($info_pantalla['identificador_pantalla'] == $id) {
				$obj[] = $info_pantalla;
			}
		}
		return $obj;
	}

	protected function get_info_eventos_pantalla($id)
	{
			$eventos = array();
			foreach($this->_info_evt_pantalla as $info_evento){
				if ($info_evento['identificador_pantalla'] == $id){
					$eventos[$info_evento['identificador_evento']] = $info_evento;
				}
			}
			return $eventos;
	}

	
	/**
	 * Indica si la botonera superior del ci se grafica en la barra superior del item
	 * @return boolean
	 */
	function es_botonera_en_barra_item()
	{
		return isset($this->_info_ci['botonera_barra_item']) && $this->_info_ci['botonera_barra_item']; 
	}	
	
	/**
	 * Retorna la referencia a la pantalla a graficar
	 * Una vez que se invoca este metodo se fija la pantalla para el resto del pedido de pagina
	 * Es importante relegar esta desicion en caso de querer variar la pantalla a mostrar dinamicamente
	 * @return toba_ei_pantalla
	 */
	function pantalla()
	{
		if (! isset($this->_pantalla_servicio)) {

			$this->_log->debug( $this->get_txt() . "Pantalla de servicio: '{$this->_pantalla_id_servicio}'", 'toba');
			$id_pantalla = $this->get_id_pantalla();			
			if(!isset($id_pantalla)) {
				//Se esta consumiendo la pantalla antes de la configuracion,
				//y sin un set_pantalla de por medio: utilizo la misma pantalla de los eventos.
				$id_pantalla = $this->_pantalla_id_eventos;
			}	
			$info_pantalla = $this->get_info_pantalla($id_pantalla);
			$obj_pantalla = $this->get_info_objetos_asoc_pantalla($id_pantalla);
			$evt_pantalla = $this->get_info_eventos_pantalla($id_pantalla);
			$info = array('_info' => $this->_info,
						 '_info_ci' => $this->_info_ci, 
						 '_info_eventos' => $this->_info_eventos,
						 '_info_ci_me_pantalla' => $this->_info_ci_me_pantalla);
			$info['_info_pantalla'] = $info_pantalla;
			$info['_objetos_pantalla'] = $obj_pantalla;
			$info['_eventos_pantalla'] = $evt_pantalla;
			$info['_const_instancia_numero'] = 0;
			if (isset($info_pantalla['subclase_archivo'])) {
				$pm = toba::puntos_montaje()->get_por_id($info_pantalla['punto_montaje']);
				$path = $pm->get_path_absoluto().'/'.$info_pantalla['subclase_archivo'];
				require_once($path);
			}
			$clase = 'toba_ei_pantalla';
			if (isset($info_pantalla['subclase'])) {
				$clase = $info_pantalla['subclase'];
			}
			$this->_pantalla_servicio = new $clase($info, $this->_submit, $this->objeto_js);	
			$this->_pantalla_servicio->set_controlador($this, $id_pantalla);
			$this->_pantalla_servicio->pre_configurar();
			//Se le pasan las notificaciones
			foreach ($this->_notificaciones as $notificacion) {
				$this->_pantalla_servicio->agregar_notificacion($notificacion['mensaje'], $notificacion['tipo']);
			}
			$this->_notificaciones = array();
		}
		return $this->_pantalla_servicio;
	}

	/**
	* Shortcut para acceder a un evento propio (en realidad es de la pantalla)
	* @param string $id Identificador del evento
	* @return toba_evento_usuario 
	*/
	function evento($id)
	{
		return $this->pantalla()->evento($id);
	}

	/**
	 * Agrega un mensaje de notificacion a la pantalla a generar
	 * @param string $mensaje
	 * @param string $tipo Puede ser 'info', 'warning', 'error'
	 */
	function agregar_notificacion($mensaje, $tipo='info')
	{
		if (isset($this->_pantalla_servicio)) {
			$this->_pantalla_servicio->agregar_notificacion($mensaje, $tipo);
		} else {
			$this->_notificaciones[] = array('mensaje' => $mensaje, 'tipo' => $tipo);
		}		
	}	
	
	/**
	 * Cambia la pantalla a utilizar en el servicio actual
	 * El cambio recien sera efectivo cuando se utilize la pantalla con el metodo pantalla()
	 * @param string $id Identificador de la pantalla, tal como se definio en el editor
	 */
	function set_pantalla($id)
	{
		$no_visibles = toba::perfil_funcional()->get_rf_pantallas_no_visibles($this->_id[1]);
	    $ok = false;
	    foreach($this->_info_ci_me_pantalla as $info_pantalla) {
	    	if (!$ok && $info_pantalla['identificador'] == $id) { 
				if (in_array($info_pantalla['pantalla'], $no_visibles)) {
					//-- Restricción funcional pantalla no-visible ------
					throw new toba_error_def($this->get_txt()."No es posible navegar hacia la pantalla '". $id ."' ya que se encuentra oculta por una restricción funcional");
					//--------------								
				} else {
					$ok = true;
				}
	    	}
	    }
	
		if (! $ok) {
			throw new toba_error_def($this->get_txt()."El identificador de pantalla '". $id ."' no está definido en el ci.");
		}
	
		if (isset($this->_pantalla_servicio)) { 
			throw new toba_error_def($this->get_txt()."No es posible cambiar la pantalla a mostrar porque ya ha sido utilizada.");
		}
	    $this->_pantalla_id_servicio	= $id;
	}

	/**
	 * Retorna el id de la pantalla actualmente seleccionada para graficar
	 * @return string
	 */
	protected function get_id_pantalla()
	{
		return $this->_pantalla_id_servicio;	
	}

	/**
	 * Genera el html de la pantalla actual
	 */
	function generar_html()
	{
		$this->pantalla()->generar_html();	
	}
	
	/**
	 * @ignore 
	 */
	function get_consumo_javascript()
	{
		return $this->pantalla()->get_consumo_javascript();
	}
	
	/**
	 * @ignore 
	 * Delega la generacion de js a la pantalla actual
	 */
	function generar_js()
	{
		if (self::$_navegacion_ajax) {
			$this->pantalla()->set_navegacion_ajax(true);
		}
		return $this->pantalla()->generar_js();
	}
	
	//------------------------------------------------------------
	//----------------------  SERVICIO AJAX  ---------------------
	//------------------------------------------------------------
	
	/**
	 * Lanza la ejecucion del metodo especificado por el pedido ajax (si existe)
	 * y comunica la respuesta.
	 * @ignore
	 * @return unknow_type
	 */
	function servicio__ajax()
	{
		$metodo = 'ajax__'.trim(toba::memoria()->get_parametro('ajax-metodo'));
		$metodo = substr($metodo,0,80);
		if (!isset($this->_metodos_ajax) || !in_array($metodo, $this->_metodos_ajax)) {
			throw new toba_error_seguridad("Invocación AJAX incorrecta, el metodo $metodo no existe");
		}
		$parametros = trim(toba::memoria()->get_parametro('ajax-param'));
		$modo = trim(toba::memoria()->get_parametro('ajax-modo'));		
		$respuesta = new toba_ajax_respuesta($modo);
		
		$variable = toba_vinculador::url_a_variable($parametros);		
		$this->$metodo($variable, $respuesta);
		$respuesta->comunicar();
	}

	//---------------------------------------------------------------
	//------------------------ SALIDA Impresion ---------------------
	//---------------------------------------------------------------

	/**
	 * Genera la vista de impresion HTML de la pantalla actual
	 */
	function vista_impresion_html( toba_impresion $salida )
	{
		$this->pantalla()->vista_impresion_html( $salida );
	}
	
	//---------------------------------------------------------------
	//------------------------- SALIDA XML --------------------------
	//---------------------------------------------------------------
	
	/**
	 * Genera el xml del componente y sus hijos
	 * @param boolean $inicial Si es el primer elemento llamado desde vista_xml
	 * @param string $xmlns Namespace para el componente
	 * @return string XML del componente y sus hijos
	 */
	function vista_xml($inicial=false, $xmlns=null)
	{
		if ($xmlns) {
			$this->xml_set_ns($xmlns);
		}
		$xml = $this->xml_get_tag_inicio();
		foreach ($this->get_dependencias() as $dep) 
		{
			if(method_exists($dep, 'vista_xml')) {
				$xml .= $dep->vista_xml($xmlns);
			}
		}

		$xml .= $this->xml_get_tag_fin();
		return $xml;
	}
	
	/**
	 * Genera el tag de inicio del componente
	 * @return string Tag de inicio del componente
	 */
	function xml_get_tag_inicio() {
		$xml = '<'.$this->xml_ns.'ci'.$this->xml_ns_url;
		$xml .= $this->xml_get_att_comunes();
		$xml .= '>';
		$xml .= $this->xml_get_elem_comunes();
		return $xml;
	}

	/**
	 * Genera el tag de cierre del componente
	 * @return string Tag de cierre
	 */
	function xml_get_tag_fin() {
		return '</'.$this->xml_ns.'ci>';
	}
}
?>
