<?php
define('TOBA_LOG_CRIT',     2);     /** Critical conditions */
define('TOBA_LOG_ERROR',    3);     /** Error conditions */
define('TOBA_LOG_WARNING',  4);     /** Warning conditions */
define('TOBA_LOG_NOTICE',   5);     /** Normal but significant */
define('TOBA_LOG_INFO',     6);     /** Informational */
define('TOBA_LOG_DEBUG',    7);     /** Debug-level messages */

if (! defined('E_DEPRECATED')) {		//Fix para PHP 5.2.x o anteriores
	define('E_DEPRECATED', 8192);
}
/**
 * Mantiene una serie de sucesos no visibles al usuario y los almacena para el posterior analisis
 * Los sucesos tienen una categoria (debug, info, error, etc.) y el proyecto que la produjo
 * 
 * Para loguear información de debug:
 * 		<pre>toba::logger()->debug($mensaje);</pre>
 * 
 * Para imprimir el valor de una variable en el log
 * 		<pre>toba::logger()->var_dump($variable);</pre>
 * 
 * Para guardar la traza actual de ejecución:
 * 		<pre>toba::logger()->trace();</pre>
 * 
 * Para loguear algun error interno:
 * 		<pre>toba::logger()->error('El importe nunca debio ser negativo!');</pre>
 * 
 *
 * Desde el punto de acceso es posible definir el nivel máximo que se guarda, los niveles son:
 *  - TOBA_LOG_CRIT: 2
 *  - TOBA_LOG_ERROR: 3
 *  - TOBA_LOG_WARNING: 4
 *  - TOBA_LOG_WARNING: 5
 *  - TOBA_LOG_WARNING: 6
 *  - TOBA_LOG_DEBUG: 7
 *
 *  Recomendaciones de uso:
 *
 *  critico: error fatal (no se sabe como puede continuar)
 *  error: falla de control o validacion (en cierta forma podría ser recuperable reiniciando la operacion por ej)
 *  warning: falla que es recuperable (se asume algo y se sigue)
 *  notice: paso algo que puede llegar a dar un fallo
 *  info: suceso que merece cierta atención
 *  debug: suceso
 *
 * @wiki Referencia/PuntosDeAcceso El nivel actual se define en el Punto de Acceso
 * @package Debug
 */
class toba_logger
{
	const separador = "-o-o-o-o-o-";
	const fin_encabezado = "==========";
	const limite_mensaje = 100000; //100 KB
	static protected $errores_manejables = array(E_WARNING, E_NOTICE, E_DEPRECATED);
	static protected $instancia;
	protected $ref_niveles;
	protected $proyecto_actual;
	
	//--- Arreglos que contienen info de los logs en runtime
	protected $mensajes = array();
	protected $niveles = array();
	protected $proyectos = array();
	
	protected $proximo = 0;
	protected $nivel_maximo = 7;
	protected $activo = true;
	
	protected $dir_logs;
	//--- Variables que son necesarias para cuando el logger se muestra antes de terminar la pág.
	protected $mostrado = false;				//Ya fue guardado en este pedido de página
	protected $cant_mostrada;					//Cant. de logs que había cuando se mostro

	/**
	 * @todo Para sacar el path de la instalacion y la instancia se necesitaria tener acceso a la clase instalacion
	 * pero no se carga en el runtime, solo en la parte administrativa, por ahora se replica el lugar
	 * donde se encuentra el dir de instalacion
	 */
	protected function __construct($proyecto = null)
	{
		$this->proyecto_actual = (isset($proyecto)) ? $proyecto : $this->get_proyecto_actual();
		$this->ref_niveles[2] = "CRITICAL";
		$this->ref_niveles[3] = "ERROR";
		$this->ref_niveles[4] = "WARNING";
		$this->ref_niveles[5] = "NOTICE";
		$this->ref_niveles[6] = "INFO";
		$this->ref_niveles[7] = "DEBUG";
		
		
		//--- Valores por defecto
		if (!defined('apex_log_archivo_tamanio')) define('apex_log_archivo_tamanio', 1024);
		if (!defined('apex_log_archivo_backup_cant')) define('apex_log_archivo_backup_cant', 10);
		if (!defined('apex_log_archivo_backup_compr')) define('apex_log_archivo_backup_compr', false);		
	}

	
	/**
	 * Este es un singleton por proyecto
	 * @return logger
	 */
	static function instancia($proyecto=null)
	{
		if (!isset(self::$instancia[$proyecto])) {
			self::$instancia[$proyecto] = new toba_logger($proyecto);
		}
		return self::$instancia[$proyecto];	
	}

