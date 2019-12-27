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
	protected $ref_niveles = array("EMERGENCY" , "ALERT", "CRITICAL", "ERROR", "WARNING", "NOTICE", "INFO", "DEBUG");	
	protected $mensajes = array();
	protected $niveles = array();
	protected $proyectos = array();
	protected $proyecto_actual;
	
	protected $nivel_maximo = 7;	
	protected $es_php_compatible = true;	
	
	static public function get_proyecto_actual()
	{
		if (class_exists('toba_proyecto')) {
			try {
				return toba_proyecto::get_id();			
			} catch (Exception $e) {
			}
		}
		return 'toba';
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
	
	function modo_debug()
	{
		return ($this->get_nivel() == TOBA_LOG_DEBUG);
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
	
	/**
	 * @ignore 
	 * @deprecated since version 3.2.3
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
	 * @deprecated since version 3.2.3 
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
	
	/**
	 * @deprecated since version 3.2.3
	 */
	protected function parsear_msg($mensaje, $parametros=true)
	{
		$er = "/\([a-zA-Z'\"\s].+\)/i";
		return ($parametros) ? $mensaje:  preg_replace($er, '(args ignored)', $mensaje);
	}
	
	/**
	 * 
	 * @param type $con_parametros
	 * @param type $pasos
	 * @return string
	 * @deprecated since version 3.2.3
	 */
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
	
	/**
	 * 
	 * @param type $argumento
	 * @return type
	 * @deprecated since version 3.2.3
	 */
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
	
	/**
	 * Arma un string con los mensajes y sus datos
	 * @return string
	 * @deprecated since version 3.2.3
	 */
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
		
	//------------------------------------------------------------------
	//------ Entradas para los distintos tipos de error
	//------------------------------------------------------------------
	
	/**
	 * Registra un suceso de emergencia (hecatombe)
	 */
	function emergencia($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_EMERGENCY, $mensaje, array('proyecto' => $proyecto));		
	}
	
	/**
	 * Registra un suceso de alerta (un error que requiere intervencion humana)
	 */
	function alerta($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_ALERT, $mensaje, array('proyecto' => $proyecto));		
	}
	
	/**
	 * Registra un suceso CRITICO (un error muy grave)
	 */
	function crit($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_CRIT, $mensaje, array('proyecto' => $proyecto));		
	}

	/**
	 * Registra un error en la apl., este nivel es que el se usa en las excepciones
	 */    
	function error($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_ERROR, $mensaje, array('proyecto' => $proyecto));		
	}

	/**
	 * Registra un suceso no contemplado pero que posiblemente no afecta la correctitud del proceso
	 */
	function warning($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_WARNING, $mensaje, array('proyecto' => $proyecto));		
	}

	/**
	 * Registra un suceso no contemplado que no es critico para la aplicacion
	 */    
	function notice($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_NOTICE, $mensaje, array('proyecto' => $proyecto));		
	}

	/**
	 * Registra un suceso netamente informativo, para una inspección posterior
	 */
	function info($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_INFO, $mensaje, array('proyecto' => $proyecto));		
	}

	/**
	 * Registra un suceso útil para rastrear problemas o bugs en la aplicación
	 */
	function debug($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_DEBUG, $mensaje, array('proyecto' => $proyecto));
	}

	//-----------------------------------------------------------------------------------------------//
	//		Entradas extra para tipos propios
	//-----------------------------------------------------------------------------------------------//
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
	 * Muestra la traza de ejecucion actual en el logger
	 */
	function trace($con_parametros=false, $proyecto = null)
	{
		$this->debug($this->construir_traza($con_parametros), array('proyecto' => $proyecto));
	}

	/**
	 * Dumpea el contenido de una variable al logger
	 */
	function var_dump($variable, $proyecto = null)
	{
		$this->debug(var_export($variable, true), array('proyecto' =>$proyecto));
	}
	
	/**
	 * Inserta un mensaje de debug que permite al visualizador dividir en secciones la ejecución
	 */
	function seccion($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_DEBUG, "[SECCION] ".$mensaje, array('proyecto' => $proyecto));
	}
}