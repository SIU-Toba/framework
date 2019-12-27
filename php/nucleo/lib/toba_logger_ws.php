<?php

/**
 * Mantiene una serie de sucesos generados durante un WS no visibles al usuario y los almacena para el posterior analisis
 * Los sucesos tienen una categoria (debug, info, error, etc.) y el proyecto que la produjo
 * 
 * @package Debug
 */	
class toba_logger_ws 
{			
	/**
	 * Este es un singleton por proyecto
	 * @return logger
	 */
	static function instancia($proyecto=null)
	{		
		$logger = logger::instancia($proyecto);
		$logger->set_logger_instance(toba_ws_handler_log::instancia($proyecto), $proyecto);		
		$logger->set_id_solicitud(toba::solicitud()->get_id());
		return $logger;
	}	
}
?>
