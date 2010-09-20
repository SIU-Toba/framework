<?php
define('apex_callback_sin_rpta', 'apex_callback_sin_rpta');
define("apex_datos_clave_fila","x_dbr_clave");			//Clave interna de los componentes tabulares, por compatibilidad es igual.
define('apex_ei_analisis_fila', 'apex_ei_analisis_fila');   //Id de la columna utilizada para el resultado del analisis de una fila
define("apex_ei_evento","evt");
define("apex_ei_separador","__");
define("apex_db_registros_clave","x_dbr_clave");			//Clave interna de los DB_REGISTROS
define('apex_ei_evt_sin_rpta', 'apex_ei_evt_sin_rpta');
define('apex_ei_evt_maneja_datos', 1);
define('apex_ei_evt_no_maneja_datos', -1);

/**
 * Padre de todas las clases que definen componentes
 * @package Componentes
 * @wiki Referencia/Objetos
 */
abstract class toba_componente
{
	static protected $_modo_memoria_compatible = false;		//Dos componentes con el mismo id creados en un request reusan la misma celda de memoria?
	protected $_solicitud;
	protected $_log;
	protected $_id;
	protected $_info;
	protected $_info_dependencias;						//Definicion de las dependencias
	protected $_indice_dependencias;					//Indice que mapea las definiciones de las dependencias con su
	protected $_dependencias_indice_actual = 0;	
	protected $_lista_dependencias = array();					//Lista de dependencias disponibles
	protected $_dependencias = array();							//Array de sub-OBJETOS
	protected $_memoria;
	protected $_memoria_existencia_previa = false;
	protected $_observaciones;
	protected $_canal;										// Canal por el que recibe datos 
	protected $_canal_recibidos;							// Datos recibidos por el canal
	protected $_estado_proceso;							// interno | string | "OK","ERROR","INFRACCION"
	protected $_id_ses_g;								//ID global para la sesion
	protected $_id_ses_grec;								//ID global para la sesion
	protected $_id_en_controlador;						//Id relativo al controlador padre
	protected $_definicion_partes;						//indica el nombre de los arrays de metadatos que posee el objeto
	protected $_exportacion_archivo;
	protected $_exportacion_path;
	protected $_propiedades_sesion = array();			//Arreglo de propiedades que se persisten en sesion
	protected $_parametros;								// Parametros de inicializacion provistos por el controlador
	protected $_inicializado = false;	

	/**
	 * Contiene el componente controlador o padre del componente actual
	 * @var toba_ci
	 */
	protected $controlador;
		
