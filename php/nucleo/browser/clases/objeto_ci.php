<?php
require_once("objeto.php");
require_once("nucleo/browser/interface/form.php");
require_once("nucleo/browser/clases/objeto_ei_formulario.php");
require_once("nucleo/browser/clases/objeto_ei_cuadro.php");
define("apex_ci_evento","evt");
define("apex_ci_separador","__");
/*
	COSAS sin RESOLVER
	------------------

		- Hay que dar un servicio para manejar el estado de EIs que tengan una relacion de 1 a 1 con las variables de la sesion
		- Tiene que pensarse un mecanismo estandar de comunicacion en una jerarquia de CIs
		- Entrada de JS para inhabilitar TABS desde el cliente
		
	REFACTORIZACION
	---------------
	
		- EDITOR de CIs:
		----------------

			* Definicion BASICA

				- Si tiene mas de una pantalla, se puede definir un metodo standard de navegacion
					( tabs verticales, tabs horizontales, wizard )

			* Definicion de PANTALLAS
			
				- ei 1) Lista de pantallas: ei_cuadro / ei_formulario_ml (editor basico)
				- ei 2) Editor especifico de la pantalla: ei_formulario
				- ei 3) Asociador de dependencias a pantallas. ei_formulario_ml
					- Tiene una cascada por: tipo de clase -> objeto
					- Si la SUBLACE del CI tiene una definicion de dependencias:
						- Ya hay una fila por cada IDS esperado
						- El padre de la cascada esta seteado de acuerdo a los tipos permitidos
					- Una pantalla podria tener algo distinto, por ejemplo un mensaje

			* Definicion especifica segun SUBCLASE de CI

				 Una subclase del CI tiene que poder agregar un editor propio al tradicional
				( 	puede agregarse un TAB, con un CI que sea que posea el editor de la sublclase 
						-> una clase deberia poder usar un editor comun, pero señalar que CI especifico va a agregar )
					NECESIDAD: 
						uno un CI que sabe relacionarse con un DB_R define cual es el mismo,
						Un ABM define su DB_T, el DAO que le provee el cuadro, etc.

			* Administrador de codigo PHP

				* Vista simplificada de los que esta pasando a nivel EVENTOS (parsear y resumir)
				* Autogenerador de codigo, que cree automaticamente la clase con los eventos necesarios para los EI.
					Tendria que tener en la base la lista de eventos que tengo por EI.

			* Declaracion de eventos del CI?
			* Definicion de dependencias inexitentes que las cree?

		- IMPLETENCION
		--------------
			
			- La ETAPA pasan a ser una PANTALLA
			- A nivel interno un CI tiene 1 o mas pantallas.
				(las llamadas sin "__etapa", son un shortcut a una definida por defecto)

*/
class objeto_ci extends objeto
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
	protected $observadores = array();				// Objetos que observan los eventos de este CI
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
		parent::objeto($id);
		$this->nombre_formulario = "CI_" . $this->id[1] ;//Cargo el nombre del <form>
		$this->submit = "CI_" . $this->id[1] . "_submit";
		$this->recuperar_estado_sesion();		//Cargo la MEMORIA no sincronizada
		$this->cargar_info_dependencias();
		$this->posicion_botonera = "abajo"; //arriba, abajo, ambos
		$this->objeto_js = "objeto_ci_{$id[1]}";		
		//-- PANTALLAS
		//Indice de etapas
		for($a = 0; $a<count($this->info_ci_me_etapa);$a++){
			$this->indice_etapas[ $this->info_ci_me_etapa[$a]["posicion"] ] = $a;
		}
		//Lo que sigue solo sirve para el request inicial, en los demas casos es rescrito
		// por "definir_etapa_gi_pre_eventos" o "definir_etapa_gi_post_eventos"
		$this->set_etapa_gi( $this->get_etapa_inicial() );

	}

	function destruir()
	{
		if( $this->gi ){
			//Guardo INFO sobre la interface generada
			$this->memoria['dependencias_interface'] = $this->dependencias_gi;
			$this->memoria['eventos'] = array_keys($this->eventos);
			//Etapa utilizada para crear la interface
			$this->memoria['etapa_gi'] = $this->etapa_gi;
		}
		//Memorizo la lista de tabs enviados
		if( isset($this->lista_tabs) ){
			$this->memoria['tabs'] = array_keys($this->lista_tabs);
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
		//-- Info BASICA --------------
		$sql["info_ci"]["sql"] = "		SELECT		ev_procesar_etiq		as	ev_procesar_etiq,
													ev_cancelar_etiq		as	ev_cancelar_etiq,
													objetos					as	objetos,
													ancho					as	ancho,			
													alto					as	alto,
													tipo_navegacion			as	tipo_navegacion
											FROM	apex_objeto_mt_me
											WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
											AND	objeto_mt_me='".$this->id[1]."';";
		$sql["info_ci"]["tipo"]="1";
		$sql["info_ci"]["estricto"]="1";
		//-- PANTALLAS --------------
		$sql["info_ci_me_etapa"]["sql"] = "SELECT	posicion			  	as posicion,
													etiqueta			  	as etiqueta,
													descripcion			  	as descripcion,
													objetos				  	as objetos,
													ev_procesar		   		as ev_procesar,
													ev_cancelar				as ev_cancelar
										FROM	apex_objeto_mt_me_etapa
										WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
										AND	objeto_mt_me = '".$this->id[1]."'
										ORDER	BY	posicion;";
		$sql["info_ci_me_etapa"]["tipo"]="x";
		$sql["info_ci_me_etapa"]["estricto"]="1";
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
		$this->dependencias[$dep]->inicializar($parametro);
		$this->dependencias[$dep]->agregar_observador($this);
		if($this->dependencias[$dep] instanceof objeto_ci ){
			//Guardo la clave de memoria de la dependencia para no perder su memoria cuando no se instancie
			$this->dependencias_ci[$dep] = $this->dependencias[$dep]->get_clave_memoria_global();
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
	//--  MANEJO de PANTALLAS  -------------------------------
	//--------------------------------------------------------

	function get_etapa_inicial()
	{
		return $this->info_ci_me_etapa[0]["posicion"];
	}

	function get_etapa_actual()
	{
		return $this->get_pantalla_actual();
	}
	
	function get_pantalla_actual()
	{
		if (isset($_POST[$this->submit])) {
			$submit = $_POST[$this->submit];
			$tab = (strpos($submit, 'cambiar_tab_') !== false) ? str_replace('cambiar_tab_', '', $submit) : false;
		}
		else
			$tab = false;
		
		if($tab !== false) { //Pidio cambiar de tab
			if(in_array($tab, $this->memoria['tabs'])){
				//El usuario selecciono un tab
				return $tab;
			}else{
				$this->log->error($this->get_txt() . "Se solicito un TAB inexistente.");			
				//Error, voy a etapa inicial
				return $this->get_etapa_inicial();
			}
		}elseif(isset( $this->memoria['etapa_gi'] )){
			//El post fue generado por otro componente
			return $this->memoria['etapa_gi'];
		}else{
			//Etapa inicial
			return $this->get_etapa_inicial();
		}
	}

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
			$this->set_etapa_gi( $this->get_etapa_inicial() );
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
				$evento_salida = apex_ci_evento . apex_ci_separador . "salida" . apex_ci_separador . $this->memoria['etapa_gi'];
				//Evento SALIDA
				if(method_exists($this, $evento_salida)){
					$this->$evento_salida();
				}
			}	
			// -[ 2 ]-  Controlo que se pueda ingresar a la etapa propuesta como ACTUAL
			$evento_entrada = apex_ci_evento . apex_ci_separador . "entrada" . apex_ci_separador . $etapa_actual;
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

		//PANTALLA
		$this->definir_etapa_gi_pre_eventos();

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

		//PANTALLA
		$this->definir_etapa_gi_post_eventos();
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
	}

	function evt__procesar()
	{
		$this->log->debug($this->get_txt() . "[ evt__procesar ]");
		$this->disparar_limpieza_memoria();
	}	
	
	//-----------------------------------------------------------	

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
		$metodo_especifico = "get_lista_ei" . apex_ci_separador . $this->etapa_gi;
		if(method_exists($this, $metodo_especifico)){
			return $this->$metodo_especifico();	
		}		
		//Busco la definicion standard para la etapa
		$objetos = trim( $this->info_ci_me_etapa[ $this->indice_etapas[ $this->etapa_gi ] ]["objetos"] );
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
				$this->dependencias[$dep]->cargar_datos( $this->proveer_datos_dependencias($dep) );
				$this->dependencias[$dep]->definir_eventos();
			}
		}
	}	
	//-------------------------------------------------------------------------------

	function proveer_datos_dependencias($dependencia)
	{
		$metodo = apex_ci_evento . apex_ci_separador . $dependencia . apex_ci_separador . "carga";
		if(method_exists($this, $metodo)){
			$this->log->debug($this->get_txt() . "[ cargar_datos_dependencia ] '$dependencia' -> [ $metodo ] ");
			return $this->$metodo();
		}else{
			$this->log->warning($this->get_txt() . "[ cargar_datos_dependencia ] El METODO [ $metodo ] no existe - '$dependencia' no fue cargada");
			return null;
		}
	}
	//-------------------------------------------------------------------------------

	function evt__post_cargar_datos_dependencias()
	//Despues de cargar las dependencias
	{
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
		echo form::abrir($this->nombre_formulario, $vinculo);
		echo "<div align='center'>\n";
		$this->obtener_html();
		echo "</div>\n";
		echo form::cerrar();
		echo js::abrir();
		$this->obtener_javascript();
		echo "cola_mensajes.mostrar({$this->objeto_js});\n";
		echo js::cerrar();
		echo "<br>\n";
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
		}
		$ancho = isset($this->info_ci["ancho"]) ? "width='" . $this->info_ci["ancho"] . "'" : "";
		$alto = isset($this->info_ci["alto"]) ? "height='" . $this->info_ci["alto"] . "'" : "";
		echo "<table $ancho $alto class='objeto-base' id='{$this->objeto_js}_cont'>\n";
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
				$this->generar_botonera();
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
				$this->generar_botonera();
				echo "</td></tr>\n";
			}
		}
		echo "</tbody>\n";
		echo "</table>\n";
		$this->gi = true;
	}

	//-------------------------------------------------------------------------------

	function generar_botonera()
	{
		echo "<table class='tabla-0' align='center' width='100%'>\n";
		echo "<tr><td align='right'>";
		foreach($this->eventos as $id => $evento ) {
			if (!isset($evento['en_botonera']) || $evento['en_botonera']) {
				$tip = '';
				$clase = ( isset($evento['estilo']) && (trim( $evento['estilo'] ) != "")) ? $evento['estilo'] : "abm-input";
				$tab_order = 0;
				$acceso = tecla_acceso( $evento["etiqueta"] );
				$html = $acceso[0]; //Falta concatenar la imagen
				$tecla = $acceso[1];
				$evento_js = eventos::a_javascript($id, $evento);
				$js = "onclick=\"{$this->objeto_js}.set_evento($evento_js);\"";
				echo "&nbsp;" . form::button_html( $this->submit.$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase);
			}
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function get_lista_eventos()
	{
		$eventos = array();
		//Evento PROCESAR
		if($this->info_ci_me_etapa[ $this->indice_etapas[$this->etapa_gi] ]['ev_procesar']) {
			$eventos += eventos::ci_procesar($this->info_ci['ev_procesar_etiq']);
		}
		//Evento CANCELAR
		if($this->info_ci_me_etapa[ $this->indice_etapas[$this->etapa_gi] ]['ev_cancelar'])	{
			$eventos += eventos::ci_cancelar($this->info_ci['ev_cancelar_etiq']);
		}
		// Eventos de TABS
		if(	$this->info_ci['tipo_navegacion'] == "tab_h" 
			|| $this->info_ci['tipo_navegacion'] == "tab_v" )
		{
			foreach ($this->get_lista_tabs() as $id => $tab) {
				$eventos += eventos::ci_cambiar_tab($id);
			}
		}
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
				$this->obtener_html_pantalla_contenido();
				break;				
			case "wizard": 									//*** Wizard (secuencia estricta hacia adelante)
				$this->obtener_html_pantalla_contenido();
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
		if(trim($this->info_ci_me_etapa[ $this->indice_etapas[ $this->etapa_gi ] ]["descripcion"])!=""){
			$descripcion = 	$this->info_ci_me_etapa[ $this->indice_etapas[ $this->etapa_gi ] ]["descripcion"];
			$imagen = recurso::imagen_apl("info_chico.gif",true);
			echo "<div class='txt-info'>$imagen&nbsp;$descripcion</div>\n";
			echo "<hr>\n";
		}
		/*
			Controla la existencia de una funcion que redeclare
			la generacion de una PANTALLA puntual
		*/
		$interface_especifica = "obtener_html_contenido". apex_ci_separador . $this->etapa_gi;
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
	//---- Mecanismos de NAVEGACION
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
			$html = $acceso[0]; //Falta concatenar la imagen
			if(isset($tab['imagen'])) $html = $tab['imagen'] . "&nbsp;&nbsp;" . $html;
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
				echo form::button_html( $this->submit.$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase);
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}
		}
		echo "<td width='90%'  class='tabs-solapa-hueco'>".gif_nulo()."</td>\n";
		echo "</tr>";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function get_lista_tabs()
	//Para inhabilitar algún tab, heredar, llamar a este método y sacar el tab del arreglo resultante
	{
		$tab = array();
		for($a = 0; $a<count($this->info_ci_me_etapa);$a++)
		{
			$id = $this->info_ci_me_etapa[$a]["posicion"];
			$tab[$id]['etiqueta'] = $this->info_ci_me_etapa[$a]["etiqueta"];
			$tab[$id]['tip'] = $this->info_ci_me_etapa[$a]["descripcion"];
			//$tab[$id]['imagen'] = recurso::imagen_apl('doc.gif',true);
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
			$objetos[] = $this->dependencias[$dep]->obtener_javascript();
		}
		$identado = js::instancia()->identar(-1);		
		//Agrega a los objetos hijos
		//ATENCION: Esto no permite tener el mismo formulario instanciado dos veces
		echo "\n";
		foreach ($objetos as $objeto) {
			echo $identado."{$this->objeto_js}.agregar_objeto($objeto);\n";
		}
	}
}
?>