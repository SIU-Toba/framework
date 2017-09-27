<?php
use SecurityMultiTool\Csrf\TokenGenerator;
	
//Tamaño de la pila de la memoria sincronizada
if (! defined('apex_hilo_tamano')) {
	define('apex_hilo_tamano','5');
}
//Tipos de reciclado de la memoria global
define('apex_hilo_reciclado_item',0);
define('apex_hilo_reciclado_acceso',1);

//----------------------------------------------------------------
//-------------------- QUERYSTRING Basico ------------------------
//----------------------------------------------------------------
/*
	ATENCION, estas claves compiten con posibles claves de los proyectos consumidores
*/
//Separador en el caso de que una clave transporte varios valores
define("apex_qs_separador","||");			//Separador utilizado para diferenciar campos de valores compuestos
define("apex_qs_sep_interno","^^");			//Separador utilizado para segundo nivel de campos compuestos
// Claves de querystring utilizadas por la pareja VINCULADOR - HILO
define("apex_hilo_qs_id","ah");									//ID de Hilo referenciado
define("apex_hilo_qs_item","ai");								//ITEM de catalogo a solicitar
define("apex_hilo_qs_parametros","ap");							//zona en la que se va a cargar al ITEM
//-- Internos
define("apex_hilo_qs_canal_obj","toba-can");					//Prefijo de los CANALES de OBJETOS (Para comunicarse con ellos por GET)
define("apex_hilo_qs_zona","tz");						//CANAL de propagacion de ZONAS
define("apex_hilo_qs_cronometro","toba-cron");					//CANAL gatillo del cronometro
define("apex_hilo_qs_menu","tm");						//Indica que el vinculo proviene del MENU
define("apex_hilo_qs_celda_memoria","tcm");				//Indicador que indica que el vinculo proviene del MENU
define("apex_hilo_qs_celda_memoria_defecto","central");	//Nombre de la celda de memoria predeterminada
define("apex_hilo_qs_servicio", "ts");
define("apex_hilo_qs_servicio_defecto", "generar_html");
define("apex_hilo_qs_objetos_destino", "tsd");

define('apex_sesion_csrt', 'cstoken');
define('apex_default_charset',  'ISO-8859-1');	

/**
 * La memoria contiene la información historica de la aplicación, enmascarando a $_GET y $_SESSION:
 *  - Memoria general de la aplicación
 *  - Memoria de las operaciones
 *  - Memoria sincronizada entre URLs (generalmente de interes interno al framework)
 *  - Parametros del link desde donde se vino ($_GET)
 * 
 * @package Centrales
 */
