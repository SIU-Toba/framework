<?
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
	
	function get_logger()
	{
		global $solicitud;
		return $solicitud->log;
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