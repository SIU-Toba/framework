<?php
require_once("objeto_ei.php");
require_once("nucleo/browser/interface/form.php");
require_once("nucleo/browser/clases/objeto_ei_formulario.php");
require_once("nucleo/browser/clases/objeto_ei_cuadro.php");

class objeto_ci extends objeto_ei
{
	// General
	protected $cn=null;								// Controlador de negocio asociado
	protected $nombre_formulario;					// privado | string | Nombre del <form> del MT
	protected $submit;								// Boton de SUBMIT
	protected $dependencias_ci_globales = array();	// Lista de todas las dependencias CI instanciadas desde el momento 0
	protected $dependencias_ci = array();			// Lista de dependencias CI utilizadas en el REQUEST
	protected $dependencias_gi;						// Dependencias utilizadas para la generacion de la interface
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
		$this->nombre_formulario = "CI_" . $this->id[1] ;//Cargo el nombre del <form>
		$this->submit = "CI_" . $this->id[1] . "_submit";
		$this->recuperar_estado_sesion();		//Cargo la MEMORIA no sincronizada
		$this->cargar_info_dependencias();
		$this->posicion_botonera = ($this->info_ci['posicion_botonera'] != '') ? $this->info_ci['posicion_botonera'] : 'abajo';
		$this->objeto_js = "objeto_ci_{$id[1]}";		
		//-- PANTALLAS
		//Indice de etapas
		for($a = 0; $a<count($this->info_ci_me_pantalla);$a++){
			$this->indice_etapas[ $this->info_ci_me_pantalla[$a]["identificador"] ] = $a;
		}
		//Lo que sigue solo sirve para el request inicial, en los demas casos es rescrito
		// por "definir_etapa_gi_pre_eventos" o "definir_etapa_gi_post_eventos"
		$this->set_etapa_gi( $this->get_etapa_actual() );

	}
	
	function elemento_toba()
	{
		require_once('api/elemento_objeto_ci.php');
		return new elemento_objeto_ci();
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
	
	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//-- Info BASICA --------------
		$sql["info_ci"]["sql"] = "		SELECT		ev_procesar_etiq		as	ev_procesar_etiq,
													ev_cancelar_etiq		as	ev_cancelar_etiq,
													objetos					as	objetos,
													ancho					as	ancho,			
													alto					as	alto,
													posicion_botonera		as  posicion_botonera,
													tipo_navegacion			as	tipo_navegacion,
													con_toc					as  con_toc
											FROM	apex_objeto_mt_me
											WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
											AND	objeto_mt_me='".$this->id[1]."';";
		$sql["info_ci"]["tipo"]="1";
		$sql["info_ci"]["estricto"]="1";
		//-- PANTALLAS --------------
		$sql["info_ci_me_pantalla"]["sql"] = "SELECT	
													identificador			as identificador,
													etiqueta			  	as etiqueta,
													descripcion			  	as descripcion,
													imagen_recurso_origen	as imagen_recurso_origen,
													imagen					as imagen,
													objetos				  	as objetos,
													eventos					as eventos
									 	FROM	apex_objeto_ci_pantalla
										WHERE	objeto_ci_proyecto='".$this->id[0]."'
										AND	objeto_ci = '".$this->id[1]."'
										ORDER	BY	orden;";
		$sql["info_ci_me_pantalla"]["tipo"]="x";
		$sql["info_ci_me_pantalla"]["estricto"]="1";
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
		if($this->dependencias[$dep] instanceof objeto_ci ){
			$this->dependencias_ci[$dep] = $this->dependencias[$dep]->get_clave_memoria_global();			
			if(isset($this->cn)){
				$this->dependencias[$dep]->asignar_controlador_negocio( $this->cn );
			}
		}		
		$this->dependencias[$dep]->inicializar($parametro);
		$this->dependencias[$dep]->agregar_controlador($this);
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
	// (se utilizo el metodo 'get_lista_ei' y se selecciono un EI que es CI)
	// hay que redeclarar este metodo para que devuelva el conjunto correcto de CIs utilizados
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
	//--  MANEJO de PANTALLAS  -------------------------------
	//--------------------------------------------------------

	function get_pantalla_inicial()
	{
		return $this->info_ci_me_pantalla[0]["identificador"];
	}
	
	/**
	*	@deprecated Desde 0.8.3
	*	@see objeto_ci::get_pantalla_inicial()
	*/	
	function get_etapa_inicial()
	{
		toba::get_logger()->obsoleto("objeto_ci", "get_etapa_inicial", "0.8.3", "Usar get_pantalla_inicial");
		return $this->get_pantalla_inicial();
	}

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
			if ($tab !== false && $this->puede_ir_a_pantalla($tab))
				return $this->ir_a_pantalla($tab);
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
	function get_etapa_actual()
	{
		toba::get_logger()->obsoleto("objeto_ci", "get_etapa_actual", "0.8.3", "Usar get_pantalla_actual");		
		return $this->get_pantalla_actual();
	}
		
	function puede_ir_a_pantalla($tab)
	{
		$evento_mostrar = apex_ei_evento . apex_ei_separador . "puede_mostrar_pantalla";
		if(method_exists($this, $evento_mostrar)){
			return $this->$evento_mostrar($tab);
		}
		return true;
	}
	
	/*
	*  Recorre las pantallas en un sentido buscando una válida para mostrar
	*/
	function ir_a_limitrofe($sentido)
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
	function ir_a_pantalla($tab)
	{
		if(in_array($tab, $this->memoria['tabs'])){
			return $tab;
		}else{
			$this->log->error($this->get_txt() . "Se solicito un TAB inexistente.");			
			//Error, voy a etapa inicial
			return $this->get_etapa_inicial();
		}
	}	
	
	//-------------------------------------------------------------------------------
	/*
	*	Determina la etapa anterior y siguiente a la dada 
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
	function set_etapa_gi($etapa)
	{
		$this->etapa_gi	= $etapa;
	}

	function get_etapa_gi()
	{
		return $this->etapa_gi;	
	}

	function definir_etapa_gi_pre_eventos()
	//Define la etapa de Generacion de Interface del request ANTERIOR
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

	function definir_etapa_gi_post_eventos()
	//Define la etapa de Generacion de Interface correspondiente al procesamiento del evento ACTUAL
	//ATENCION: esto se esta ejecutando despues de los eventos propios... 
	//				puede traer problemas de ejecucion de eventos antes de validar la salida de etapas
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

	protected function disparar_eventos()
	// Se les ordena a las dependencias que gatillen sus eventos
	// Cualquier error que aparezca, sea donde sea, se atrapa en el ultimo nivel.
	// 		Esto esta bien? --> cuando aparece el primer error no se sigan procesando las cosas... solo se puede atrapar un error.
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

	function controlar_eventos_propios()
	//Reconoce que evento del CI se ejecuto
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

	function disparar_evento_propio()
	//Dispara un evento propio
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

	public function registrar_evento($id, $evento) 
	// Se disparan eventos dentro del nivel actual
	// Puede recibir N parametros adicionales
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

	function evt__post_recuperar_interaccion()
	//Despues de recuperar la interaccion con el usuario
	{
		/*
		$this->evt__validar_datos();
		*/
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
	
	function evt__cancelar()
	{
		$this->log->debug($this->get_txt() . "[ evt__cancelar ]");
		$this->disparar_limpieza_memoria();
		if(isset($this->cn)){
			$this->cn->cancelar();			
		}
	}

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
		$this->evt__post_cargar_datos_dependencias();
	}
	//-------------------------------------------------------------------------------

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

	function evt__pre_cargar_datos_dependencias()
	//Antes de cargar las dependencias
	{
		//Existe una definicion especifica para esta etapa?
		$metodo_especifico = "evt__pre_cargar_datos_dependencias" . apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $metodo_especifico)){
			$this->$metodo_especifico();	
		}		
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
				//-- Inyecto DATOS en los EIs, si es que existe un metodo para cargarlos --
				$this->dependencias[$dep]->cargar_datos( $this->proveer_datos_dependencias($dep) );
				$this->dependencias[$dep]->definir_eventos();
			}
		}
	}	
	//-------------------------------------------------------------------------------

	function proveer_datos_dependencias($dependencia)
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
	//-------------------------------------------------------------------------------

	function evt__post_cargar_datos_dependencias()
	//Despues de cargar las dependencias
	{
		//Existe una definicion especifica para esta etapa?
		$metodo_especifico = "evt__post_cargar_datos_dependencias" . apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $metodo_especifico)){
			$this->$metodo_especifico();	
		}		
	}
	//-------------------------------------------------------------------------------

	function obtener_html_base()
	{
		$this->get_info_post_proceso();
		//-[1]- Muestro la cola de mensajes
		//-[2]- Genero la SALIDA
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
		echo "\n<!-- ################################## Inicio CI ( ".$this->id[1]." ) ######################## -->\n\n\n\n";
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
		$this->solicitud->cola_mensajes->mostrar();		
		echo "\n<!-- ###################################  Fin CI  ( ".$this->id[1]." ) ######################## -->\n\n";		
	}
	//-------------------------------------------------------------------------------

	function get_info_post_proceso()
	{
	}
	//-------------------------------------------------------------------------------

	function obtener_html()
	{
		//-->Listener de eventos
		$this->eventos = $this->get_lista_eventos();
		if( count($this->eventos) > 0){
			echo form::hidden($this->submit, '');
			echo form::hidden($this->submit."__param", '');
		}
		$ancho = isset($this->info_ci["ancho"]) ? "width='" . $this->info_ci["ancho"] . "'" : "";
		$alto = isset($this->info_ci["alto"]) ? "height='" . $this->info_ci["alto"] . "'" : "";
		echo "<table $ancho $alto class='objeto-base' align='center' id='{$this->objeto_js}_cont'>\n";
		//--> Barra SUPERIOR
		echo "<tr><td class='celda-vacia'>";
		$this->barra_superior(null,true,"objeto-ci-barra-superior");
		echo "</td></tr>\n";
		echo "<tbody id='cuerpo_{$this->objeto_js}'>\n";
		//--> Botonera
		$con_botonera = $this->hay_botones();
		if($con_botonera){
			if( ($this->posicion_botonera == "arriba") || ($this->posicion_botonera == "ambos") ){
				echo "<tr><td class='abm-zona-botones'>";
				$this->obtener_botones();
				echo "</td></tr>\n";
			}
		}
		//--> Cuerpo del CI
		echo "<tr><td  class='ci-cuerpo' height='100%'>";
		$this->obtener_html_pantalla();
		echo "</td></tr>\n";

		//--> Botonera
		if($con_botonera){
			if( ($this->posicion_botonera == "abajo") || ($this->posicion_botonera == "ambos") ){
				echo "<tr><td class='abm-zona-botones'>";
				$this->obtener_botones();
				echo "</td></tr>\n";
			}
		}
		echo "</tbody>\n";
		echo "</table>\n";
		$this->gi = true;
	}

	//-------------------------------------------------------------------------------
	protected function get_lista_eventos_definidos()
	/*
	*	Obtiene la lista de eventos definidos desde el administrador 
	* 	Se redefine el método para dejar sólo aquellos eventos definidos en esta pantalla
	*/
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
	//-------------------------------------------------------------------------------

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
	//-------------------------------------------------------------------------------

	protected function obtener_html_pantalla_contenido()
	//Genera el HTML de las dependencias
	{
		/*
			Descripcion de la PANTALLA
		*/
		$descripcion = $this->obtener_descripcion_pantalla($this->etapa_gi);
		$es_wizard = $this->info_ci['tipo_navegacion'] == 'wizard';
		if($descripcion !="" || $es_wizard) {
			$imagen = recurso::imagen_apl("info_chico.gif",true);
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
		/*
			Controla la existencia de una funcion que redeclare
			la generacion de una PANTALLA puntual
		*/
		$interface_especifica = "obtener_html_contenido". apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $interface_especifica)){
			$this->$interface_especifica();
		}else{
			/*
				Solicita el HTML de todas las dependencias que forman parte
				de la generacion de la interface
			*/
			$this->obtener_html_dependencias();
		}
	}
	
	function obtener_descripcion_pantalla($pantalla)
	{
		return trim($this->info_ci_me_pantalla[$this->indice_etapas[$pantalla]]["descripcion"]);
	}
	
	//-------------------------------------------------------------------------------
	
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

	
	//-------------------------------------------------------------------------------
	//----  NAVEGACION tipo WIZARD
	//-------------------------------------------------------------------------------

	function wizard_mostrar_toc()
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
	
	//-------------------------------------------------------------------------------
	//----  NAVEGACION con TABS
	//-------------------------------------------------------------------------------
	
	function obtener_tabs_horizontales()
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
			if(isset($tab['imagen'])) 
				$html = recurso::imagen($tab['imagen'], null, null, null, null, null, 'vertical-align: middle;' ).' ';
			$html .= $acceso[0];
			$tecla = $acceso[1];
			$js = "onclick=\"{$this->objeto_js}.set_evento(new evento_ei('cambiar_tab_$id', true, ''));\"";
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
	//-------------------------------------------------------------------------------

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
				$atajo = form::acceso($tecla, $tip);
				echo "<div class='tabs-v-solapa'>";
				echo "<a id='".$this->submit.'_cambiar_tab_'.$id."' href='#' $atajo class='tabs-v-boton' $js>$html</a>";
				echo "</div>";
			}
		}
		echo "<div class='tabs-v-solapa' style='height:99%;'></div>";
	}
	//-------------------------------------------------------------------------------	
	
	function get_lista_tabs()
	//Para inhabilitar algún tab, heredar, llamar a este método y sacar el tab del arreglo resultante
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
		$consumo_js = parent::consumo_javascript_global();
		$consumo_js[] = 'clases/objeto_ci';
		foreach($this->dependencias_gi as $dep){
			$temp = $this->dependencias[$dep]->consumo_javascript_global();
			if(isset($temp))
				$consumo_js = array_merge($consumo_js, $temp);
		}
		return $consumo_js;
	}
	//-------------------------------------------------------------------------------

	function crear_objeto_js()
	{
		$identado = js::instancia()->identado();	
		//Crea le objeto CI
		echo $identado."var {$this->objeto_js} = new objeto_ci('{$this->objeto_js}', '{$this->nombre_formulario}', '{$this->submit}');\n";

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
}
?>