class toba_memoria
{
	static private $instancia;
	private $id;
	private $url_actual;
	private $item_solicitado;
	private $item_solicitado_original = null;
	private $hilo_referencia;
	private $parametros;
	private $reciclar_memoria = true;	//Habilita el reciclado de la memoria en la sesion
	private $acceso_menu;
	private $servicio = apex_hilo_qs_servicio_defecto;
	private $objetos_destino = null;
	//Memoria
	private $memoria_celdas;			// Bindeo a el espacio de la memoria del proyecto
	private $celda_memoria_actual_id;	// ID de la celda actual.
	private $celda_memoria_actual;		// Referencia al espacio de direcciones de la CELDA actual
	private $memoria_global;			// Espacio de memoria global de la aplicacion.
	private $memoria_instancia;			// Memoria global de la instancia.
	private $url_original;
	private $parametros_item_original;
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_memoria();
		}
		return self::$instancia;		
	}
	
	static function eliminar_instancia()
	{
		self::$instancia = null;
	}
			
	private function __construct()
	{
		//toba::logger()->debug("TOBA MEMORIA: Inicializacion.", 'toba');
		//dump_session();
		$this->id = uniqid('st', true);
		$this->url_actual = texto_plano($_SERVER["PHP_SELF"]);	
		
		$this->parametros = array();
		//-[0]- Recupero los parametros
		foreach (array_keys($_GET) as $clave) {		
			$this->parametros[utf8_decode($clave)] = utf8_decode($_GET[$clave]);			
		}
		
		 //-[1]- Busco el ID de referencia de la instanciacion anterior del HILO
		//		Este ID me permite ubicar la memoria correcta para el request ACTUAL
		if(isset($this->parametros[apex_hilo_qs_id])){
			$this->hilo_referencia=$this->parametros[apex_hilo_qs_id];
		}else{
			//Atencion, no hay hilo de referencia. CONTROLAR!!
			//Esto tiene sentido solo para la pagina de logon (?) para el resto 
			//del sistema implica que las cosas funcionen mal!
		}
        
		//-[2]- Que ITEM se solicito?
		$this->item_solicitado = self::get_item_solicitado_original();		
		//FALTA hacer un URL decode!!!		
		$encriptar_qs = toba::proyecto()->get_parametro('encriptar_qs');
		if($encriptar_qs){
			$claves_encriptacion = toba::instalacion()->get_claves_encriptacion();
			if(isset($_GET[apex_hilo_qs_parametros])){
				$encriptador = toba::encriptador();
				parse_str($encriptador->desencriptar($_GET[apex_hilo_qs_parametros], $claves_encriptacion['get']), $parametros);
				$this->parametros = array_merge($this->parametros, $parametros);
				unset($this->parametros[apex_hilo_qs_parametros]);
				unset($this->parametros["jmb76"]);	//Clave agregada para complicar la encriptacion
			}
		}
		unset($this->parametros[apex_hilo_qs_id]);
		unset($this->parametros[apex_hilo_qs_item]);
		
		//------- MEMORIA -- Hago el bindeo con $_SESSION  ----------------------------
		// Determino el ID de la celda de memoria actual		
		if(isset($this->parametros[apex_hilo_qs_celda_memoria])) {
			$this->celda_memoria_actual_id = self::get_valor_verificado($this->parametros[apex_hilo_qs_celda_memoria],  apex_hilo_qs_celda_memoria_defecto);
			unset($this->parametros[apex_hilo_qs_celda_memoria]);
		} else {
			$this->celda_memoria_actual_id = apex_hilo_qs_celda_memoria_defecto;
		}
		
		// Apunto las referencias a session
		$this->memoria_celdas =& toba::manejador_sesiones()->segmento_memoria_proyecto();
		// Celda ACTUAL
		if(!isset($this->memoria_celdas[$this->celda_memoria_actual_id])){
			$this->memoria_celdas[$this->celda_memoria_actual_id] = array();
		}
		$this->celda_memoria_actual =& $this->memoria_celdas[$this->celda_memoria_actual_id]; 
		// Memoria GLOBAL
		if(!isset($this->memoria_celdas['__toba__global_proyecto'])){
			$this->memoria_celdas['__toba__global_proyecto'] = array();
		}
		$this->memoria_global =& $this->memoria_celdas['__toba__global_proyecto'];
		// Memoria de la INSTANCIA
		$this->memoria_instancia =& toba::manejador_sesiones()->segmento_datos_instancia();
		//-----------------------------------------------------------------------------

		if (isset($this->parametros[apex_hilo_qs_servicio])) {			
			$this->servicio = substr(self::get_valor_verificado($this->parametros[apex_hilo_qs_servicio], apex_hilo_qs_servicio_defecto),0, 60);
			unset($this->parametros[apex_hilo_qs_servicio]);
		}
		if (isset($this->parametros[apex_hilo_qs_objetos_destino])) {
			$objetos = $this->parametros[apex_hilo_qs_objetos_destino];
			$lista_obj = explode(",", $objetos);
			$this->objetos_destino = array();
			foreach ($lista_obj as $obj) {
				if (trim($obj) != '') {
					$this->objetos_destino[] = explode(apex_qs_separador, $obj);
				}
			}
			unset($this->parametros[apex_hilo_qs_servicio]);
		}		
		//Guardo el FLAG que indica si se accedio por el menu
		if (isset($_GET[apex_hilo_qs_menu])) {
			$this->acceso_menu = true;
		} else {
			$this->acceso_menu = false;
		}

		$this->inicializar_memoria();
		
		if (isset($this->celda_memoria_actual['hilo'])) {
			//Si la página requerida es una que ya se genero, se descartan los siguientes pedidos
			//Esto permite manejar el caso del refresh y el back, aunque se pierde la posibilidad de re-hacer con forward
			//ya que cada back que se ingresa genera un nuevo estado (porque se realiza una nueva ejecución)
			$claves = array_keys($this->celda_memoria_actual['hilo']);
			if (in_array($this->hilo_referencia, $claves) 			//Desde donde provengo aun se encuentra en el cache
					&& $this->hilo_referencia != end($claves)) {	//No provengo desde la ultima generacion (seria el caso normal)
				$encontrado = false;
				foreach ($claves as $clave) {
					if ($encontrado) {
						unset($this->celda_memoria_actual['hilo'][$clave]);	//Borro aquellos que quedan en 'sandwich'
					}
					if ($clave == $this->hilo_referencia) {
						$encontrado = true;
					}
				}
			}
		}
		if (! isset($this->parametros_item_original) || is_null($this->parametros_item_original)) {
			$this->parametros_item_original = $this->parametros;
			$this->set_dato('parametros_item_original', $this->parametros);
			toba::logger()->debug("Guardando parametros del item original");
			toba::logger()->var_dump($this->parametros_item_original);
		}
 	}

 	/**
 	 * Destructor de la memoria, no sirve para borrar la memoria, sino es parte del proceso de apagado del framework
 	 */
	function destruir()
	{
		//Disparo el proceso de reciclaje
		if($this->reciclar_memoria){
			$this->reciclar_datos_globales_acceso();	
			$this->reciclar_datos_sincronizados();
		}
		//Mantengo guardado cual es el item anterior de la celda
		$this->celda_memoria_actual['item_anterior'] = $this->celda_memoria_actual['item'];
	}

	function get_celda_memoria_actual_id()
	{
		return $this->celda_memoria_actual_id;
	}

	function set_item_solicitado($item, $genera_csrf_token=true) 
	{
		toba::logger()->debug('Se cambia el ítem solicitado a =>'.var_export($item, true), "toba");
		$this->item_solicitado = $item;
		$this->inicializar_memoria();
		if ($genera_csrf_token) {
			$this->fijar_csrf_token(true);
		}	
		if (toba_editor::activado()) {						//Esto no deberia estar en el item de inicializacion de sesion del editor? WTF ^ 10 ^ 100
			toba_editor::set_item_solicitado($item);
		}
	}
	
	/**
	 * Genera la primera porcion de todas las URLs
	 */
	function prefijo_vinculo()
	{
		return $this->url_actual . "?" . apex_hilo_qs_id  . "=" . $this->id;
	}

	/**
	 * Retorna el id que identifica univocamente este request
	 */
	function get_id()
	{
		return $this->id;
	}

	//----------------------------------------------------------------
	//-----------------  ACCESO al ESTADO GENERAL  -------------------
	//----------------------------------------------------------------
	
	/**
	 * Retorna el servicio solicitado por la URL
	 */
	function get_servicio_solicitado()
	{
		return $this->servicio;	
	}
	
	function set_servicio_solicitado($servicio)
	{
		$this->servicio = $servicio;
	}
	
	
	/**
	 * Retorna la referencia a aquellos objetos destino del servicio solicitado
	 */
	function get_id_objetos_destino()
	{
		return $this->objetos_destino;
	}

	/**
	 * Retorna el valor de un parámetro enviado en la URL por el vinculador
	 *
	 * @param string $canal Identificador que se utilizó como clave del parámetro
	 * @return string Valor pasado como parámetro, o null en el caso que no se encuentre
	 */
	function get_parametro($canal)
	{
		if(isset($this->parametros[$canal])){
			return $this->parametros[$canal];			
		}else{
			return null;
		}
	}

	/**
	 * Retorna todos los parámetros enviados en la URL por el vinculador
	 * @return array Arreglo clave => valor de los parámetros
	 */
	function get_parametros()
	{
		$temp = $this->parametros;
		unset($temp[apex_hilo_qs_zona]);
		return $temp;			
	}

	/**
	 * Retorna el item requerido en este pedido de página
	 * @return array [0]=>proyecto, [1]=>id_item
	 */
	function get_item_solicitado()
	{
		if (isset($this->item_solicitado)) {
			return $this->item_solicitado;
		}
	}

	/**
	 * Retorna la operación requerida originalmente por el usuario en este pedido de página
	 * Puede diferir de la operación actual ya que se pudo hacer una redirección
	 * @see get_item_solicitado
	 * @return array [0]=>proyecto, [1]=>id_item
	 */	
	static function get_item_solicitado_original()
	{
		if (isset($_GET[apex_hilo_qs_item])) {
			$item = explode(apex_qs_separador,$_GET[apex_hilo_qs_item]);
			if(count($item)==0) {
				return null;
			} elseif(count($item)==2) {
				$proy = self::get_valor_verificado($item[0], false);
				$it = self::get_valor_verificado($item[1], false);				
				return ($proy !== false  && $it !== false ) ? array($proy, $it) : null;		//Los parametros pueden o no cumplir con las reglas
			} else {
				return null;
			}
		} else {
			return null;//No hay parametro
		}		
	}
	
	/**
	 * Retorna los parametros del item requerido originalmente, la lectura es destructiva del dato
	 * @return array
	 */
	function get_parametros_item_original()	
	{
		if (isset($this->parametros_item_original)) {
			$this->eliminar_dato('parametros_item_original');							//Elimino el dato para que solo pueda ser utilizado por unica vez (de preferencia en el login)
			toba::logger()->debug("Eliminando parametros del item original");
			unset($this->parametros_item_original[apex_hilo_qs_zona]);
			unset($this->parametros_item_original[apex_sesion_qs_finalizar]);			
			return $this->parametros_item_original;
		}
		return null;
	}	
	
	function usuario_solicita_cronometrar()
	{
		if(isset($this->parametros[apex_hilo_qs_cronometro])){
			return true;
		}else{
			return false;
		}
	}
	
	private function set_url_original($param)
	{
		$rs = filter_var($param, FILTER_VALIDATE_URL);
		if ($rs !== false) {
			$this->url_original = $rs;			
		}
	}
	
	function get_url_solicitud()
	{
		if (isset($this->url_original)) {
			return $this->url_original;
		} else {
			$url = toba_http::get_url_actual(true, true);			
			$this->set_url_original($url);
		}
		
		return $this->url_original;
	}
	//----------------------------------------------------------------
	//------------ MEMORIA -------------------------------------------
	//----------------------------------------------------------------	

	/**
		Inicializa la memoria
	*/
	function inicializar_memoria()
	{
		if( $this->verificar_acceso_menu() ){
			/*
				El flag de acceso por el menu desencadena el borrado de toda la memoria pensada para
				utilizarse dentro de una operacion: los elementos de la memoria GLOBAL marcados 
				como 'reciclables' y la memoria sincronizada (la alienada al request anterior).
			*/
			toba::logger()->info('Se detecto acceso desde el menu. Se limpia la memoria de la operacion', 'toba');
			$this->limpiar_memoria_sincronizada();
			$this->limpiar_datos_reciclable();
			$this->fijar_csrf_token(true);
		}
		$this->parametros_item_original = $this->get_dato('parametros_item_original');							
		$this->inicializar_reciclaje_global();		
	}

	function limpiar_memoria()
	{
		$this->limpiar_memoria_sincronizada();
		$this->limpiar_datos_operacion();
	}

	function dump()
	{
		ei_arbol( array(	'global proyecto' => $this->memoria_global,
					'celda_actual' => $this->celda_memoria_actual, 
					'instancia' => $this->memoria_instancia ),
					'Estado INTERNO de toba::memoria()');
	}

	/**
		Indica si se accedio por el menu
	*/
	function verificar_acceso_menu()
	{
		return $this->acceso_menu;
	}

	/**
	 * Indica si se lanzo la solicitud por medio de un ef_popup.
	 * @return boolean 
	 */
	function verificar_acceso_popup()
	{
		$valor = $this->get_parametro('ef_popup_valor');
		return (! is_null($valor));
	}
	//------------------------------------------------------------------------
	//--------- Manejo de DATOS de la INSTANCIA (no utiliza celdas) ----------
	//------------------------------------------------------------------------

	/**
	 * Almacena un dato en la instancia. 
	 * Este mecanismo aporta un canal para la comunicacion entre proyectos que se ejecutan simultaneamente.
	 */
	function set_dato_instancia($indice, $datos)
	{
		$this->memoria_instancia[$indice]=$datos;		
	}
	
	/**
	 * Recupera un dato almacenado con set_dato_instancia
	 * @return mixed Si el dato existe en la memoria lo retorna sino retorna null
	 */
	function get_dato_instancia($indice, $eliminar_dato=false)
	{
		if($this->existe_dato_instancia($indice))	{
			$dato = $this->memoria_instancia[$indice];
			if ($eliminar_dato) {
				$this->eliminar_dato_instancia($indice);
			}
			return $dato;
		}else{
			return null;
		}
	}

	/**
	 * Elimina un dato de la memoria de la instancia
	 */
	function eliminar_dato_instancia($indice)
	{
		if(isset($this->memoria_instancia[$indice])){
			unset($this->memoria_instancia[$indice]);
		}
	}
	
	/**
	 * Determina si un dato esta disponible en la memoria de la instancia
	 */	
	function existe_dato_instancia($indice)
	{
		return isset($this->memoria_instancia[$indice]);
	}
	
	/**
	 * Limpia la memoria de la instancia
	 */
	function limpiar_datos_instancia()
	{
		$this->memoria_instancia = array();
	}

	//------------------------------------------------------------------------
	//--------- Manejo de DATOS del PROYECTO (no utiliza celdas) -------------
	//------------------------------------------------------------------------
	/**
	 * Almacena un dato en la sesion del proyecto y perdura durante toda la sesión
	 * Similar al manejo normal del $_SESSION en una aplicacion ad-hoc
	 */
	function set_dato($indice, $datos)
	{
		$this->memoria_global[$indice]=$datos;		
	}
	
	/**
	 * Recupera un dato almacenado con set_dato
	 * @return mixed Si el dato existe en la memoria lo retorna sino retorna null
	 */
	function get_dato($indice)
	{
		if($this->existe_dato($indice))	{
			return $this->memoria_global[$indice];
		}else{
			return null;
		}
	}

	/**
	 * Elimina un dato de la memoria
	 */
	function eliminar_dato($indice)
	{
		if(isset($this->memoria_global[$indice])){
			unset($this->memoria_global[$indice]);
		}
	}
	
	/**
	 * Determina si un dato esta disponible en la memoria
	 */	
	function existe_dato($indice)
	{
		return isset($this->memoria_global[$indice]);
	}
	
	/**
	 * Limpia la memoria global del proyecto
	 */
	function limpiar_datos()
	{
		$this->memoria_global = array();
	}

	//------------------------------------------------------------------------
	//-------- Manejo de DATOS de una OPERACION en la sesion (x celda) -------
	//------------------------------------------------------------------------
	/*
	*	Esta memoria es utilizada por los componentes toba para guardar la informacion
	*	propia de una operacion. La misma se recicla cuando el usuario cambia de operacion
	*/

	/**
	 * Almacena un dato de la operacion actual en la sesión.
	 * Se elimina cuando se cambia de operación
	 */
	function set_dato_operacion($indice, $datos)
	{
		$this->celda_memoria_actual['global'][$indice]=$datos;
		$this->agregar_dato_global_reciclable($indice, apex_hilo_reciclado_item);
	}

	/**
	 * Recupera un dato almacenado durante la operacion
	 * @return mixed Si el dato existe en la memoria lo retorna sino retorna null
	 */
	function get_dato_operacion($indice)
	{
		if($this->existe_dato_operacion($indice))	{
			//Se avisa que se accedio a un dato global al sistema de reciclado
			$this->acceso_a_dato_global($indice);
			return $this->celda_memoria_actual['global'][$indice];
		}else{
			return null;
		}
	}

	/**
	 * Elimina un dato de la memoria de la operacion
	 */
	function eliminar_dato_operacion($indice)
	{
		if(isset($this->celda_memoria_actual['global'][$indice])){
			unset($this->celda_memoria_actual['global'][$indice]);
		}
		$this->eliminar_informacion_reciclado($indice);
	}
	
	/**
	 * Determina si un dato esta disponible en la memoria de la operacion
	 */	
	function existe_dato_operacion($indice)
	{
		return isset($this->celda_memoria_actual['global'][$indice]);
	}
	
	/**
	 * Limpia la memoria de la operacion
	 */
	function limpiar_datos_operacion()
	{
		$this->celda_memoria_actual['global'] = array();
	}

	//------------------------------------------------------------------------
	//------- Primitivas control seguridad en la (x operacion, x celda) ------
	//------------------------------------------------------------------------

	/**
	 * Almacena un array de la operacion actual en la sesión.
	 * Se elimina cuando se cambia de operación
	 */
	function set_array_operacion($indice, $datos)
	{
		$this->set_dato_operacion($indice, $datos);
	}

	/**
	 * Controla que exista un dato en un array almacenado para la operacion actual.
	 */	
	function en_array_operacion($indice, $dato, $tira_excepcion=true)
	{
		$ok = in_array($dato, $this->celda_memoria_actual['global'][$indice]);
		if ($tira_excepcion && !$ok) {
			throw new toba_error_seguridad('El valor seleccionado no se corresponde con las opciones válidas');
		}
		return $ok;
	}
	
	/**
	 * Recupera un array almacenado durante la operacion
	 * @return mixed Si el dato existe en la memoria lo retorna sino retorna null
	 */
	function get_array_operacion($indice)
	{
		return $this->get_dato_operacion($indice);
	}
	
	/**
	 * Elimina un array de la memoria de la operacion
	 */
	function limpiar_array_operacion($indice)
	{
		$this->eliminar_dato_operacion($indice);
	}

	//------------------------------------------------------------------------
	//-------------------- Memoria SINCRONIZADA (x celda) --------------------
	//------------------------------------------------------------------------
	/**
	 * Guarda un dato en la memoria sincronizada.
	 * La memoria sincronizada guarda datos macheados contra el request que los produjo.
	 * Por ejemplo en el request 65 el indice 'cantidad de tabs'  tiene el valor 8
	 * Al hacer el get_dato_sincronizado se chequea en que request se encuentra actualmente y retorna el valor asociado
	 * Esto permite que al hacer BACK con el browser se vuelva a las variables de sesion de las antiguas paginas
	 * No es una buena opción para guardar información de la aplicación sino mas bien cosas relacionadas con la seguridad
	 * y funcioanmiento interno del framework
	 */
	function set_dato_sincronizado($indice, $datos, $celda=null)
	{
		if (!isset($celda)) {
			$this->celda_memoria_actual['hilo'][$this->id][$indice]=$datos;
		} else {
			$this->memoria_celdas[$celda]['hilo'][$this->id][$indice]=$datos;
		}
	}
	
	/**
	 * Recupera un dato de la memoria sincronizada, macheandolo con el id actual del hilo
	 * @return mixed El dato solicitado o NULL si no existe
	 */
	function get_dato_sincronizado($indice, $celda=null)
	{
		if (isset($celda)) {
			//--- Es una celda particular
			if (isset($this->memoria_celdas[$celda]['hilo'][$this->hilo_referencia][$indice])) {
				return $this->memoria_celdas[$celda]['hilo'][$this->hilo_referencia][$indice];
			}
		} elseif (isset($this->celda_memoria_actual['hilo'][$this->hilo_referencia][$indice])) {
			//--- Celda actual
			return $this->celda_memoria_actual['hilo'][$this->hilo_referencia][$indice];
		}
		return null;
	}

	function eliminar_dato_sincronizado($indice)
	{
		if(isset($this->celda_memoria_actual['hilo'][$this->id][$indice])){
			unset($this->celda_memoria_actual['hilo'][$this->id][$indice]);
		}
	}

	function limpiar_memoria_sincronizada()
	{
		unset($this->celda_memoria_actual['hilo']);
	}

	private function reciclar_datos_sincronizados()
	//Ejecuto la recoleccion de basura de la MEMORIA SINCRONIZADA
	{
		if(isset($this->celda_memoria_actual['hilo'])){
			if(count($this->celda_memoria_actual['hilo']) > apex_hilo_tamano ){
				array_shift($this->celda_memoria_actual['hilo']);
			}
		}
	}

	/**
	 * Borra la memoria de un evento antendido en un request previo
	 * 	Lo utilizan los componentes para que los eventos no se reejecuten con el refresh del browser
	 * @ignore 
	 */
	function eliminar_evento_sincronizado_solicitud_previa($componente, $evento)
	{
		if(isset($this->celda_memoria_actual['hilo'][$this->hilo_referencia][$componente]['eventos'][$evento])) {
			unset($this->celda_memoria_actual['hilo'][$this->hilo_referencia][$componente]['eventos'][$evento]);
		}
	}

	//----------------------------------------------------------------	
	//-------------  RECICLAJE de CELDAS -----------------------------
	//----------------------------------------------------------------	
	
	/**
		Desactiva el reciclado
	*/
	function desactivar_reciclado()
	{
		$this->reciclar_memoria = false;
	}

	/**
		Inicializa el esquema de reciclado global
	*/
	private function inicializar_reciclaje_global()
	{
		//-- Inicializo reciclaje por cambio de item.
		if(isset($this->item_solicitado)){
			$this->celda_memoria_actual['item'] = implode('|',$this->item_solicitado);
		}else{
			$this->celda_memoria_actual['item'] = 'inicio';
		}
		//toba::logger()->debug('Item de celda de memoria: '.$this->celda_memoria_actual['item']);
		//-- Inicializo reciclaje por acceso.
		//		Vacio los reciclables activos para que se registren ellos.
		$this->celda_memoria_actual['reciclables_activos'] = array();
		if(!isset($this->celda_memoria_actual['reciclables'])){
			$this->celda_memoria_actual['reciclables'] = array();
		}
		//Disparo el reciclaje por cambio de item
		$this->reciclar_datos_globales_item();
	}

	/**
		Se marca un dato global como reciclable
	*/
	private function agregar_dato_global_reciclable($indice, $tipo_reciclado)
	//Se reporta que un dato global se va a reciclar
	{
		if($tipo_reciclado != apex_hilo_reciclado_item && $tipo_reciclado != apex_hilo_reciclado_acceso ){
			//El tipo de reciclado es invalido!
			throw new toba_error('El tipo de reciclado solicitado es invalido');
		}
		if( !$this->existe_dato_reciclable($indice) ){
			$this->celda_memoria_actual['reciclables'][$indice] = $tipo_reciclado;
		}
		if($tipo_reciclado == apex_hilo_reciclado_acceso){
			//Si el tipo de reciclado es por conteo de accesos, marco al dato como activo
			$this->dato_global_activo($indice);
		}
	}
	
	/**
		Registra el acceso a datos globales para el esquema de reciclado por conteo de accesos
	*/
	private function acceso_a_dato_global($indice)
	{
		//Si el tipo de reciclado es por acceso, marco que el elemento fue accedido
		if( isset($this->celda_memoria_actual['reciclables'][$indice]) ){
			if($this->celda_memoria_actual['reciclables'][$indice]== apex_hilo_reciclado_acceso){
				$this->dato_global_activo();	
			}
		}
	}

	/**
		Setea un dato reciclable como activo en el esquema de reciclado por conteo de accesos
	*/
	private function dato_global_activo($indice)
	//Indica que el dato reciclable fue activado
	{
		//Puede ser llamado por fuera
		if( $this->existe_dato_reciclable($indice) ){
			if(!in_array($indice,$this->celda_memoria_actual['reciclables_activos'])){
				$this->celda_memoria_actual['reciclables_activos'][] = $indice;
			}
		}
	}

	/**
		Reciclado de datos globales por cambio de item.
		Se debe ejecutar cuando se inicia el request
			(Si el item de la celda actual cambio, eliminar el contenido)
	*/
	private function reciclar_datos_globales_item()
	{
		if(isset($this->celda_memoria_actual['item_anterior'])){
			$es_distinto_item = ($this->celda_memoria_actual['item_anterior'] != $this->celda_memoria_actual['item']);
			if($es_distinto_item && !$this->verificar_acceso_menu()) {
				toba::logger()->info("Se detecto cambio de operación. Se limpia la memoria de la operacion", 'toba');
				foreach( $this->celda_memoria_actual['reciclables'] as $reciclable => $tipo){	
					if($tipo == apex_hilo_reciclado_item){
						$this->eliminar_dato_operacion($reciclable);
					}
				}
			}
		}
	}

	/**
		Reciclado por control de acceso a los datos guardados
		Se debe ejecutar cuando termina el request
			(Si un dato no fue accedido, borrarlo)
	*/
	private function reciclar_datos_globales_acceso()
	{
		foreach( $this->celda_memoria_actual['reciclables'] as $reciclable => $tipo){	
			if($tipo == apex_hilo_reciclado_acceso){
				//Si hay un elemento reciclable que no se activo, lo destruyo
				if(!in_array($reciclable,$this->celda_memoria_actual['reciclables_activos'])){
					toba::logger()->info("Se limpio de la memoria el elemento '$reciclable' porque no fue accedido", 'toba');
					$this->eliminar_dato_operacion($reciclable);
				}
			}
		}
	}

	/**
		Controla si existe un dato reciclabe
	*/
	private function existe_dato_reciclable($indice)
	{
		return (isset($this->celda_memoria_actual['reciclables'][$indice]));
	}

	/**
		Limpia toda la memoria reciclable
	*/
	private function limpiar_datos_reciclable()
	{
		if(isset($this->celda_memoria_actual['reciclables'])){
			foreach($this->celda_memoria_actual['reciclables'] as $reciclable => $tipo){
				$this->eliminar_dato_operacion($reciclable);
			}
		}
		//Esto no deberia ser necesario.
		unset($this->celda_memoria_actual['reciclables']);
		unset($this->celda_memoria_actual['reciclables_activos']);
	}

	/**
		Elimina la informacion asociada al reciclado de un dato
	*/
	private function eliminar_informacion_reciclado($indice)
	{
		//Si el dato era reciclable, lo saco de las listas de reciclado
		if($this->existe_dato_reciclable($indice)){
			$tipo = $this->celda_memoria_actual['reciclables'][$indice];
			//Si el reciclado es de tipo 'acceso', tengo que sacarlo de la lista de reciclables activos
			if($tipo == apex_hilo_reciclado_acceso){
				foreach(array_keys($this->celda_memoria_actual['reciclables_activos']) as $reciclable){
					if($this->celda_memoria_actual['reciclables_activos'][$reciclable]==$indice){
						unset($this->celda_memoria_actual['reciclables_activos'][$reciclable]);
					}
				}
			}
			unset($this->celda_memoria_actual['reciclables'][$indice]);
		}
	}
	
	//----------------------------------------------------------------
	//-------------------- EVENTOS de SESION TOBA --------------------
	//----------------------------------------------------------------