	static function manejador_errores_recuperables($error_nro, $error_string, $error_archivo = '', $error_linea = 0)
	{
		$instancia  = self::instancia();
		$instancia->error("Se produjo una salida inesperada: \n ($error_nro) $error_string \n En el archivo $error_archivo ($error_linea)");
		if (! in_array($error_nro, self::$errores_manejables)) {
			return false;						// Que lo trate el manejador de errores de PHP ya que no es uno de los que dejan texto nomas.
		}
	}

	function get_proyecto_actual()
	{
		if (class_exists('toba_proyecto')) {
			try {
				return toba_proyecto::get_id();			
			} catch (Exception $e) {
			}
		}
		return 'toba';
	}
	
	function get_usuario_actual()
	{
		if( php_sapi_name() === 'cli' ) {
			return null;
		} else {
			return toba::usuario()->get_id();
		}
	}
	
	/**
	 * Desactiva el logger durante todo el pedido de página actual
	 */
	function desactivar()
	{
		$this->nivel_maximo = 0;
		$this->activo = false;
	}
	
	
	function set_nivel($nivel)
	{
		$this->nivel_maximo = $nivel;
	}
	

	/**
	 * @ignore 
	 */
	protected function registrar_mensaje($mensaje, $proyecto, $nivel)
	{
		if ($nivel <= $this->nivel_maximo) {
			$msg = $this->extraer_mensaje($mensaje);
			if (strlen($msg) > self::limite_mensaje) {
				$msg = substr($msg, 0, self::limite_mensaje).
						"..TEXTO CORTADO POR EXCEDER EL LIMITE DE ".
						self::limite_mensaje.
						" bytes";
			}	
			$this->mensajes[$this->proximo] = $msg;
			$this->niveles[$this->proximo] = $nivel;
			if (!isset($proyecto)) {
				//Se hace estatica para poder loguear antes de construido el hilo
				$proyecto = $this->proyecto_actual;
			}
			$this->proyectos[$this->proximo] = $proyecto;
			$this->proximo++;
		}
	}
	
	/**
	 * @ignore 
	 */	
	protected function extraer_mensaje($mensaje)
	{
		if (is_object($mensaje)) {
			if ($mensaje instanceof Exception) {
				if ($mensaje instanceof toba_error) {
					$texto = $mensaje->get_mensaje_log();
				} else {
					$texto = $mensaje->getMessage();
				}
				$res = get_class($mensaje).": ".$texto."\n";
				$es_php_compatible = false && version_compare(phpversion(), "5.1.0", ">=");
				if ($es_php_compatible) {
					//Solo muestra parametros en modo DEBUG
					$con_parametros = (TOBA_LOG_DEBUG <= $this->nivel_maximo);
					$traza = $mensaje->getTrace();
					$traza[0]['file'] = $mensaje->getFile();
					$traza[0]['line'] = $mensaje->getLine();
					$res .= $this->construir_traza($con_parametros, $traza);
				} else {
					//Para php < 5.1 mostrar el string 
					$res .= "\n[TRAZA]".$mensaje->__toString();
				}
				return $res;
			} else if (method_exists($mensaje, 'getMessage')) {
				return $mensaje->getMessage();
			} else if (method_exists($mensaje, 'tostring')) {
				return $mensaje->toString();
			} else if (method_exists($mensaje, '__tostring')) {
				return (string)$mensaje;
			} else {
				return var_export($mensaje, true);
			}
		} else if (is_array($mensaje)) {
			return var_export($mensaje, true);
		} else {
			return $mensaje;	
		}
	}
	
