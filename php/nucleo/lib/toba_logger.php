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
	//use \toba_basic_logger;
	
	static protected $errores_manejables = array(E_WARNING, E_NOTICE, E_DEPRECATED);
	/**
	 * Este es un singleton por proyecto
	 * @return logger
	 */
	static function instancia($proyecto=null)
	{
		$logger = logger::instancia($proyecto);
		$logger->set_logger_instance(toba_handler_log::instancia($proyecto), $proyecto);
		if (class_exists('toba') && is_object(toba::solicitud())) {
			$logger->set_id_solicitud(toba::solicitud()->get_id());
		}		
		return $logger;
	}

	static function manejador_errores_recuperables($error_nro, $error_string, $error_archivo = '', $error_linea = 0)
	{
		$instancia  = self::instancia();
		$instancia->error("Se produjo una salida inesperada: \n ($error_nro) $error_string \n En el archivo $error_archivo ($error_linea)");
		if (! in_array($error_nro, self::$errores_manejables)) {
			return false;						// Que lo trate el manejador de errores de PHP ya que no es uno de los que dejan texto nomas.
		}
	}
}
?>