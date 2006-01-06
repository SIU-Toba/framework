<?
require_once('nucleo/nucleo_toba.php');

/**
 * Obtencion de referencias a los objetos centrales del TOBA
 */
class toba
{
	static function get_nucleo()
	{
		return nucleo_toba::instancia();
	}
	
	static function get_solicitud()
	{
		return nucleo_toba::instancia()->get_solicitud();	
	}
	
	static function get_vinculador()
	{
		return toba::get_solicitud()->vinculador;
	}
	
	/**
	 * @return hilo
	 */
	static function get_hilo()
	{
		if (isset(toba::get_solicitud()->hilo)) {
			return toba::get_solicitud()->hilo;
		}
	}
	
	/**
	*	Retorna el logger de eventos de toba
	*	@return logger
	*/
	static function get_logger()
	{
		return logger::instancia();
	}

	static function get_cola_mensajes()
	{
		global $solicitud;
		return $solicitud->cola_mensajes;
	}

	static function get_fuente($fuente, $ado=null)
	{
		global $db, $ADODB_FETCH_MODE;	
		if(isset($ado)){
			$ADODB_FETCH_MODE = $ado;
		}else{
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		}
		if(!isset($db[$fuente])){
			throw new excepcion_toba("La fuente de datos no se encuentra disponible." );
		}
		return $db[$fuente];
	}
	
	static function get_db($fuente, $ado=null)
	{
		return dba::get_db($fuente);
	}

	static function get_encriptador()
	{
		return encriptador::instancia();	
	}

	static function get_cronometro()
	{
		return cronometro::instancia();	
	}

}
?>