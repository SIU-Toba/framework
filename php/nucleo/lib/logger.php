<?
define('TOBA_LOG_EMERG',    0);     /** System is unusable */
define('TOBA_LOG_ALERT',    1);     /** Immediate action required */
define('TOBA_LOG_CRIT',     2);     /** Critical conditions */
define('TOBA_LOG_ERR',      3);     /** Error conditions */
define('TOBA_LOG_WARNING',  4);     /** Warning conditions */
define('TOBA_LOG_NOTICE',   5);     /** Normal but significant */
define('TOBA_LOG_INFO',     6);     /** Informational */
define('TOBA_LOG_DEBUG',    7);     /** Debug-level messages */
/*

	Ver tema de mascaras y niveles
*/
class logger
{
	var $solicitud;
    //var $_mask = TOBA_LOG_ALL;
	
	function __construct($solicitud)
	{
		$this->solicitud = $solicitud;
	}	

	function registrar_mensaje($mensaje, $nivel)
	{
		
	}

	function registrar_excepcion($excepcion)
	{
		ei_arbol($excepcion->obtener_resumen(),"Excepcion");
	}

	//-------------- Entradas para los distintos tipos de error

    function emerg($message)
    {
        return $this->log($message, TOBA_LOG_EMERG);
    }

    function alert($message)
    {
        return $this->log($message, TOBA_LOG_ALERT);
    }

    function crit($message)
    {
        return $this->log($message, TOBA_LOG_CRIT);
    }

    function err($message)
    {
        return $this->log($message, TOBA_LOG_ERR);
    }

    function warning($message)
    {
        return $this->log($message, TOBA_LOG_WARNING);
    }

    function notice($message)
    {
        return $this->log($message, TOBA_LOG_NOTICE);
    }

    function info($message)
    {
        return $this->log($message, TOBA_LOG_INFO);
    }

    function debug($message)
    {
        return $this->log($message, TOBA_LOG_DEBUG);
    }
}


?>