/*
* Las funciones que siguen generan dos links ESPECIALES que se atrapan antes que cualquier 
* proxima ACTIVIDAD (Antes de que exista la proxima solicitud). 
* Estos links implican EVENTOS de SESION. Estan metidos en el hilo porque
* Hacen referencia al estado actual (especificamente a la eliminacion del estado actual)
* Hasta ahora estas funciones solo se llaman desde el CONTROL.
*/

	function finalizar()
/*
 	@@acceso: nucleo
	@@desc: Crea el vinculo que hay que llamar para eliminar la SESION...
*/
	{
		return $this->prefijo_vinculo() ."&". apex_sesion_qs_finalizar . "=1";
	}	

	//----------------------------------------------------------------
	//---------------- MANEJO de ARCHIVOS de Temporales --------------
	//----------------------------------------------------------------	
/*
	Estas funciones pertinen manejar el ciclo de vida de archivos
	cargados con UPLOADS que son solo validos durante la sesion.
	La eliminacion de estos archivos esta en el evento cerrar de la sesion
*/
	function registrar_archivo_temporal($archivo){
		if(	!isset($this->memoria_celdas['__toba__archivos_temporales']) 
			|| !in_array($archivo, $this->memoria_celdas['__toba__archivos_temporales'])){
			$this->memoria_celdas['__toba__archivos_temporales'][] = $archivo;
		}
	}

	function eliminar_archivos_temporales()
	{
		//Existieron archivos temporales asociados a la sesion, los elimino...
		if (isset($this->memoria_celdas['__toba__archivos_temporales'])) {
			foreach($this->memoria_celdas['__toba__archivos_temporales'] as $archivo) {
				//SI puedo ubicar los archivos los elimino
				if(is_file($archivo)){
					unlink($archivo);
				}
			}
		}		
	}

	/**
	 * Intenta devolver la parte correcta de un parametro o falso sino
	 * @param mixed $param
	 * @param mixed $valor_defecto
	 * @return mixed
	 */
	static function get_valor_verificado($param, $valor_defecto)
	{
		if ( preg_match('#^[a-zA-Z0-9_]+$#', $param, $aux) !== false) {
			return current($aux);
		} else {			
			return $valor_defecto;
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------------------------------//
	//						METODOS ANTI-CSRF 
	//-------------------------------------------------------------------------------------------------------------------------------------//	
	function fijar_csrf_token($forzar = false)
	{
		if (! $this->existe_dato_operacion(apex_sesion_csrt) || $forzar) {
			$cstoken = $this->generar_unique_cripto();
			$this->set_dato_operacion(apex_sesion_csrt, $cstoken);
		}
	}
	
	function validar_pedido_pagina($valor_form)
	{
		if ($this->existe_dato_operacion(apex_sesion_csrt)) {
			$valor = trim($valor_form);
			$frm_orig = ($valor === trim($this->get_dato_operacion(apex_sesion_csrt)));	
			return $frm_orig;
		} 
		return true;
	}
	
	protected function generar_unique_cripto()
	{		
		$generador = new SecurityMultiTool\Csrf\TokenGenerator();
		$hashed = $generador->generate();
		return $hashed;
	}
}
?>