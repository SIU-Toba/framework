<?php
define('apex_callback_sin_rpta', 'apex_callback_sin_rpta');

/**
 * Padre de todas las clases que definen componentes
 * @package Componentes
 * @wiki Referencia/Objetos
 */
abstract class toba_componente
{
	protected $solicitud;
	protected $id;
	protected $info;
	protected $info_dependencias;						//Definicion de las dependencias
	protected $indice_dependencias;					//Indice que mapea las definiciones de las dependencias con su
	protected $dependencias_indice_actual = 0;	
	protected $lista_dependencias = array();					//Lista de dependencias disponibles
	protected $dependencias = array();							//Array de sub-OBJETOS
	protected $memoria;
	protected $memoria_existencia_previa = false;
	protected $observaciones;
	protected $canal;										// Canal por el que recibe datos 
	protected $canal_recibido;							// Datos recibidos por el canal
	protected $estado_proceso;							// interno | string | "OK","ERROR","INFRACCION"
	protected $id_ses_g;									//ID global para la sesion
	protected $id_en_controlador;						//Id relativo al controlador padre
	protected $definicion_partes;						//indica el nombre de los arrays de metadatos que posee el objeto
	protected $exportacion_archivo;
	protected $exportacion_path;
	protected $propiedades_sesion = array();			//Arreglo de propiedades que se persisten en sesion
	protected $parametros;								// Parametros de inicializacion provistos por el controlador	
	
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
		$this->id[0] = $definicion['info']['proyecto'];
		$this->id[1] = $definicion['info']['objeto'];
		//Cargo las variables internas que forman la definicion
		foreach (array_keys($definicion) as $parte) {
			$this->definicion_partes[] = $parte;
			$this->$parte = $definicion[$parte];
		}
		$this->solicitud = toba::solicitud();
		$this->log = toba::logger();
		//Recibi datos por el CANAL?
		$this->canal = apex_hilo_qs_canal_obj . $this->id[1];
		$this->canal_recibidos = toba::memoria()->get_parametro($this->canal);
		$this->id_ses_g = "obj_" . $this->id[1];
		$this->id_ses_grec = "obj_" . $this->id[1] . "_rec";
		$this->set_controlador($this);												//Hasta que nadie lo explicite, yo me controlo solo
		//Manejo transparente de memoria
		$this->cargar_memoria();			//RECUPERO Memoria sincronizada
		$this->recuperar_estado_sesion();	//RECUPERO Memoria dessincronizada
		$this->cargar_info_dependencias();
		$this->log->debug("CONSTRUCCION: {$this->info['clase']}({$this->id[1]}): {$this->get_nombre()}", 'toba');
	}

	/**
	 * Método interno para iniciar el componente una vez construido
	 * @ignore 
	 */
	function inicializar($parametros=array())
	{
		$this->parametros = $parametros;
	}

	/**
	 * Destructor del componente
	 */	
	function destruir()
	{
		//Persisto informacion
		$this->memorizar();						//GUARDO Memoria sincronizada
		$this->guardar_estado_sesion();		//GUARDO Memoria dessincronizada
		//Llamo a los destructores de los OBJETOS anidados
		foreach(array_keys($this->dependencias) as $dependencia){
			$this->dependencias[$dependencia]->destruir();
		}
	}

	/**
	 * @ignore 
	 */	
	function get_clave_memoria_global()
	{
		return $this->id_ses_grec;
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
		return "componente(".$this->id[1]."): ";	
	}

	/**
	 * Retorna el nombre del componente según se definió en el editor
	 * @return string
	 */
	function get_nombre()
	{
		return $this->info['nombre'];
	}

	/**
	 * Retorna el título del componente (nombre visible al usuario)
	 * @return string
	 */
	function get_titulo()
	{
		return $this->info['titulo'];
	}

	/**
	 * Retorna el identificador del componente
	 * @return integer
	 */
	function get_id()
	{
		return $this->id;	
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
			$this->log->debug( $this->get_txt() . "[ invocar_callback ] '$metodo'", 'toba');
			return call_user_func_array(array($this, $metodo), $parametros);
		}else{
			$this->log->debug($this->get_txt() . "[ invocar_callback ] '$metodo' no fue atrapado", 'toba');
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
		if ($mensaje = toba::mensajes()->get_componente($this->id[1], $indice, $parametros)) {
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
//Las clases que lo usen solo tienen generar las claves que necesiten dentro de este (ej: $this->memoria["una_cosa"])
//y despues llamar a los metodos "memorizar" para guardarla en el HILO y "cargar_memoria" para recuperarlo
//Preg: Por que no se usa el indice 0 en la clave del OBJETO?
//Res: proque no se pueden cargar objetos de dos proyectos en la misma solicitud

	/**
	 * Persiste el array '$this->memoria' para utilizarlo en la proxima invocacion del objeto
	 * @ignore 
	 */
	function memorizar()
	{
		if(isset($this->memoria)){
			toba::memoria()->set_dato_sincronizado("obj_".$this->id[1],$this->memoria);
		}
	}
	
	/**
	 * Recupera la memoria que dejo una instancia anterior del objeto. (Setea $this->memoria)
	 * @ignore
	 */
	function cargar_memoria()
	{
		if($this->memoria = toba::memoria()->get_dato_sincronizado("obj_".$this->id[1])){
			$this->memoria_existencia_previa = true;
		}
	}

	/**
	 * Controla la existencia de la memoria
	 * SI la memoria no se cargo se corta la ejecucion y despliega un mensaje
	 * @ignore 
	 */
	function controlar_memoria()
	{
		if ((!isset($this->memoria)) || (is_null($this->memoria))){
			throw new toba_error("Error cargando la MEMORIA del OBJETO. abms[". ($this->id[1]) ."]");
		}
	}

	/**
	 * Elimina toda la memoria interna actual y de pedidos anteriores
	 */
	function borrar_memoria()
	{
		unset($this->memoria);
		toba::memoria()->set_dato_sincronizado("obj_".$this->id[1],null);
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
		$this->propiedades_sesion = array_merge($this->propiedades_sesion, $props);
	}

	/**
	 * Carga en la clase actual los valores de las propiedades almacenadas en sesion
	 * @ignore 
	 */
	protected function recuperar_estado_sesion()
	{
		if(toba::memoria()->existe_dato($this->id_ses_grec)) {
			//Recupero las propiedades de la sesion
			$temp = toba::memoria()->get_dato($this->id_ses_grec);
			if(isset($temp["toba__indice_objetos_serializados"])) {			//El objeto persistio otros objetos
				$objetos = $temp["toba__indice_objetos_serializados"];
				unset($temp["toba__indice_objetos_serializados"]);
				foreach(array_keys($temp) as $propiedad) {
					if(in_array($propiedad,$objetos)) {
						//La propiedad es un OBJETO!
						$this->$propiedad = unserialize($temp[$propiedad]);
					} else {
						$this->$propiedad = $temp[$propiedad];
					}
				}
			} else { 														//El objeto solo persistio variables
				foreach(array_keys($temp) as $propiedad) {
					$this->$propiedad = $temp[$propiedad];
				}
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
		if(count($this->propiedades_sesion)>0){
			for($a=0;$a<count($this->propiedades_sesion);$a++){
				//Existe la propiedad
				$nombre_prop = $this->propiedades_sesion[$a];
				if(isset($this->$nombre_prop)) {
					if(is_object($this->$nombre_prop)){
						$temp[$this->propiedades_sesion[$a]] = serialize($this->$nombre_prop);
						//Dejo la marca de que serialize un OBJETO.
						$temp["toba__indice_objetos_serializados"][] = $this->propiedades_sesion[$a];
					} else {
						$temp[$this->propiedades_sesion[$a]] = $this->$nombre_prop;
					}
				}
			}
			if(isset($temp)) {
				//ei_arbol($temp,"Persistencia PROPIEDADES " . $this->id[1]);
				$temp['toba__descripcion_objeto'] = '['. get_class($this). '] ' . $this->info['nombre'];
				toba::memoria()->set_dato_operacion($this->id_ses_grec, $temp);
			} else {
				//Si existia y las propiedades pasaron a null, hay que borrarlo
				toba::memoria()->eliminar_dato($this->id_ses_grec);
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
		for($a=0;$a<count($this->propiedades_sesion);$a++){
			if(!in_array($this->propiedades_sesion[$a], $no_eliminar)){
				$nombre_prop = $this->propiedades_sesion[$a];
				unset($this->$nombre_prop);
			}
		}
		toba::memoria()->eliminar_dato($this->id_ses_grec);
	}
	
	/**
	 * Construye un arreglo con los valores de todas las propiedades a almacenarse en sesion
	 * @return array Arreglo(propiedad => valor)
	 */
	function get_estado_sesion()
	{
		if(count($this->propiedades_sesion)>0){
			$propiedades = get_object_vars($this);
			for($a=0;$a<count($this->propiedades_sesion);$a++){
				//Existe la propiedad
				if(in_array($this->propiedades_sesion[$a],$propiedades)){
					//Si la propiedad no es NULL
					$nombre_prop = $this->propiedades_sesion[$a];
					if (isset( $this->$nombre_prop) ) {
						$temp[$this->propiedades_sesion[$a]] = $this->$nombre_prop;
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
		$this->id_en_controlador = $id_en_padre;
		if (isset($this->objeto_js)) {
			$this->objeto_js .= '_'.$id_en_padre;
			$this->submit .= '_'.$id_en_padre;
		}
	}	
	
	/**
	 * Arma un hash interno de las dependencias, utilizo durante la construccion
	 * @ignore 
	 */
	protected function cargar_info_dependencias()
	{
		if (isset($this->info_dependencias)) {
			for($a=0;$a<count($this->info_dependencias);$a++){
				$this->indice_dependencias[$this->info_dependencias[$a]["identificador"]] = $a;//Columna de informacion donde esta la definicion
				$this->lista_dependencias[] = $this->info_dependencias[$a]["identificador"];
			}
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
		return $this->dependencias[$id];
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
	 * @param string $identificador ID. del componente
	 * @param string $proyecto 
	 * @param string $tipo Tipo de componente
	 */
	function agregar_dependencia( $identificador, $proyecto, $objeto )
	{
		$sig = count($this->info_dependencias);
		$this->info_dependencias[$sig] = toba::proyecto()->get_definicion_dependencia($objeto, $identificador, $proyecto);
		$this->indice_dependencias[$identificador] = $sig;
		$this->lista_dependencias[] = $identificador;	
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
		//El indice es valido?
		if(!isset($this->indice_dependencias[$identificador])){
			throw new toba_error("OBJETO [cargar_dependencia]: No EXISTE una dependencia asociada al indice [$identificador].");
		}
		$posicion = $this->indice_dependencias[$identificador];
		$clase = $this->info_dependencias[$posicion]['clase'];
		$clave['proyecto'] = $this->info_dependencias[$posicion]['proyecto'];
		$clave['componente'] = $this->info_dependencias[$posicion]['objeto'];
		$this->dependencias[$identificador] = toba_constructor::get_runtime( $clave, $clase );
	}

	/**
	 * Retorna verdadero si la dependencia fue construida y asociada en este pedido de página
	 * @return boolean
	 */
	function dependencia_cargada($id)
	{
		return isset($this->dependencias[$id]);
	}
	
	/**
	 * Retorna verdadero si un componente es dependencia del actual
	 * @return boolean
	 */
	function existe_dependencia($id)
	{
		return isset($this->indice_dependencias[$id]);	
	}
	
	/**
	 * Devuelve las dependencias cuya clase de componente coincide con la expresion regular pasada como parametro
	 * @return array
	 */
	function get_dependencias_clase($ereg_busqueda)
	{
		$ok = array();
		for($a=0;$a<count($this->info_dependencias);$a++){
			if( preg_match("/".$ereg_busqueda."/", $this->info_dependencias[$a]['clase']) ){
				$ok[] = $this->info_dependencias[$a]["identificador"];
			}
		}
		return $ok;
	}

}
?>