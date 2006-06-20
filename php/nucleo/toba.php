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
	
	/**
	 * @return solicitud_web
	 */
	static function get_solicitud()
	{
		return nucleo_toba::instancia()->get_solicitud();	
	}
	
	/**
	 * @return vinculador
	 */
	static function get_vinculador()
	{
		return vinculador::instancia();
	}
	
	/**
	 * @return hilo
	 */
	static function get_hilo()
	{
		return hilo::instancia();
	}
	
	/**
	*	Retorna el logger de eventos de toba
	*	@return logger
	*/
	static function get_logger()
	{
		return logger::instancia();
	}
	
	/**
	 * Retorna la referencia al administrador de permisos particulares
	 *	@return permisos
	 */
	static function get_permisos()
	{
		return permisos::instancia();
	}

	/**
	 * @return cola_mensajes
	 */
	static function get_cola_mensajes()
	{
		return cola_mensajes::instancia();
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
	
	/**
	 * Retorna la referencia a la librería que administra las consultas y comandos a la fuente de datos
	 *
	 * @param string $fuente
	 * @return db_postgres7
	 */
	static function get_db($fuente, $ado=null)
	{
		return dba::get_db($fuente);
	}

	static function get_encriptador()
	{
		return encriptador::instancia();	
	}

	/**
	 * @return cronometro
	 */
	static function get_cronometro()
	{
		return cronometro::instancia();	
	}

	static function get_sesion()
	{
		$subclase = info_proyecto::instancia()->get_parametro('sesion_subclase');
		$archivo = info_proyecto::instancia()->get_parametro('sesion_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
			return call_user_func(array($subclase,'instancia'));
		} else {
			return sesion_toba::instancia();
		}
	}

	static function get_usuario()
	{
		$subclase = info_proyecto::instancia()->get_parametro('usuario_subclase');
		$archivo = info_proyecto::instancia()->get_parametro('usuario_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
			return call_user_func(array($subclase,'instancia'));
		} else {
			return usuario_toba::instancia();
		}
	}
}
?>