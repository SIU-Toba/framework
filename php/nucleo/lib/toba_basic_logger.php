<?php

define('TOBA_LOG_EMERGENCY',    0);     /** System is unusable */	
define('TOBA_LOG_ALERT',     1);     /** Action must be taken immediately */	
define('TOBA_LOG_CRIT',     2);     /** Critical conditions */
define('TOBA_LOG_ERROR',    3);     /** Error conditions */
define('TOBA_LOG_WARNING',  4);     /** Warning conditions */
define('TOBA_LOG_NOTICE',   5);     /** Normal but significant */
define('TOBA_LOG_INFO',     6);     /** Informational */
define('TOBA_LOG_DEBUG',    7);     /** Debug-level messages */

if (!defined('apex_log_archivo_tamanio')) { 
	define('apex_log_archivo_tamanio', 1024); 
}
if (!defined('apex_log_archivo_backup_cant')) { 
	define('apex_log_archivo_backup_cant', 10); 
}
if (!defined('apex_log_archivo_backup_compr')) { 
	define('apex_log_archivo_backup_compr', false); 
}
if (!defined('apex_log_error_log')) { 
	define('apex_log_error_log', true); 
}

if (!defined('apex_log_error_log_nivel')) { 
	define('apex_log_error_log_nivel', TOBA_LOG_ERROR); 
}

trait toba_basic_logger 
{	
	public static $separador = "-o-o-o-o-o-";
	public static $fin_encabezado = "==========";
	public static $limite_mensaje = 100000; //100 KB

	protected $ref_niveles = array("EMERGENCY" , "ALERT", "CRITICAL", "ERROR", "WARNING", "NOTICE", "INFO", "DEBUG");	
	protected $mensajes = array();
	protected $niveles = array();
	protected $proyectos = array();
	protected $proyecto_actual;
	
	protected $proximo = 0;
	protected $nivel_maximo = 7;
	protected $activo = true;
	
	protected $es_php_compatible = true;
	
	
	public function get_proyecto_actual()
	{
		if (class_exists('toba_proyecto')) {
			try {
				return toba_proyecto::get_id();			
			} catch (Exception $e) {
			}
		}
		return 'toba';
	}
	
	public function get_usuario_actual()
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
	public function desactivar()
	{
		$this->nivel_maximo = -1;
		$this->activo = false;
	}
	
	public function set_nivel($nivel)
	{
		$this->nivel_maximo = $nivel;
	}
		
	public function get_cantidad_mensajes()
	{
		return count($this->mensajes);
	}
	
