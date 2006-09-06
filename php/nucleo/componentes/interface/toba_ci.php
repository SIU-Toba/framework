<?php
require_once('toba_ei_pantalla.php');
require_once("nucleo/componentes/interface/toba_ei_formulario.php");
require_once("nucleo/componentes/interface/toba_ei_cuadro.php");
require_once('nucleo/lib/toba_parser_ayuda.php');

/**
 * Controlador de Interface (ci): Componente responsable de manejar las pantallas y sus distintos elementos
 * @package Componentes
 * @subpackage Eis
 */
class toba_ci extends toba_ei
{
	// General
 	protected $prefijo = 'ci';	
	protected $cn=null;								// Controlador de negocio asociado
	protected $dependencias_ci = array();			// Lista de dependencias CI utilizadas en el REQUEST
	protected $dependencias_gi = array();			// Dependencias utilizadas para la generacion de la interface
	protected $dependencias_inicializadas = array();// Lista de dependencias inicializadas
	protected $dependencias_configuradas = array();
	protected $eventos;								// Lista de eventos que expone el CI
	protected $evento_actual;						// Evento propio recuperado de la interaccion
	protected $evento_actual_param;					// Parametros del evento actual
	protected $posicion_botonera;					// Posicion de la botonera en la interface
	// Pantalla
	protected $pantalla_id_eventos;					// Id de la pantalla que se atienden eventos
	private   $pantalla_id_servicio;					// Id de la pantalla a mostrar en el servicio
	protected $pantalla_servicio;					// Comp. pantalla que se muestra en el servicio 
	protected $en_servicio = false;					// Indica que se ha entrado en la etapa de servicios
	protected $ini_operacion = true;				// Indica si la operación recién se inicia
	protected $wizard_sentido_navegacion;			// Indica si el wizard avanza o no
	
	function __construct($id)
	{
		$this->set_propiedades_sesion(array('ini_operacion'));		
		parent::__construct($id);
		$this->nombre_formulario = "formulario_toba" ;//Cargo el nombre del <form>
	}

	function preparar_componente(){}

	function destruir()
	{
		$this->fin();
		if( isset($this->pantalla_servicio) ){
			//Guardo INFO sobre la interface generada
			$this->memoria['pantalla_dep'] = $this->pantalla_servicio->get_lista_dependencias();
			$this->memoria['pantalla_servicio'] = $this->pantalla_id_servicio;
			$this->memoria['tabs'] = array_keys($this->pantalla_servicio->get_lista_tabs());
			$this->eventos_usuario_utilizados = $this->pantalla_servicio->get_lista_eventos_usuario();
			$this->eventos = $this->pantalla_servicio->get_lista_eventos_internos();
		}
		parent::destruir();
	}
	
	/**
	 * Ventana de extensión previa a la destrucción del componente, al final de la atención de los servicios
	 */
	function fin() {}

	function inicializar($parametro=null)
	{
		if(isset($parametro)){
			$this->nombre_formulario = $parametro["nombre_formulario"];
		}
		if ($this->ini_operacion) {
			$this->log->debug($this->get_txt(). "[ ini__operacion ]", 'toba');
			$this->ini__operacion();
			$this->ini_operacion = false;
		}
		$this->ini();
		$this->definir_pantalla_eventos();		
	}

	/**
	 * Ventana de extensión que se ejecuta cuando el componente se inicia en la operación.
	 * Su utilidad recide en por ejemplo inicializar un conjunto de variables de sesion y evitar
	 * el chequeo continuo de las mismas.
	 * Este momento generalmente se corresponde con el inicio del item, aunque existen excepciones:
	 *  - Si el componente es un ci dentro de otro ci, recien se ejecuta cuando entra a la operacion que no necesariamente es al inicio,
	 * 		si por ejemplo se encuentra en la 3er pantalla del ci principal.
	 *  - Si se ejecuta una limpieza de memoria (comportamiento por defecto del evt__cancelar)
	 */
	function ini__operacion() {}
	
	/**
	 * Ventana de extensión que se ejecuta al iniciar el componente en todos los pedidos en los que participa.
	 * Como la ventana es previa a la atención de eventos y servicios es un punto ideal para la configuración global del componente
	 */
	function ini() {}
	
	//--------------------------------------------------------------
	//---------  Manejo de MEMORIA -------------------------------
	//--------------------------------------------------------------
		
