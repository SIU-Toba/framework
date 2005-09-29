<?
require_once('nucleo/browser/clases/interfaces.php');

/*
	Obtencion de referencias a los objetos centrales del TOBA
*/

class toba
{
	function get_solicitud()
	{
		global $solicitud;
		return $solicitud;
	}
	
	function get_vinculador()
	{
		global $solicitud;
		return $solicitud->vinculador;
	}
	
	function get_hilo()
	{
		global $solicitud;
		return $solicitud->hilo;
	}
	
	/**
	*	Retorna el logger de eventos de toba
	*	@return logger
	*/
	function get_logger()
	{
		global $logger;
		if(!isset($logger)){
			$logger = new logger();
		}
		return $logger;
	}

	function get_cola_mensajes()
	{
		global $solicitud;
		return $solicitud->cola_mensajes;
	}

	function get_fuente($fuente, $ado=null)
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
	
	function get_db($fuente, $ado=null)
	{
		return dba::get_db($fuente);
	}

	function get_fuente_datos($fuente)
	{
		$fuente = toba::get_fuente($fuente);
		return $fuente[apex_db];
	}

	function get_encriptador()
	{
		global $encriptador;
		return $encriptador;	
	}

	function get_info_instancia($id)
	{
		global $instancia;
		if(!isset($instancia[$id])){
			throw new excepcion_toba("La instancia no se encuentra definida." );
		}
		return $instancia[$id];
	}
}
?>