	protected function construir_traza($con_parametros=false, $pasos = null)
	{
		if (!isset($pasos)) {
    		$pasos = debug_backtrace();
		}
		$html = "[TRAZA]\n";
		$html .= "\t<ul>\n";    
		foreach ($pasos as $paso) {
			$clase = '';
			if (isset($paso['class'])) {
				$clase .= $paso['class'];
			}
			//Se obvia los pasos por esta clase
			if ($clase !== __CLASS__) {
				if (isset($paso['type']))
					$clase .= $paso['type'];				
				$html .= "\t<li><strong>$clase{$paso['function']}</strong><br />";
				if (isset($paso['file'])) {
					$html .= "Archivo: {$paso['file']}, línea {$paso['line']}<br />";
				}
				if ($con_parametros && ! empty($paso['args'])) {
					$html .= "Parámetros: <ol>";
					foreach ($paso['args'] as $arg) {
						$html .= "<li>";
						if (is_object($arg)) {
							$html .= 'Instancia de <em>'.get_class($arg).'</em>';
						} else {
							$html .= highlight_string(print_r($arg, true), true);
						}
						$html .= "</li>\n";
					}
					$html .= "\t</ol>\n";
				} 
				$html .= "\t</li>\n";
			}
		}
		$html .= "\t</ul>";
		//--- Una traza no puede exceder la mitad del limite de todo el mensaje
		if (strlen($html) > self::limite_mensaje/2) {
			$html = substr($html, 0, self::limite_mensaje/2).
					"\nTRAZA CORTADA POR EXCEDER EL LIMITE DE ".
					self::limite_mensaje/2 . " bytes";
		}
		return $html;
	}
	
	protected function mensajes()
	{
		return $this->mensajes;
	}
	
	function get_cantidad_mensajes()
	{
		return count($this->mensajes);
	}
	
	function get_mensajes_minimo_nivel()
	{
		$cantidad = array();
		$minimo = $this->nivel_maximo + 1;
		foreach ($this->niveles as $nivel) {
			if (! isset($cantidad[$nivel])) {
				$cantidad[$nivel] = 0;
			}
			$cantidad[$nivel]++;
			if ($nivel < $minimo) {
				$minimo = $nivel;
			}
		}
		if ($minimo !==  $this->nivel_maximo + 1) {
			return array($minimo, $cantidad[$minimo]);
		} else {
			return array(0, 0);
		}
	}
	
	
	function get_niveles()
	{
		return $this->ref_niveles;	
	}
	
	function get_nivel()
	{
		return $this->nivel_maximo;
	}	
	
	function modo_debug()
	{
		return ($this->get_nivel() == TOBA_LOG_DEBUG);
	}	

	//------------------------------------------------------------------
	//------ Entradas para los distintos tipos de error
	//------------------------------------------------------------------

	/**
	 * Muestra la traza de ejecucion actual en el logger
	 */
	function trace($con_parametros=false, $proyecto = null)
	{
		$this->debug($this->construir_traza($con_parametros), $proyecto);
	}

	/**
	 * Dumpea el contenido de una variable al logger
	 */
	function var_dump($variable, $proyecto = null)
	{
		$this->debug(var_export($variable, true), $proyecto);
	}
	
