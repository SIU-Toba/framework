<?php
if (! defined('apex_ef_no_seteado')) {
	define('apex_ef_no_seteado','nopar');// Valor que debe ser considerado como NO ACTIVADO, si se cambia cambiar en las clases JS
}
if (! defined('apex_tipo_gadget_interno')) {
define ('apex_tipo_gadget_interno', 'I');
}
if (! defined('apex_tipo_gadget_shindig')) {
define('apex_tipo_gadget_shindig', 'S');
}
if (version_compare(PHP_VERSION, '5.3.2') >= 0) {
	define('apex_pa_algoritmo_hash', 'bcrypt');
} else {
	define('apex_pa_algoritmo_hash', 'sha256');
}
if (! defined('apex_pa_pwd_largo_minimo')) {
	define('apex_pa_pwd_largo_minimo', '8');
}

/**
 * Clase que brinda las puertas de acceso al n�cleo de toba
 * @package Centrales
 */
class toba_nucleo
{
	static private $instancia;
	static private $dir_compilacion;
	static private $path;
	/**
	 * @var toba_solicitud
	 */
	private $solicitud = null;
	private $medio_acceso;
	private $solicitud_en_proceso = false;

	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_nucleo();
		}
		return self::$instancia;
	}

	static function get_indice_archivos()
	{
		// Dado que esta funci�n se usa en el entorno del instalador no se puede
		// registrar el autoload del n�cleo xq resulta en comportamiento extra�o
		require_once(self::toba_dir().'/php/toba_autoload.php');
		return toba_autoload::$clases;
	}

	private function __construct()
	{
		$this->cargar_includes_basicos();
		spl_autoload_register(array('toba_nucleo', 'cargador_clases'));
		toba::cronometro();
	}

	/**
	 * @return solicitud
	 */
	function get_solicitud()
	{
		return $this->solicitud;
	}
		
	/**
	 * Punto de entrada http al nucleo
	 */
	function acceso_web()
	{
		try {
			$this->iniciar_contexto_ejecucion();
			toba::manejador_sesiones()->verificar_cambio_perfil_activo();				//Miro si se quiere cambiar el perfil funcional activo
			toba_http::headers_standart();
			try {
				$this->solicitud = $this->cargar_solicitud_web();
				$this->solicitud_en_proceso = true;
				$this->solicitud->procesar();
			} catch( toba_reset_nucleo $e ) {
				toba::logger()->info('Se recargo el nucleo', 'toba');
				//El item retrasa el envio de headers?
				if ( !$this->solicitud->get_datos_item('retrasar_headers') ) {
					throw new toba_error_def('ERROR: La operaci�n no esta habilitada para provocar redirecciones.');
				}
				//TRAP para forzar la recarga de solicitud
				$this->solicitud_en_proceso = false;
				toba::memoria()->limpiar_memoria();
				$item_nuevo = $e->get_item();
				toba::memoria()->set_item_solicitado($item_nuevo);				
				$this->solicitud = $this->cargar_solicitud_web();
				$this->solicitud->procesar();
			}
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
			$this->finalizar_contexto_ejecucion();
		} catch (Exception $e) {
			toba::logger()->crit($e, 'toba');
			echo $e->getMessage() . "\n\n";
		}
		//toba::logger()->debug('Tiempo utilizado: ' . toba::cronometro()->tiempo_acumulado() . ' seg.');
		toba::logger()->guardar();
	}
	
	/**
	 * Punto de entrada http al nucleo
	 */
	function acceso_servicio()
	{
		try {
			$this->iniciar_contexto_ejecucion();
			//toba_http::headers_standart();
			$item = toba::memoria()->get_item_solicitado();
			if (! isset($item)) {
				//Si no tiene ID (porque axis lo elimina del GET) usar el extra la URL
				$servicio = basename($_SERVER['REQUEST_URI']);	//Asume que es x.php/id_servicio
				if (strpos($servicio, '?') !== false) {
					$servicio = substr($servicio, 0, strpos($servicio, '?')); 
				}
				//Si es el .php a secas pide un listado de los servicios
				if (basename($servicio, '.php') !== $servicio) {
					header("HTTP/1.0: 404 Not Found");
					die;
				}
				$item = array(apex_pa_proyecto, $servicio);
				toba::memoria()->set_item_solicitado($item);
			}
			$this->iniciar_contexto_solicitud($item);
			$this->solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'toba_item');
			if(! $this->solicitud instanceof toba_solicitud_servicio_web) {
				throw new toba_error_seguridad("El item {$item[1]} no es un item de servicio web");
			}
			if (!toba::instalacion()->es_produccion()) {
				if ($xml = file_get_contents('php://input')) {
					toba::logger_ws()->debug("XML Input: $xml");
					toba::logger_ws()->set_checkpoint();
				}
			}
			$this->solicitud_en_proceso = true;
			$this->solicitud->procesar();
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
			$this->finalizar_contexto_ejecucion();
		} catch (Exception $e) {
			toba::logger()->crit($e, 'toba');
			echo $e->getMessage() . "\n\n";
		}
		toba::logger()->guardar();
	}	

	/**
	 * Punto de entrada http-REST al nucleo
	 */
	function acceso_rest()
	{
		try {
			$this->iniciar_contexto_ejecucion();
			$toba_rest = new toba_rest();
			if (! $toba_rest->es_pedido_documentacion()) {
				$toba_rest->conf__inicial();
				$app = $toba_rest->get_instancia_rest();
				if (method_exists(toba::contexto_ejecucion(), "conf__rest")) {
					toba::contexto_ejecucion()->conf__rest($app);
				}
			}
			$toba_rest->ejecutar();
			$this->finalizar_contexto_ejecucion();
		} catch (Exception $e) {
			toba::logger()->crit($e, 'toba');
			echo $e->getMessage() . "\n\n";
		}
		toba::logger()->guardar();
	}

	/**
	 * Punto de entrada desde la consola al nucleo
	 */	
	function acceso_consola($instancia, $proyecto, $item)
	{
		$estado_proceso = null;
		$this->iniciar_contexto_desde_consola($instancia, $proyecto);
		try {
			$this->solicitud = toba_constructor::get_runtime(array('proyecto' => $proyecto, 'componente' => $item), 'toba_item');
			$this->solicitud->procesar();	//Se llama a la ACTIVIDAD del ITEM
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
			$estado_proceso = $this->solicitud->get_estado_proceso();
		} catch (toba_error $e) {
			toba::logger()->crit($e, 'toba');
			echo $e;
		}
		$this->finalizar_contexto_ejecucion();
		toba::logger()->debug('Estado Proceso: '.$estado_proceso, 'toba');
		//toba::logger()->debug('Tiempo utilizado: ' . toba::cronometro()->tiempo_acumulado() . ' seg.');
		$dir_logs = toba_modelo_instalacion::dir_base().'/logs_comandos';
		toba::logger()->set_directorio_logs($dir_logs);
		toba::logger()->guardar_en_archivo('comandos.log');
		exit($estado_proceso);		
	}
	
	function iniciar_contexto_desde_consola($instancia, $proyecto)
	{
		//Seteo el estado del nucleo
		$_SERVER['TOBA_INSTANCIA'] = $instancia;
		$_SERVER['TOBA_PROYECTO'] = $proyecto;
		$this->iniciar_contexto_ejecucion();	
	}
	
	function solicitud_en_proceso()
	{
		return $this->solicitud_en_proceso;
	}

	/**
	 * Carga la SOLICITUD actual. Se determina el item y se controla el acceso al mismo
	 */
	function cargar_solicitud_web()
	{
		if (toba::manejador_sesiones()->existe_sesion_activa()) {		// Estoy dentro de una SESION
			$item = $this->get_id_item('item_inicio_sesion', false, true);
			if (!$item[0]||!$item[1]) {
				throw new toba_error_def('ERROR: No esta definido el ITEM de INICIO de sesion');	
			}			
			$this->iniciar_contexto_solicitud($item);
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'toba_item');
			if (!$solicitud->es_item_publico()) {
				$this->autorizar_acceso_item($item);
			}
			return $solicitud;
		} else {														// Estoy fuera de la sesion. Solo se puede acceder a lo publico
			$mensaje_error = 'La sesi�n no esta activa. Solo es posible acceder items PUBLICOS.';
			$item = $this->get_id_item('item_pre_sesion');
			if(!$item[0] || !$item[1]) {
				throw new toba_error_def('ERROR: No esta definido el ITEM de LOGIN');	
			}			
			$this->iniciar_contexto_solicitud($item);
			$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'toba_item');
			if (!$solicitud->es_item_publico()) {
				// Si se arrastra una URL previa despues de finalizar la sesion y se refresca la pagina
				// el nucleo trata de cargar un item explicito por URL. El mismo no va a ser publico...
				// Esto apunta a solucionar ese error: Blanqueo el item solicitado y vuelvo a intentar.
				// (NOTA: esto puede ocultar la navegacion entre items supuestamente publicos)
				if ( toba::memoria()->get_item_solicitado() ) {
					toba::logger()->notice('Fallo la carga de una operaci�n publica. Se intenta con la operaci�n predeterminada', 'toba');
					toba::memoria()->set_item_solicitado(null);					
					$item = $this->get_id_item('item_pre_sesion');
					$this->iniciar_contexto_solicitud($item);
					$solicitud = toba_constructor::get_runtime(array('proyecto'=>$item[0],'componente'=>$item[1]), 'toba_item');
					if (!$solicitud->es_item_publico()) {
						throw new toba_error_def($mensaje_error);		
					}
				} else {
					throw new toba_error_def($mensaje_error);				
				}
			}
			return $solicitud;
		}		
	}

	/**
	 * Averigua el ITEM ACTUAL. Si no existe y puede busca el ITEM PREDEFINIDO pasado como parametro
	 */
	protected function get_id_item($predefinido=null,$forzar_predefinido=false, $evitar_pre_sesion=false)
	{
		$item = toba::memoria()->get_item_solicitado();
		if (!$item || ($evitar_pre_sesion && 
						$item[0] == toba::proyecto()->get_id() &&
						$item[1] == toba::proyecto()->get_parametro('item_pre_sesion'))) {
			if(isset($predefinido)){
				$item[0] = toba::proyecto()->get_id();
				$item[1] = toba::proyecto()->get_parametro($predefinido);		
				toba::memoria()->set_item_solicitado($item);
			} else {
				throw new toba_error_def('NUCLEO: No es posible determinar la operaci�n a cargar');
			}
		}
		return $item;
	}
	
	protected function autorizar_acceso_item($item)
	{
		if (toba::proyecto()->get_id() != $item[0]) {
			//-- No esta implementado poder entrar a un item de un proyecto distinto de la solicitud
			throw new toba_error_seguridad('Imposible determinar proyecto actual');
		}
		if (! toba::proyecto()->puede_grupo_acceder_item($item[1]) ) {
			throw new toba_error_autorizacion('El usuario no posee permisos para acceder al item solicitado.');
		}
	}

	protected function iniciar_contexto_ejecucion()
	{
		if (!ini_get('safe_mode') && strpos(ini_get('disable_functions'), 'set_time_limit') === FALSE) {		
			set_time_limit(0);
		}
		$this->controlar_requisitos_basicos();
		$this->agregar_paths();
		$this->recuperar_revision_recursos();
		toba::manejador_sesiones()->iniciar();
		$this->registrar_autoloaders_proyecto();
		toba::contexto_ejecucion()->conf__inicial();
	}
        
	protected function agregar_paths()
	{
            agregar_dir_include_path(toba_proyecto::get_path_php());
            if (toba::proyecto()->es_personalizable()) {
                agregar_dir_include_path(toba_proyecto::get_path_pers_php());
            }
        }
        

	protected function registrar_autoloaders_proyecto()
	{
		$punto_php = toba::puntos_montaje()->get(toba_modelo_pms::pm_php);
		$punto_php->registrar_autoload();

		if (toba::proyecto()->es_personalizable()) {
			$punto_pers = toba::puntos_montaje()->get(toba_modelo_pms::pm_pers);
			$punto_pers->registrar_autoload();
		}
	}

	protected function get_archivos_autoload()
	{
		return toba_autoload::$clases;
	}

	protected function finalizar_contexto_ejecucion()
	{
		toba::manejador_sesiones()->finalizar();
		toba::contexto_ejecucion()->conf__final();
		if (isset($this->solicitud)) {
			$this->solicitud->guardar_cronometro();
		}
	}
	
	function controlar_requisitos_basicos()
	{
		if (php_sapi_name() !== 'cli' && get_magic_quotes_gpc()) {
			throw new toba_error_def("Necesita desactivar las 'magic_quotes' en el servidor (ver http://www.php.net/manual/es/security.magicquotes.disabling.php)");
		}
	}

	//--------------------------------------------------------------------------
	//	Carga de archivos
	//--------------------------------------------------------------------------

	static function toba_dir()
	{
		if (! isset(self::$path)) {
			$dir = dirname(__FILE__);
			self::$path = substr($dir, 0, -11);
		}
		return self::$path;
	}

	static function toba_instalacion_dir()
	{
		if (isset($_SERVER['TOBA_INSTALACION_DIR'])) {
			return $_SERVER['TOBA_INSTALACION_DIR'];
		} /*elseif (isset($_SERVER['toba_instalacion_dir'])) {
			return $_SERVER['toba_instalacion_dir'];
		}*/ else {
			return self::toba_dir().'/instalacion';
		}
	}	
	
	/**
	*	Carga de includes basicos
	*/
	protected function cargar_includes_basicos()
	{
		//Las funciones globales no puden cargarse con el autoload.
		if ($this->utilizar_archivos_compilados()) {
			require_once( self::toba_dir() . '/php/nucleo/toba_motor.php');	
		} else {
			foreach(self::get_includes_funciones_globales() as $archivo ) {
				require_once( self::toba_dir() . $archivo);
			}
		}
		require_once(self::toba_dir().'/php/toba_autoload.php');	// incluimos las clases de autoload
	}

	/**
	*	Indica si hay que usar el nucleo resumido a un archivo o el esquema de includes usual
	*	@ignore
	*/
	function utilizar_archivos_compilados()
	{
		return (defined('apex_pa_archivos_compilados') && apex_pa_archivos_compilados);
	}
		
	/**
	*	En el modo compilado, carga los metadatos necesarios para la solicitud actual
	*	@ignore
	*/
	protected function iniciar_contexto_solicitud($item)
	{
		if ($this->utilizar_metadatos_compilados() ) {
			// Arbol de componentes de la operacion
			$id = toba_manejador_archivos::nombre_valido( $item[1] );
			require_once( self::get_directorio_compilacion() . '/oper/toba_mc_oper__' . $id . '.php' );
			// Datos BASICOS
			require_once( self::get_directorio_compilacion() . '/gene/toba_mc_gene__basicos.php' );
			$grupos_acceso = toba::manejador_sesiones()->get_perfiles_funcionales_activos();
			foreach( $grupos_acceso as $grupo ) {
				require_once( self::get_directorio_compilacion() . '/gene/toba_mc_gene__grupo_'.$grupo.'.php' );				
			}
		}
	}

	/**
	*	Indica si hay que usar metadatos compilados o provenientes de la base.
	*	(La compilacion se activa solo sobre el proyecto primario).
	*	@ignore
	*/
	function utilizar_metadatos_compilados($proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : toba_proyecto::get_id();
		$flag = toba::instancia()->get_directiva_compilacion($proyecto);
		if(!isset($flag) && $proyecto == toba_proyecto::get_id()) {
			//Mecanismo obsoleto
			return (defined('apex_pa_metadatos_compilados') && apex_pa_metadatos_compilados);
		}else{
			return $flag;			
		}
	}

	static function get_directorio_compilacion()
	{
		if(!self::$dir_compilacion) self::$dir_compilacion = toba_proyecto::get_path() . '/metadatos_compilados';
		return self::$dir_compilacion;
	}

	static function cargador_clases($clase)
	{
		//Busqueda de un METADATO compilado!!
		if (strpos($clase,'toba_mc_')!==false) {
			$subdir = substr($clase,8,4);
			$archivo = self::get_directorio_compilacion() . '/' . $subdir . '/' . $clase . '.php';
			require_once($archivo);
			return;
		}
		//Carga de las clases del nucleo
		if (toba_autoload::existe_clase($clase)) {
			toba_autoload::cargar($clase);
		}
//		if(isset(toba_autoload::$clases[$clase])) {
//			require_once( self::toba_dir() .'/php/'. toba_autoload::$clases[$clase]);
//		} else {
//			echo "$clase<br>";
//		}

	}

	/**
	*	@ignore
	*/
	static function get_includes_funciones_globales()
	{
		return array( 
			'/php/lib/toba_varios.php',
			'/php/lib/toba_parseo.php',
			'/php/lib/toba_sql.php',
			'/php/nucleo/lib/toba_db.php',
			'/php/nucleo/lib/interface/toba_ei.php',
			'/php/nucleo/lib/toba_debug.php', 
			'/php/contrib/lib/array_column.php'
		);
	}
	
	private function recuperar_revision_recursos()
	{
		$svn = new toba_svn();		
		if (! toba::memoria()->existe_dato_instancia('toba_revision_recursos_cliente')) {
			if (toba::instalacion()->es_produccion() || ! $svn->hay_cliente_svn()) {
				$version = toba::instalacion()->get_version()->__toString();
			} else {
				$path_recursos = $this->toba_dir(). '/www';
				$version = $svn->get_revision($path_recursos);
			}
			toba::memoria()->set_dato_instancia('toba_revision_recursos_cliente', $version);
		}
		
		if (! toba::memoria()->existe_dato_instancia('proyecto_revision_recursos_cliente')) {
			if (toba::instalacion()->es_produccion() || ! $svn->hay_cliente_svn()) {
				$version = toba::proyecto()->get_version()->__toString();
			} else {
				$path_recursos = toba::proyecto()->get_path(). '/www';
				$version = $svn->get_revision($path_recursos);
			}
			toba::memoria()->set_dato_instancia('proyecto_revision_recursos_cliente', $version);
		}
	}
}
?>