	/**
	 * Borra la memoria de todas las dependencias y la propia
	 */
	function disparar_limpieza_memoria()
	{
		$this->log->debug( $this->get_txt() . "[ disparar_limpieza_memoria ]", 'toba');
		foreach($this->get_dependencias_ci() as $dep){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->dependencias[$dep]->disparar_limpieza_memoria();
		}
		$this->evt__limpieza_memoria(array('ini_operacion'));
		$this->log->debug($this->get_txt(). "[ ini__operacion ]", 'toba');		
		$this->ini__operacion();
	}
	
	/**
	 * Borra la memoria de este CI y lo reinicializa
	 */
	function evt__limpieza_memoria($no_borrar=null)
	{
		$this->set_pantalla( $this->get_pantalla_inicial() );
		$this->borrar_memoria();
		$this->eliminar_estado_sesion($no_borrar);
		$this->ini();
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
		$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ]", 'toba');
		$this->evt__get_datos_cn( $modo );
		$deps = $this->get_dependencias_ci();
		foreach( $deps as $dep ){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->log->debug( $this->get_txt() . "[ disparar_obtencion_datos_cn ] ejecutar '$dep'", 'toba');
			$this->dependencias[$dep]->disparar_obtencion_datos_cn( $modo );
		}
	}

	function evt__get_datos_cn( $modo=null )
	{
		//Esta funcion hay que redefinirla en un hijo para OBTENER datos
		$this->log->warning($this->get_txt() . "[ evt__get_datos_cn ] No fue redefinido!");
	}

	//--  SALIDA de DATOS ----

	function disparar_entrega_datos_cn()
	{
		$this->log->debug( $this->get_txt() . "[ disparar_entrega_datos_cn ]", 'toba');
		//DUDA: Validar aca es redundante?
		$this->evt__validar_datos();
		$this->evt__entregar_datos_cn();
		$deps = $this->get_dependencias_ci();
		foreach( $deps as $dep ){
			if( !isset($this->dependencias[$dep]) ){
				$this->inicializar_dependencias(array($dep));
			}
			$this->log->debug( $this->get_txt() . "[ disparar_entrega_datos_cn ] ejecutar '$dep'", 'toba');
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
	
	//------------------------------------------------
	//--  ETAPA EVENTOS   ----------------------------
	//------------------------------------------------
	
	/**
	 * Se disparan los eventos propios y se les ordena a las dependencias que gatillen sus eventos
	 * Cualquier error de usuario que aparezca, sea donde sea, se atrapa en la solicitud
	 * @todo Esto esta bien? --> cuando aparece el primer error no se sigan procesando las cosas... solo se puede atrapar un error.
	 */
	function disparar_eventos()
	{
		$this->log->debug( $this->get_txt() . " disparar_eventos", 'toba');

		//--- Si no hubo servicio anterior, no se atienden eventos
		if (isset($this->pantalla_id_eventos)) {
			$this->controlar_eventos_propios();
			//Los eventos que no manejan dato tienen que controlarse antes
			if( isset($this->memoria['eventos'][$this->evento_actual]) && 
					$this->memoria['eventos'][$this->evento_actual] == apex_ei_evt_no_maneja_datos ) {
				$this->disparar_evento_propio();
			} else {
				//Disparo los eventos de las dependencias
				foreach( $this->get_dependencias_eventos() as $dep) {
					$this->dependencias[$dep]->disparar_eventos();
				}
				$this->disparar_evento_propio();
			}
		} else {
 			$this->log->debug( $this->get_txt() . "No hay señales de un servicio anterior, no se atrapan eventos", 'toba');
		}
		$this->post_eventos();		
		$this->controlar_cambio_pantalla();
	}
	
	/**
	 * Callback que se ejecuta una vez que todos los eventos se han disparado para este objeto
	 */
	protected function post_eventos() {}
	
	/**
	 *	Si existio un cambio explicito de pantalla se notifican las callbacks de entrada-salida
	 */
	protected function controlar_cambio_pantalla()
	{
		$cambio_pantalla_explicito = (isset($this->pantalla_id_servicio) && 
										isset($this->pantalla_id_eventos) &&
				 						$this->pantalla_id_servicio !== $this->pantalla_id_eventos);
		
		//--- Se da la oportunidad de que alguien rechaze el seteo, y vuelva todo para atras
		if ($cambio_pantalla_explicito) { 
			// -[ 1 ]-  Controlo que se pueda salir de la pantalla anterior
			$evento_salida = apex_ei_evento . apex_ei_separador . $this->pantalla_id_eventos . apex_ei_separador . "salida";
			$this->invocar_callback($evento_salida);				

			// -[ 2 ]-  Controlo que se pueda ingresar a la etapa propuesta como ACTUAL
			$evento_entrada = apex_ei_evento . apex_ei_separador . $this->pantalla_id_servicio . apex_ei_separador . "entrada";
			$this->invocar_callback($evento_entrada);
		}
	}

	/**
	 * Reconoce que evento del CI se ejecuto
	 */
	protected function controlar_eventos_propios()
	{
		$this->evento_actual = "";
		if (isset($_POST[$this->submit]) && $_POST[$this->submit] != '') {
			$evento = $_POST[$this->submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if (isset( $this->memoria['eventos'][$evento] )) {
				$this->evento_actual = $evento;
				$this->evento_actual_param = $_POST[$this->submit."__param"];
			} else {
				throw new toba_error('ERROR CI: Se recibio el EVENTO ['.$evento.']. El mismo no fue enviado en el servicio anterior');	
			}
		}
	}

	protected function disparar_evento_propio()
	{
		if($this->evento_actual != "")	{
			$metodo = apex_ei_evento . apex_ei_separador . $this->evento_actual;
			if(method_exists($this, $metodo)){
				//Ejecuto el metodo que implementa al evento
				$this->log->debug( $this->get_txt() . "[ disparar_evento_propio ] '{$this->evento_actual}' -> [ $metodo ]", 'toba');
				$this->$metodo($this->evento_actual_param);
			
				//Comunico el evento al contenedor
				$this->reportar_evento( $this->evento_actual );
			}else{
				$this->log->info($this->get_txt() . "[ disparar_evento_propio ]  El METODO [ $metodo ] no existe - '{$this->evento_actual}' no fue atrapado", 'toba');
			}
		}
		
		//--- El cambio de tab es un evento
		//--- Si se lanzo se determina cual es el candidato (aun falta la aprobacion)
		if (isset($_POST[$this->submit])) {
			$submit = $_POST[$this->submit];
			//Se pidio explicitamente un id de pantalla o navegar atras-adelante?
			$tab = (strpos($submit, 'cambiar_tab_') !== false) ? str_replace('cambiar_tab_', '', $submit) : false;
			if ($tab == '_siguiente' || $tab == '_anterior') {
				$this->wizard_sentido_navegacion = ($tab == '_anterior') ? 0 : 1;
				$this->pantalla_id_servicio = $this->ir_a_limitrofe();
			} else {
				//--- Se pidio un cambio explicito
				if ($tab !== false) {
					if(isset($this->memoria['tabs']) && in_array($tab, $this->memoria['tabs'])){
						$this->pantalla_id_servicio = $tab;
					}else{
						toba::logger()->crit("No se pudo determinar los tabs anteriores, no se encuentra en la memoria sincronizada");
						//Error, voy a la pantalla inicial
						$this->pantalla_id_servicio =  $this->get_pantalla_inicial();
					}
				}
			}
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
		if (method_exists($this, $metodo)) {
			$this->log->debug( $this->get_txt() . "[ registrar_evento ] '$evento' -> [ $metodo ]\n" . var_export($parametros, true), 'toba');
			return call_user_func_array(array($this, $metodo), $parametros);
		} else {
			$this->log->info($this->get_txt() . "[ registrar_evento ]  El METODO [ $metodo ] no existe - '$evento' no fue atrapado", 'toba');
			return apex_ei_evt_sin_rpta;
		}
	}	


	//------------------------------------------------
	//--  Eventos Predefinidos------------------------
	//------------------------------------------------
	/**
	 * Validar el estado interno, dispara una excepcion si falla
	 */
	function evt__validar_datos() {}

	/**
	 * Evento predefinido de cancelar, limpia este objeto, y en caso de exisitr, cancela al cn asociado
	 */
	function evt__cancelar()
	{
		$this->log->debug($this->get_txt() . "[ evt__cancelar ]", 'toba');
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
		$this->log->debug($this->get_txt() . "[ evt__procesar ]", 'toba');
		if(isset($this->cn)){
			$this->disparar_entrega_datos_cn();
			$this->cn->procesar();
		}
		$this->disparar_limpieza_memoria();
	}	

	
	//----------------------------------------------------
	//------------   Manejo de Dependencias  -------------
	//----------------------------------------------------

	function inicializar_dependencias( $dependencias )
	//Carga las dependencias y las inicializar
	{
		asercion::es_array($dependencias,"No hay dependencias definidas");
		$this->log->debug( $this->get_txt() . "[ inicializar_dependencias ]\n" . var_export($dependencias, true), 'toba');
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
		if( in_array( $dep, $this->dependencias_inicializadas ) )  return;
		if ($this->dependencias[$dep] instanceof toba_ci ){
			$this->dependencias_ci[$dep] = $this->dependencias[$dep]->get_clave_memoria_global();			
			if(isset($this->cn)){
				$this->dependencias[$dep]->asignar_controlador_negocio( $this->cn );
			}
		}
		$this->dependencias[$dep]->set_controlador($this, $dep); //Se hace antes para que puede acceder a su padre
		$this->dependencias[$dep]->inicializar($parametro);
		$this->dependencias_inicializadas[] = $dep;
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
		if (! in_array( $id, $this->dependencias_inicializadas ) ) {
 			if (  $dependencia instanceof toba_ei ) {
				$parametro['id'] = $id;
				$parametro['nombre_formulario'] = $this->nombre_formulario;
				$this->inicializar_dependencia( $id, $parametro );
			}
		}
		//--- A los eis se les debe configurar cuando estan en servicio
		if (	$this->en_servicio
				&& $this->dependencias[$id] instanceof toba_ei 
				&& ! $this->dependencia_esta_configurada($id) ) {
			$this->configurar_dep($id);
		}
		return $dependencia;
	}
	
	/**
	 * @see dependencia
	 */
	function dep($id, $carga_en_demanda = true)
	{
		return $this->dependencia($id, $carga_en_demanda);
	}
	
	/**
	 * Devuelve la lista de dependencias que se utlizaron para generar el servicio anterior (atender los eventos actuales)
	 */
	protected function get_dependencias_eventos()
	{
		//Memoria sobre dependencias que fueron a la interface
		if( isset($this->memoria['pantalla_dep']) ){
			$dependencias = $this->memoria['pantalla_dep'];
			//Necesito cargar los daos dinamicos?
			//Esto es posible si los EF chequean que su valor se encuentre entre los posibles
			$this->inicializar_dependencias( $dependencias );
			return $dependencias;
		}else{
			return array();
		}
	}
		
	//--------------------------------------------------------
	//--  MANEJO de PANTALLAS  -------------------------------
	//--------------------------------------------------------

	/**
	 * Define la pantalla de eventos (servicio del request anterior)
	 */
	protected function definir_pantalla_eventos()
	{
		//--- La pantalla anterior de servicio ahora se convierte en la potencial pantalla de eventos
		if (isset($this->memoria['pantalla_servicio'])) {
			$this->pantalla_id_eventos = $this->memoria['pantalla_servicio'];
			unset($this->memoria['pantalla_servicio']);
			$this->log->debug( $this->get_txt() . "Pantalla de eventos: '{$this->pantalla_id_eventos}'", 'toba');			
		}
	}


	function get_pantalla_inicial()
	{
		return $this->info_ci_me_pantalla[0]["identificador"];
	}
	
	
	/**
	 * Recorre las pantallas en el sentido actual buscando una válida para mostrar
	 */
	protected function ir_a_limitrofe()
	{
		if (!isset($this->pantalla_id_eventos)) {
			toba::logger()->crit("No se pudo determinar la pantalla anterior, no se encuentra en la memoria sincronizada");
			return $this->get_pantalla_inicial();
		}
		$limitrofes = array_elem_limitrofes($this->memoria['tabs'], $this->pantalla_id_eventos);
		return $limitrofes[$this->wizard_sentido_navegacion];
	}
	
	/**
	 * Retorna true si la navegación por wizard recibio un 'siguiente' en la ultima solicitud
	 */
	protected function wizard_avanza()
	{
		return isset($this->wizard_sentido_navegacion) && ($this->wizard_sentido_navegacion == 1);
	}

	//------------------------------------------------
	//--  ETAPA SERVICIO  ----------------------------
	//------------------------------------------------
	
	function pre_configurar()
	{
		$this->en_servicio = true;
		//--- Es posible que nadie haya decidido aun la pantalla ,se decide aca
		if (! isset($this->pantalla_id_servicio)) {
			if (isset( $this->pantalla_id_eventos )) {
				$this->pantalla_id_servicio =  $this->pantalla_id_eventos;
			} else {
				$this->pantalla_id_servicio = $this->get_pantalla_inicial();
			}
		}		
		
		//--- Configuracion pers. propia		
		$this->conf();
		
		//--- Configuracion pers. pantalla actual
		$this->invocar_callback('conf__'.$this->pantalla_id_servicio, $this->pantalla());
		$this->pantalla()->post_configurar();		
	}
	
	protected function configurar_dep($dep)
	{
		if ($this->dependencia_esta_configurada($dep)) {
			throw new toba_error("La dependencia '$dep' ya ha sido configurada anteriormente");
		}
		$this->dependencias_configuradas[] = $dep;		
		//--- Config. por defecto
		$this->dependencias[$dep]->pre_configurar();
		//--- Config. personalizada
		//ei_arbol($this->dependencias, $dep);return;
		$rpta = $this->invocar_callback('conf__'.$dep, $this->dependencias[$dep]);
		//--- Por comodidad y compat.hacia atras, si se responde con algo se asume que es para cargarle datos
		if (isset($rpta) && $rpta !== apex_callback_sin_rpta) {
			$this->dependencias[$dep]->set_datos($rpta);
		}		
		
		//--- Config. por defecto
		$this->dependencias[$dep]->post_configurar();
	}
	
	function dependencia_esta_configurada($id)
	{
		return in_array($id, $this->dependencias_configuradas);
	}
	
	function post_configurar(){}

	/**
	 * Ventana para hacer una configuración personalizada del ci
	 */
	protected function conf() {}
	
	protected function get_info_pantalla($id)
	{
		foreach($this->info_ci_me_pantalla as $info_pantalla) {
			if ($info_pantalla['identificador'] == $id) {
				return $info_pantalla;	
			}
		}
	}

	/**
	 * @return toba_ei_pantalla
	 */
	function pantalla()
	{
		if (! isset($this->pantalla_servicio)) {

			$this->log->debug( $this->get_txt() . "Pantalla de servicio: '{$this->pantalla_id_servicio}'", 'toba');
			require_once('toba_ei_pantalla.php');
			$id_pantalla = $this->get_id_pantalla();			
			if(!isset($id_pantalla)) {
				//Se esta consumiendo la pantalla antes de la configuracion,
				//y sin un set_pantalla de por medio: utilizo la misma pantalla de los eventos.
				$id_pantalla = $this->pantalla_id_eventos;
			}	
			$info_pantalla = $this->get_info_pantalla($id_pantalla);
			$info = array('info' => $this->info,
						 'info_ci' => $this->info_ci, 
						 'info_eventos' => $this->info_eventos,
						 'info_ci_me_pantalla' => $this->info_ci_me_pantalla);
			$info['info_pantalla'] = $info_pantalla;
			
			if (isset($info_pantalla['subclase_archivo'])) {
				require_once($info_pantalla['subclase_archivo']);
			}
			$clase = 'toba_ei_pantalla';
			if (isset($info_pantalla['subclase'])) {
				$clase = $info_pantalla['subclase'];
			}
			$this->pantalla_servicio = new $clase($info, $this->submit, $this->objeto_js);	
			$this->pantalla_servicio->set_controlador($this, $id_pantalla);
			$this->pantalla_servicio->pre_configurar();
		}
		return $this->pantalla_servicio;
	}

	/**
	*	Shortcut para acceder a un evento propio (en realidad es de la pantalla)
	*/
	function evento($id)
	{
		return $this->pantalla()->evento($id);
	}
	
	protected function set_pantalla($id)
	{
		if (isset($this->pantalla_servicio)) {
			throw new toba_error($this->get_txt()."No es posible cambiar la pantalla a mostrar porque ya ha sido utilizada.");
		}
		$this->pantalla_id_servicio	= $id;
	}

	protected function get_id_pantalla()
	{
		return $this->pantalla_id_servicio;	
	}

	function generar_html()
	{
		$this->pantalla()->generar_html();	
	}
	
	function get_consumo_javascript()
	{
		return $this->pantalla()->get_consumo_javascript();
	}
	
	function generar_js()
	{
		return $this->pantalla()->generar_js();
	}
	
}
?>