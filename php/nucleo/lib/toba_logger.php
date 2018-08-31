<?php

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
 *  - TOBA_LOG_ALERT: 1
 *  - TOBA_LOG_CRIT: 2
 *  - TOBA_LOG_ERROR: 3
 *  - TOBA_LOG_WARNING: 4
 *  - TOBA_LOG_NOTICE: 5
 *  - TOBA_LOG_INFO: 6
 *  - TOBA_LOG_DEBUG: 7
 *
 *  Recomendaciones de uso:
 *
 *  emergencia: falta de funcionamiento absoluta
 *  alerta: falta algun componente esencial, por ej: base de datos (requiere intervencion de IT)
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
	use \toba_basic_logger;
	
	static protected $errores_manejables = array(E_WARNING, E_NOTICE, E_DEPRECATED);
	static protected $instancia;
	
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
	 * Inserta un mensaje de debug que permite al visualizador dividir en secciones la ejecución
	 */
	function seccion($mensaje, $proyecto=null)
	{
		return $this->log(TOBA_LOG_DEBUG, "[SECCION] ".$mensaje, array('proyecto' => $proyecto));
	}

	public function log($nivel, $mensaje, array $context = array())
	{
		if ($nivel <= $this->nivel_maximo) {
			$proyecto = (isset($context['proyecto'])) ? $context['proyecto'] :$this->proyecto_actual;
			$this->registrar_mensaje($mensaje, $proyecto, $nivel);
		}
	}
	
	//-----------------------------------------------------------------------------------------------------------------//
	//				METODOS PUBLICOS
	//-----------------------------------------------------------------------------------------------------------------//
	
	
	function modo_debug()
	{
		return ($this->get_nivel() == TOBA_LOG_DEBUG);
	}	
	
	//----------------------------------------------------------------------------------------------------------------------------------//
	//						METODOS AUXILIARES
	//----------------------------------------------------------------------------------------------------------------------------------//
	
	protected function armar_encabezado()
	{
		$salto = "\r\n";
		$texto = self::$separador.$salto;
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
		$texto .= self::$fin_encabezado.$salto;		
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