	function __construct( $definicion )
	{
		//--- Compatibilidad con el metodo anterior de mantener cosas en sesion
		$this->definir_propiedades_sesion();
		// Compatibilidad hacia atras en el ID
		$this->_id[0] = $definicion['_info']['proyecto'];
		$this->_id[1] = $definicion['_info']['objeto'];
		//Cargo las variables internas que forman la definicion
		foreach (array_keys($definicion) as $parte) {
			$this->_definicion_partes[] = $parte;
			$this->$parte = $definicion[$parte];
		}
		$this->_solicitud = toba::solicitud();
		$this->_log = toba::logger();
		//Recibi datos por el CANAL?
		$this->_canal = apex_hilo_qs_canal_obj . $this->_id[1];
		$this->_canal_recibidos = toba::memoria()->get_parametro($this->_canal);
		$this->_id_ses_g = "obj_" . $this->_id[1];
		$this->_id_ses_grec = "obj_" . $this->_id[1] . "_rec";
		if (! self::$_modo_memoria_compatible) {
			$this->_id_ses_g .= $definicion['_const_instancia_numero'];
			$this->_id_ses_grec .= $definicion['_const_instancia_numero'];
		}		
		$this->set_controlador($this);												//Hasta que nadie lo explicite, yo me controlo solo
		//Manejo transparente de memoria
		$this->cargar_memoria();			//RECUPERO Memoria sincronizada
		$this->cargar_info_dependencias();
		//$this->_log->debug("CONSTRUCCION: {$this->_info['clase']}({$this->_id[1]}): {$this->get_nombre()}", 'toba');

	}
	
	
	function __sleep()
	{
		throw new toba_error($this->get_txt()."Los componentes toba no se pueden serializar (eg. guardar en sesion) en forma directa. 
								Si esta intentando serializar un objeto que posee una referencia a un componente toba entre sus propiedades, puede hacerlo funcionar heredando
									de la clase toba_serializar_propiedades.");
	}
	
	/**
	 * Alternativa para que la reutilización de un mismo componente en un mismo request se siga comportando como antes de [3050], es decir el ultimo creado pisa la memoria del 1ero.
	 */
	static function set_modo_compatible_manejo_sesion($compatible)
	{
		self::$_modo_memoria_compatible = $compatible;
	}
	
	/**
	 * Método interno para iniciar el componente una vez construido
	 * @ignore 
	 */
	function inicializar($parametros=array())
	{
		$this->_inicializado = true;
		$this->recuperar_estado_sesion();	//RECUPERO Memoria desincronizada		
		$this->_parametros = $parametros;
		$this->ini();
	}
	
	/**
	 * Ventana de extensión que se ejecuta al iniciar el componente en todos los pedidos en los que participa.
	 * Como la ventana es previa a la atención de eventos y servicios es un punto ideal para la configuración global del componente
	 * @ventana
	 */
	function ini() {}

	/**
	 * Destructor del componente
	 */	
	function destruir()
	{
		//Persisto informacion
		$this->memorizar();						//GUARDO Memoria sincronizada
		$this->guardar_estado_sesion();		//GUARDO Memoria dessincronizada
		//Llamo a los destructores de los OBJETOS anidados
		foreach(array_keys($this->_dependencias) as $dependencia){
			$this->_dependencias[$dependencia]->destruir();
		}
	}

	/**
	 * @ignore 
	 */	
	function get_clave_memoria_global()
	{
		return $this->_id_ses_grec;
	}

	/**
	 * Shortcut de {@link toba_logger::debug() toba::logger()->debug} que incluye información básica del componente actual
	 */
	protected function log($txt)
	{
		toba::logger()->debug($this->get_txt() . get_class($this). ": " . $txt, 'toba');
	}
	
	/**
	 * @ignore
	 */
	function get_txt()
	{
		return "componente(".$this->_id[1]."): ";	 //- $this->_id_en_controlador
	}

	/**
	 * Retorna el nombre del componente según se definió en el editor
	 * @return string
	 */
	function get_nombre()
	{
		return $this->_info['nombre'];
	}

	/**
	 * Retorna el título del componente (nombre visible al usuario)
	 * @return string
	 */
	function get_titulo()
	{
		return $this->_info['titulo'];
	}

	/**
	 * Retorna el identificador del componente
	 * @return array
	 */
	function get_id()
	{
		return $this->_id;	
	}
	
	
	/**
	 * @return boolean
	 */
	function inicializado()
	{
		return $this->_inicializado;
	}

	/**
	 * Retorna un parámetro estático definido en las prop. básicas del componente
	 * @param string $parametro Puede ser a,b,c,d,e,f
	 */
	function get_parametro($parametro)
	{
		return $this->_info['parametro_'.$parametro];
	}	
	
	/**
	 * Retorna la referencia al componente padre o contenedor del actual, generalmente un ci
	 * @return toba_ci
	 */
	function controlador()
	{
		return $this->controlador;	
	}
	
	/**
	 * Metodo generico de invocar callbacks en el propio objeto
	 *
	 * @param string $metodo Nombre completo del metodo a invocar
	 * @return mixed apex_callback_sin_rpta en caso que no se encuentre el callback, sino la respuesta del metodo
	 */
	protected function invocar_callback($metodo)
	{
		$parametros	= func_get_args();
		array_splice($parametros, 0 , 1);
		if(method_exists($this, $metodo)){
			$this->_log->debug( $this->get_txt() . "[ callback ] '$metodo'", 'toba');
			return call_user_func_array(array($this, $metodo), $parametros);
		}else{
			$this->_log->info($this->get_txt() . "[ callback ] '$metodo' no fue atrapado", 'toba');
			return apex_callback_sin_rpta;
		}
	}
		
	//-------------------------------------------------------------------------------
	//-----------------   Mensajes al usuario        --------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Retorna un mensaje asociado al componente
	 *
	 * @param mixed $indice Indice del mensaje en el componente
	 * @param mixed $parametros Parámetros posicionales a ser reemplazados en el mensaje (puede ser uno solo o un array)
	 * @return string Mensaje parseado
	 * @see toba_mensajes
	 */
	function get_mensaje($indice, $parametros=null)
	{
		//Busco el mensaje del OBJETO
		if ($mensaje = toba::mensajes()->get_componente($this->_id[1], $indice, $parametros)) {
			return $mensaje;	
		} else {
			//El objeto no tiene un mensaje con el indice solicitado,
			//Busco el INDICE global
			return toba::mensajes()->get($indice, $parametros);
		}
	}

	/**
	 * Informa un mensaje al usuario
	 *
	 * @param string $mensaje Mensaje a mostrar
	 * @param string $nivel Severidad: info o error
	 * 
	 * @see toba_mensajes
	 * @see toba_notificacion
	 */
	function informar_msg($mensaje, $nivel=null)
	{
		toba::notificacion()->agregar($mensaje,$nivel);	
	}

	/**
	 * Informa un mensaje predefinido al usuario, usando toba::notificacion() y toba::mensajes()
	 *
	 * @param mixed $indice Indice del mensaje predefinido
	 * @param mixed $parametros Parámetros posicionales a ser reemplazados en el mensaje (puede ser uno solo o un array)
	 * @param string $nivel Severidad: info o error
	 * 
 	 * @see toba_mensajes
	 * @see toba_notificacion
	 */
	function informar($indice, $parametros=null,$nivel=null)
	{
		$this->informar_msg($this->get_mensaje($indice, $parametros), $nivel);
	}

	//---------------------------------------------------------------
	//-----------------    MEMORIA   --------------------------------
	//---------------------------------------------------------------
//La memoria es una array que se hace perdurable a travez del HILO
//Las clases que lo usen solo tienen generar las claves que necesiten dentro de este (ej: $this->_memoria["una_cosa"])
//y despues llamar a los metodos "memorizar" para guardarla en el HILO y "cargar_memoria" para recuperarlo
//Preg: Por que no se usa el indice 0 en la clave del OBJETO?
//Res: proque no se pueden cargar objetos de dos proyectos en la misma solicitud

	/**
	 * Persiste el array '$this->_memoria' para utilizarlo en la proxima invocacion del objeto
	 * @ignore 
	 */
	function memorizar()
	{
		if(isset($this->_memoria)){
			toba::memoria()->set_dato_sincronizado("obj_".$this->_id[1],$this->_memoria);
		}
	}
	
	/**
	 * Recupera la memoria que dejo una instancia anterior del objeto. (Setea $this->_memoria)
	 * @ignore
	 */
	function cargar_memoria()
	{
		if($this->_memoria = toba::memoria()->get_dato_sincronizado("obj_".$this->_id[1])){
			$this->_memoria_existencia_previa = true;
		}
	}

	/**
	 * Controla la existencia de la memoria
	 * SI la memoria no se cargo se corta la ejecucion y despliega un mensaje
	 * @ignore 
	 */
	function controlar_memoria()
	{
		if ((!isset($this->_memoria)) || (is_null($this->_memoria))){
			throw new toba_error_seguridad("Error cargando la MEMORIA del OBJETO. abms[". ($this->_id[1]) ."]");
		}
	}

	/**
	 * Elimina toda la memoria interna actual y de pedidos anteriores
	 */
	function borrar_memoria()
	{
		unset($this->_memoria);
		toba::memoria()->set_dato_sincronizado("obj_".$this->_id[1],null);
	}
	
	//-------------------------------------------------------------------------------
	//-----------------   Memoria de propiedades     --------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @deprecated Usar $this->set_propiedades_sesion
	 */
	function mantener_estado_sesion()
	{
		return array();
	}
	
	/**
	 * Determina las propiedades que se almacenaran en sesion
	 * @ignore 
	 */
	protected function definir_propiedades_sesion()
	{
		//--- Compat. hacia atras
		$props = $this->mantener_estado_sesion();
		if (! empty($props)) {
			$this->set_propiedades_sesion($props);
		}
		//--- Metodo de descubrir propiedades que empiezen con s__
		$props = reflexion_buscar_propiedades($this, 's__');
		if (! empty($props)) {
			$this->set_propiedades_sesion($props);
		}
	}

	/**
	 * Fuerza a persistir en sesion ciertas propiedades internas 
	 * @param array $props Arreglo que contiene los nombres de las propiedades
	 */
	protected function set_propiedades_sesion($props)
	{
		$this->_propiedades_sesion = array_merge($this->_propiedades_sesion, $props);
	}

	/**
	 * Carga en la clase actual los valores de las propiedades almacenadas en sesion
	 * @ignore 
	 */
	protected function recuperar_estado_sesion()
	{
		if (toba::memoria()->existe_dato_operacion($this->_id_ses_grec)) {
			//Recupero las propiedades de la sesion
			$temp = toba::memoria()->get_dato_operacion($this->_id_ses_grec);
			$serializados = isset($temp["toba__indice_objetos_serializados"]) ? $temp["toba__indice_objetos_serializados"] : array();
			
			foreach(array_keys($temp) as $propiedad) {
				if (in_array($propiedad, $serializados)) {
					$this->$propiedad = unserialize($temp[$propiedad]);
				} else {
					//Es un elemento corriente
					$this->$propiedad = $temp[$propiedad];
				}
			}
			if (! empty($serializados)) {
				unset($temp['toba__indice_objetos_serializados']);
			}
		}
	}
	
	/**
	 * Guarda en sesion aquellas propiedades del componente marcadas anteriormente como pertenecientes a sesion
	 * @ignore 
	 */
	function guardar_estado_sesion()
	{
		//Busco las propiedades que se desea persistir entre las sesiones
		if(count($this->_propiedades_sesion)>0){
			for($a=0;$a<count($this->_propiedades_sesion);$a++){
				//Existe la propiedad
				$nombre_prop = $this->_propiedades_sesion[$a];
				if(isset($this->$nombre_prop)) {
					//Si la propiedad es un array que posee objetos, lo serializo
					//a mano para no tener problemas con la desserializacion automatica de la sesion
					//que requiere todos los includes del proyecto antes del session start
					if (is_array($this->$nombre_prop)) {
						$posee_objetos = array_posee_objetos($this->$nombre_prop);
					} else {
						$posee_objetos = false;
					}
					if (is_object($this->$nombre_prop) || $posee_objetos) {
						$temp[$this->_propiedades_sesion[$a]] = serialize($this->$nombre_prop);
						//Dejo la marca de que serialize un OBJETO.
						$temp["toba__indice_objetos_serializados"][] = $this->_propiedades_sesion[$a];
					} else {
						$temp[$this->_propiedades_sesion[$a]] = $this->$nombre_prop;
					}
				}
			}
			if(isset($temp)) {
				$temp['toba__descripcion_objeto'] = '['. get_class($this). '] ' . $this->_info['nombre'];
				toba::memoria()->set_dato_operacion($this->_id_ses_grec, $temp);
			} else {
				//Si existia y las propiedades pasaron a null, hay que borrarlo
				toba::memoria()->eliminar_dato_operacion($this->_id_ses_grec);
			}
		}
	}

	/**
	 * Elimina de la sesion las propiedades de este componente, a su vez pone en null estas propiedades en este objeto
	 * @param array $no_eliminar Excepciones, propiedades que no se van a poner en null
	 */
	function eliminar_estado_sesion($no_eliminar=null)
	{
		if(!isset($no_eliminar))$no_eliminar=array();
		for($a=0;$a<count($this->_propiedades_sesion);$a++){
			if(!in_array($this->_propiedades_sesion[$a], $no_eliminar)){
				$nombre_prop = $this->_propiedades_sesion[$a];
				unset($this->$nombre_prop);
			}
		}
		toba::memoria()->eliminar_dato_operacion($this->_id_ses_grec);
	}
	
	/**
	 * Construye un arreglo con los valores de todas las propiedades a almacenarse en sesion
	 * @return array Arreglo(propiedad => valor)
	 */
	function get_estado_sesion()
	{
		if(count($this->_propiedades_sesion)>0){
			$propiedades = get_object_vars($this);
			for($a=0;$a<count($this->_propiedades_sesion);$a++){
				//Existe la propiedad
				if(in_array($this->_propiedades_sesion[$a],$propiedades)){
					//Si la propiedad no es NULL
					$nombre_prop = $this->_propiedades_sesion[$a];
					if (isset( $this->$nombre_prop) ) {
						$temp[$this->_propiedades_sesion[$a]] = $this->$nombre_prop;
					}
				}
			}
			if(isset($temp)){
				return $temp;
			}
		}
	}

	//-------------------------------------------------------------------
	//-----------------   DEPENDENCIAS   --------------------------------
	//-------------------------------------------------------------------

	/**
	 * Determina que el componente actual es controlado por otro
	 * @param toba_componente $controlador Padre o contenedor de este componente
	 * @param string $id_en_padre Id de este componente en el padre (conocido como dependencia)
	 */
	function set_controlador($controlador, $id_en_padre=null)
	{
		$this->controlador = $controlador;
		$this->_id_en_controlador = $id_en_padre;
		if (isset($this->objeto_js)) {
			$this->objeto_js .= '_'.$id_en_padre;
			$this->_submit .= '_'.$id_en_padre;
		}
	}	
	
	/**
	 * Arma un hash interno de las dependencias, utilizo durante la construccion
	 * @ignore 
	 */
	protected function cargar_info_dependencias()
	{
		if (isset($this->_info_dependencias)) {
			for($a=0;$a<count($this->_info_dependencias);$a++){
				$this->_indice_dependencias[$this->_info_dependencias[$a]["identificador"]] = $a;//Columna de informacion donde esta la definicion
				$this->_lista_dependencias[] = $this->_info_dependencias[$a]["identificador"];
			}
		}
	}
	
	/**
	 * Devuelve la informacion correspondiente a una dependencia
	 * @ignore 
	 */
	protected function get_info_dependencia($id)
	{
		if ($this->existe_dependencia($id)) {
			return $this->_info_dependencias[$this->_indice_dependencias[$id]];
		}
	}

	/**
	 * Accede a una dependencia del objeto, opcionalmente si la dependencia no esta cargada, la carga
	 *
	 * @param string $id Identificador de la dependencia dentro del objeto actual
	 * @param boolean $cargar_en_demanda En caso de que el objeto no se encuentre cargado en memoria, lo carga
	 * @return toba_componente
	 */
	function dependencia($id, $carga_en_demanda = true)
	{
		if (! $this->dependencia_cargada($id) && $carga_en_demanda) {
			$this->cargar_dependencia($id);
		}
		return $this->_dependencias[$id];
	}	
	
	/**
	 * @see dependencia
	 * @return toba_componente
	 */
	function dep($id, $carga_en_demanda = true)
	{
		return $this->dependencia($id, $carga_en_demanda);
	}	
	
	/**
	 * Agregar dinámicamente una dependencia al componente actual
	 *
	 * @param string $identificador ID. de la dependencia
	 * @param string $proyecto 
	 * @param string $objeto ID. de componente en el proyecto
	 */
	function agregar_dependencia( $identificador, $proyecto, $objeto )
	{
		$sig = count($this->_info_dependencias);
		$this->_info_dependencias[$sig] = toba::proyecto()->get_definicion_dependencia($objeto, $proyecto);
		$this->_info_dependencias[$sig]['identificador'] = $identificador;
		$this->_indice_dependencias[$identificador] = $sig;
		$this->_lista_dependencias[] = $identificador;	
	}

	/**
	 * Agregar dinámicamente una dependencia al componente actual utilizando
	 *
	 * @param string $identificador ID. del componente
	 * @param string $proyecto 
	 * @param string $tipo Tipo de componente
	 */
	function agregar_dependencia_por_indice( $identificador_rol, $identificador_componente )
	{
		$proyecto = toba_proyecto::get_id();
		$id = toba::proyecto()->get_id_componente_por_indice($identificador_componente, $proyecto);
		$this->agregar_dependencia( $identificador_rol, $proyecto, $id['componente'] );
		$this->dep($identificador_rol)->inicializar();
	}

	/**
	 * Construye una dependencia y la asocia al componente actual
	 *
	 * @param unknown_type $identificador
	 * @param unknown_type $parametros
	 * @return unknown
	 * @ignore 
	 */
	function cargar_dependencia($identificador)
 	{
		if(!isset($this->_indice_dependencias[$identificador])){
			throw new toba_error_def("OBJETO [cargar_dependencia]: No EXISTE una dependencia asociada al indice [$identificador].");
		}
		$posicion = $this->_indice_dependencias[$identificador];
		$clase = $this->_info_dependencias[$posicion]['clase'];
		$clave['proyecto'] = $this->_info_dependencias[$posicion]['proyecto'];
		$clave['componente'] = $this->_info_dependencias[$posicion]['objeto'];
		$this->_dependencias[$identificador] = toba_constructor::get_runtime( $clave, $clase );
	}
	

	/**
	 * Descarga una dependencia asociada al componente actual
	 *
	 * @param unknown_type $identificador
	 * @return unknown
	 * @ignore 
	 */
	function descargar_dependencia($identificador)
	{
		$posicion = $this->_indice_dependencias[$identificador];
		unset($this->_info_dependencias[$posicion]);
		unset($this->_indice_dependencias[$identificador]);
		unset($this->_dependencias[$identificador]);
		foreach($this->_lista_dependencias as $pos => $id) {
			if($id == $identificador) {
				unset($this->_lista_dependencias[$pos]);
			}	
		}
	}

	/**
	 * Retorna verdadero si la dependencia fue construida y asociada en este pedido de página
	 * @return boolean
	 */
	function dependencia_cargada($id)
	{
		return isset($this->_dependencias[$id]);
	}
	
	/**
	 * Retorna verdadero si un componente es dependencia del actual
	 * @return boolean
	 */
	function existe_dependencia($id)
	{
		return isset($this->_indice_dependencias[$id]);	
	}

	/**
	 * Retorna un array con las dependencias cargadas del componente
	 * @return array
	 */
	function get_dependencias()
	{
		return $this->_dependencias;		
	}
	
	/**
	 * Retorna la cantidad de dependencias cargadas
	 * @return integer
	 */
	function get_cantidad_dependencias()
	{
		return count($this->_dependencias);		
	}

	/**
	 * Devuelve las dependencias cuya clase de componente coincide con la expresion regular pasada como parametro
	 * @return array
	 */
	function get_dependencias_clase($ereg_busqueda)
	{
		$ok = array();
		for($a=0;$a<count($this->_info_dependencias);$a++){
			if( preg_match("/".$ereg_busqueda."/", $this->_info_dependencias[$a]['clase']) ){
				$ok[] = $this->_info_dependencias[$a]["identificador"];
			}
		}
		return $ok;
	}

}
?>