	/**
	 * Registra un suceso CRITICO (un error muy grave)
	 */
	function crit($mensaje, $proyecto=null)
	{
		return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_CRIT);
	}

	/**
	 * Registra un error en la apl., este nivel es que el se usa en las excepciones
	 */    
	function error($mensaje, $proyecto=null)
	{
		return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_ERROR);
	}

	/**
	 * Registra un suceso no contemplado pero que posiblemente no afecta la correctitud del proceso
	 */
	function warning($mensaje, $proyecto=null)
	{
		return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_WARNING);
	}

	/**
	*	Indica la llamada a un metodo/funcion obsoleto, es un alias de notice
	*	@param string $version  Versión desde la cual el metodo/funcion deja de estar disponible
	*/
	function obsoleto($clase, $metodo, $version, $extra=null, $proyecto=null) 
	{
		if (TOBA_LOG_NOTICE <= $this->nivel_maximo) {
			$extra = "";
			//Se saca el archivo que llamo el metodo obsoleto solo cuando hay modo debug
			if (TOBA_LOG_DEBUG <= $this->nivel_maximo) {
				$traza = debug_backtrace();
				$archivo = $traza[2]['file'];
				$linea = $traza[2]['line'];
				$extra = "Archivo: $archivo, linea: $linea";
			}
			if ($clase != '') {
				$unidad = "Método '$clase::$metodo'";
			} elseif ($metodo != '') {
				$unidad = "Función '$metodo'";
			} else {
				$unidad = '';	
			}
			$msg = "OBSOLETO: $unidad desde versión $version. $extra";
			$this->notice($msg, $proyecto);
		}
	}

	/**
	 * Registra un suceso no contemplado que no es critico para la aplicacion
	 */    
	function notice($mensaje, $proyecto=null)
	{
		return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_NOTICE);
	}

	/**
	 * Registra un suceso netamente informativo, para una inspección posterior
	 */
	function info($mensaje, $proyecto=null)
	{
		return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_INFO);
	}

	/**
	 * Registra un suceso útil para rastrear problemas o bugs en la aplicación
	 */
	function debug($mensaje, $proyecto=null)
	{
		return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_DEBUG);
	}

	/**
	 * Inserta un mensaje de debug que permite al visualizador dividir en secciones la ejecución
	 */
	function seccion($mensaje, $proyecto=null)
	{
		return $this->registrar_mensaje("[SECCION] ".$mensaje, $proyecto, TOBA_LOG_DEBUG);    	
	}

	//------------------------------------------------------------------
	//---- Manejo de MASCARAS
	//------------------------------------------------------------------

	protected function mascara($nivel)
	{
		return (1 << $nivel);
	}

	protected function mascara_hasta($nivel)
	{
		return ((1 << ($nivel + 1)) - 1);
	}

	//------------------------------------------------------------------
	//---- Manejo de las fuentes de log
	//------------------------------------------------------------------
	
	function directorio_logs()
	{
		if (! isset($this->dir_logs)) {
			$id_instancia = toba_instancia::get_id();
			$this->dir_logs = toba_nucleo::toba_instalacion_dir()."/i__$id_instancia/p__{$this->proyecto_actual}/logs";
		}
		return $this->dir_logs;
	}	
	
	function set_directorio_logs($dir)
	{
		$this->dir_logs = $dir;	
	}

	protected function armar_encabezado()
	{
		$salto = "\r\n";
		$texto = self::separador.$salto;
		$texto .= "Fecha: ".date("d-m-Y H:i:s").$salto;
		if (class_exists('toba') && is_object(toba::solicitud())) {
			$texto .= "Operacion: ".toba::solicitud()->get_datos_item('item_nombre').$salto;
		}
		$usuario = self::get_usuario_actual();
		if (isset($usuario)) {
			$texto .= "Usuario: ".$usuario.$salto;
		}
		$texto .= "Version-PHP: ". phpversion().$salto;
		if (isset($_SERVER['SERVER_NAME'])) {
			$texto .= "Servidor: ".$_SERVER['SERVER_NAME'].$salto;
		}
		if (isset($_SERVER['REQUEST_URI'])) {
			$texto .= "URI: ".$_SERVER['REQUEST_URI'].$salto;	
		}		
		if (isset($_SERVER["HTTP_REFERER"])) {
			$texto .= "Referrer: ".$_SERVER["HTTP_REFERER"].$salto;
		}
		if (isset($_SERVER["REMOTE_ADDR"])) {
			$texto .= "Host: ".$_SERVER["REMOTE_ADDR"].$salto;			
		}
		if( php_sapi_name() === 'cli' ) {
			global $argv;
			$texto .= 'Ruta: '.getcwd().$salto;	
			$texto .= 'Argumentos: '.implode(' ', $argv).$salto;
		}
		return $texto;
	}
	
	protected function armar_mensajes()
	{
		$texto = '';
		$mascara_ok = $this->mascara_hasta( $this->nivel_maximo );
		for($a=0; $a<count($this->mensajes); $a++) {
			if( $mascara_ok & $this->mascara( $this->niveles[$a] ) ) {
				$texto .= "[" . $this->ref_niveles[$this->niveles[$a]] . 
						"][".$this->proyectos[$a]."] " . $this->mensajes[$a] . "\r\n";
			}			
		}
		return $texto;
	}
	
	/**
	 * Guarda los sucesos actuales en el sist. de archivos
	 */
	function guardar()
	{
		if ($this->activo) {
			$this->guardar_en_archivo("sistema.log");
		}
	}
	
	function guardar_en_archivo($archivo, $forzar_salida = false)
	{
		$salto = "\r\n";
		
		$texto = $this->armar_encabezado();
		$texto .= self::fin_encabezado.$salto;		
		$mensajes = $this->armar_mensajes();
		$hay_salida = (trim($mensajes) != '');
		if ($hay_salida || $forzar_salida) {
			$texto .= $mensajes;
			$this->guardar_archivo_log($texto, $archivo);
		}
	}
	
	protected function guardar_archivo_log($texto, $archivo)
	{
		$permisos = 0774;
		//--- Asegura que el path esta creado
		$path = $this->directorio_logs();
		$path_completo = $path ."/".$archivo;
		toba_manejador_archivos::crear_arbol_directorios($path, $permisos);

		$es_nuevo = false;
		if (!file_exists($path_completo)) {
			//Caso base el archivo no existe
			$this->anexar_a_archivo($texto, $path_completo);
			$es_nuevo = true;
		} else {
			//El archivo existe, ¿Hay que ciclarlo?
			$excede_tamanio = (filesize($path_completo) > apex_log_archivo_tamanio * 1024);
			if (apex_log_archivo_tamanio != null && $excede_tamanio) {
				$this->ciclar_archivos_logs($path, $archivo);
				$es_nuevo = true;
			}
			$this->anexar_a_archivo($texto, $path_completo);
		}
		
		if ($es_nuevo) {
			//Cambiar permisos
			@toba_manejador_archivos::chmod_recursivo($path, $permisos);
		}
	}
	
	protected function anexar_a_archivo($texto, $archivo)
	{
		$res = file_put_contents($archivo, "$texto\r\n", FILE_APPEND);
		if ($res === FALSE) {
			throw new toba_error("Imposible guardar el archivo de log '$archivo'. Chequee los permisos de escritura del usuario apache sobre esta carpeta/archivo");
		}
	}
	
	protected function ciclar_archivos_logs($path, $archivo)
	{
		if (apex_log_archivo_backup_cant == 0) {
			//Si es un unico archivo hay que borrarlo
			unlink($path."/".$archivo);
			return;
		}
		//Encuentra los archivos
		$patron = "/$archivo\.([0-9]+)/";
		$archivos = toba_manejador_archivos::get_archivos_directorio($path);
		sort($archivos);

		//¿Cual es el numero de cada uno?
		$ultimo = 0;
		$arch_ordenados = array();
		foreach ($archivos as $arch_actual) {
			$version = array();
			preg_match($patron, $arch_actual, $version);
			if (! empty($version) && count($version) > 1) {
				$pos = $version[1];
				$arch_ordenados[$pos] = $arch_actual;
				if ($pos > $ultimo) {
					$ultimo = $pos;
				}
			}
		}
		//Se determina el siguiente numero
		$sig = $ultimo + 1;
		
		//¿Hay que purgar algunos?
		$puede_purgar = (apex_log_archivo_backup_cant != -1);
		$debe_purgar = count($arch_ordenados) >= (apex_log_archivo_backup_cant -1);
		if ($puede_purgar && $debe_purgar) {
			ksort($arch_ordenados);
			reset($arch_ordenados);
			//Se dejan solo N-1 archivos			
			$a_purgar = count($arch_ordenados) - (apex_log_archivo_backup_cant - 1);
			while ($a_purgar > 0) {
				unlink(current($arch_ordenados));
				$a_purgar--;
				next($arch_ordenados);
			}
		}
	
		//Se procede a mover el archivo actual
		$path_completo = $path . "/" . $archivo;
		if (apex_log_archivo_backup_compr) {
			//Se comprime
			$nuevo = $path_completo . ".$sig.gz";
			toba_manejador_archivos::comprimir_archivo($path_completo, 5, $nuevo);
			unlink($path_completo);
		} else {
			$nuevo = $path_completo . ".$sig";
			rename($path_completo, $nuevo);
		}
	}

	/**
	 * Borra físicamente todos los archivos de log del proyecto actual
	 */
	function borrar_archivos_logs()
	{
		$patron = "/sistema.log/";
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->directorio_logs(), $patron);
		foreach ($archivos as $archivo) {
			unlink($archivo);			
		}
	}
	
}
?>