	public function get_mensajes_minimo_nivel()
	{
		$cantidad = array();
		$minimo = $this->nivel_maximo + 1;
		foreach ($this->niveles as $nivel) {
			$cantidad[$nivel] = (! isset($cantidad[$nivel])) ? 0 : $cantidad[$nivel]+1;
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
		
	public function get_niveles()
	{
		return $this->ref_niveles;	
	}
	
	public function get_nivel()
	{
		return $this->nivel_maximo;
	}	
	
	//------------------------------------------------------------------
	//---- Manejo de las fuentes de log
	//------------------------------------------------------------------	
	public function directorio_logs()
	{
		if (! isset($this->dir_logs)) {
			$id_instancia = toba_instancia::get_id();
			$this->dir_logs = toba_nucleo::toba_instalacion_dir()."/i__$id_instancia/p__{$this->proyecto_actual}/logs";
		}
		return $this->dir_logs;
	}	
	
	public function set_directorio_logs($dir)
	{
		$this->dir_logs = $dir;	
	}
	
	//-----------------------------------------------------------------------------------------------------------//	
	//					METODOS AUXILIARES
	//-----------------------------------------------------------------------------------------------------------//
	protected function truncar_msg($msg)
	{
		if (strlen($msg) > self::$limite_mensaje) {
			$msg = substr($msg, 0, self::$limite_mensaje).
					"..TEXTO CORTADO POR EXCEDER EL LIMITE DE ".
					self::$limite_mensaje.
					" bytes";
		}
		return $msg;
	}
	
	protected function registrar_msg_cli($msg, $nivel)
	{
		if (PHP_SAPI != 'cli' && apex_log_error_log && $nivel <= apex_log_error_log_nivel) {
			$error_log_max = ini_get("log_errors_max_len");
			if (! isset($error_log_max) || !is_numeric($error_log_max) || strlen($error_log_max) <= 1) {
				$error_log_max = 1024;
			}
			$error_log_extra = "...SIGUE...";
			$msg_error_log = $msg;
			if (strlen($msg_error_log) > $error_log_max) {
				$msg_error_log = substr($msg_error_log, 0 , $error_log_max - strlen($error_log_extra));
				$msg_error_log .= $error_log_extra;
			}
			error_log($msg_error_log);
		}		
	}
	
	/**
	 * @ignore 
	 */
	protected function registrar_mensaje($mensaje, $proyecto, $nivel)
	{
		$msg = $this->extraer_mensaje($mensaje);
		$msg = $this->truncar_msg($msg);
		$this->registrar_msg_cli($msg, $nivel);
		
		$this->mensajes[$this->proximo] = $msg;
		$this->niveles[$this->proximo] = $nivel;
		$this->proyectos[$this->proximo]  = (isset($proyecto)) ? $proyecto : $this->proyecto_actual;
		$this->proximo++;
	}
	
	/**
	 * @ignore 
	 */	
	protected function extraer_mensaje($mensaje)
	{		
		if (is_object($mensaje)) {
			/*switch (true) {				
			case ($mensaje instanceof Exception) : echo "exception";break;
			case (method_exists($mensaje, 'getMessage')) : echo "getMessage"; break;
			case (method_exists($mensaje, 'tostring')) : echo "tostring"; break;
			case (method_exists($mensaje, '__tostring')): echo "__magicToString"; break;
			default: 
				echo "var_export";
			}*/
			
			
			if ($mensaje instanceof Exception) {
				/*$es_php_compatible = (false && version_compare(phpversion(), "5.1.0", ">="));				*/		//Va al constructor
				$con_parametros = (TOBA_LOG_DEBUG <= $this->nivel_maximo);		
				$texto = ($mensaje instanceof toba_error) ? $mensaje->get_mensaje_log() : $mensaje->getMessage();

				$res = get_class($mensaje).": ".$texto."\n";
				if ($this->es_php_compatible) {
					//Solo muestra parametros en modo DEBUG					
					$traza = $mensaje->getTrace();
					$traza[0]['file'] = $mensaje->getFile();
					$traza[0]['line'] = $mensaje->getLine();
					$res .= $this->construir_traza($con_parametros, $traza);
				} else {
					//Para php < 5.1 mostrar el string 
					$msg = $this->parsear_msg($mensaje->__toString(), $con_parametros);
					$res .= "\n[TRAZA]".$msg;
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
	
	protected function parsear_msg($mensaje, $parametros=true)
	{
		$er = "/\([a-zA-Z'\"\s].+\)/i";
		return ($parametros) ? $mensaje:  preg_replace($er, '(args ignored)', $mensaje);
	}
	
	protected function construir_traza($con_parametros=false, $pasos = null)
	{
		if (!isset($pasos)) {			
			$opciones = ($con_parametros) ? null: \DEBUG_BACKTRACE_IGNORE_ARGS;
			$pasos = debug_backtrace($opciones);
		}
		$html = "[TRAZA]\n";
		$html .= "\t<ul>\n";    
		foreach ($pasos as $paso) {
			$clase = (isset($paso['class'])) ? $paso['class'] : '';
			
			//Se obvia los pasos por esta clase
			if ($clase !== __CLASS__) {
				if (isset($paso['type'])) {
					$clase .= $paso['type'];
				}
				
				$html .= "\t<li><strong>$clase{$paso['function']}</strong><br />";
				if (isset($paso['file'])) {
					$html .= "Archivo: {$paso['file']}, línea {$paso['line']}<br />";
				}
				if ($con_parametros && ! empty($paso['args'])) {
					$html .= "Parámetros: <ol>";
					foreach ($paso['args'] as $arg) {
						$html .= "<li>";
						$html .= $this->armar_parametros_traza($arg);
						$html .= "</li>\n";
					}
					$html .= "\t</ol>\n";
				} 
				$html .= "\t</li>\n";
			}
		}
		$html .= "\t</ul>";
		//--- Una traza no puede exceder la mitad del limite de todo el mensaje
		if (strlen($html) > self::$limite_mensaje / 2) {
			$html = substr($html, 0, self::$limite_mensaje / 2).
					"\nTRAZA CORTADA POR EXCEDER EL LIMITE DE ".
					self::$limite_mensaje / 2 . " bytes";
		}
		return $html;
	}
	
	protected function armar_parametros_traza($argumento)
	{
		$html = '';
		if (is_object($argumento)) {
			$html .= 'Instancia de <em>'.get_class($argumento).'</em>';
		} elseif (is_array($argumento)) {
			foreach($argumento as $arg) {
				$html .= $this->armar_parametros_traza($arg);
			}
		} else {
			$html .= highlight_string(print_r($argumento, true), true);
		}		
		return $html;
	}
	
	protected function armar_mensajes()
	{
		$texto = '';
		$mascara_ok = $this->mascara_hasta($this->nivel_maximo);
		for($a=0; $a < count($this->mensajes); $a++) {
			if( $mascara_ok & $this->mascara($this->niveles[$a])) {
				$texto .= "[" . $this->ref_niveles[$this->niveles[$a]] . 	"][".$this->proyectos[$a]."] " . $this->mensajes[$a] . PHP_EOL;
			}			
		}
		return $texto;
	}
	
	protected function mensajes()
	{
		return $this->mensajes;
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
		
	protected function ciclar_archivos_logs($path, $archivo)
	{
		if (apex_log_archivo_backup_cant == 0) {
			//Si es un unico archivo hay que borrarlo
			unlink($path."/".$archivo);
			return;
		}
		//Encuentra los archivos
		$patron = "/$archivo\.([0-9]+)/";
		$archivos = toba_manejador_archivos::get_archivos_directorio($path, $patron);
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
		$this->purgar_archivos_viejos($arch_ordenados);
	
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

	protected function purgar_archivos_viejos($lista_archivos)
	{
		//¿Hay que purgar algunos?
		$puede_purgar = (apex_log_archivo_backup_cant != -1);
		$a_purgar = count($lista_archivos) - (apex_log_archivo_backup_cant -1); 			//Se dejan solo N-1 archivos
		if ($puede_purgar && $a_purgar > 0) {
			ksort($lista_archivos);
			reset($lista_archivos);
			 do {
				unlink(current($lista_archivos));
				$a_purgar--;
				next($lista_archivos);
			} while ($a_purgar > 0);
		}		
	}
	
	protected function anexar_a_archivo($texto, $archivo)
	{
		$res = file_put_contents($archivo, "$texto\r\n", FILE_APPEND);
		if ($res === FALSE) {
			throw new toba_error("Imposible guardar el archivo de log '$archivo'. Chequee los permisos de escritura del usuario apache sobre esta carpeta/archivo");
		}
	}	
}