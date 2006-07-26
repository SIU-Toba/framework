<?
require_once('nucleo/nucleo_toba.php');

/**
 * Obtencion de referencias a los objetos centrales del TOBA
 */
class toba
{
	static private $sesion;

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
	 * @return zona
	 */
	static function get_zona()
	{
		return nucleo_toba::instancia()->get_solicitud()->zona();
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

	/**
	 * Retorna una referencia a una fuente de datos declarada en el proyecto
	 * @param string $id_fuente
	 * @return fuente_de_datos
	 */
	static function get_fuente($id_fuente=null)
	{
		return administrador_fuentes::instancia()->get_fuente($id_fuente);
	}
	
	/**
	 * Retorna una referencia a una base de datos
	 * @param string $id_fuente
	 * @return db
	 */
	static function get_db($id_fuente=null)
	{
		return administrador_fuentes::instancia()->get_fuente($id_fuente)->get_db();
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

	/**
	 * @return sesion_toba
	 */
	static function get_sesion()
	{
		if (!isset(self::$sesion)) {
			$subclase = info_proyecto::instancia()->get_parametro('sesion_subclase');
			$archivo = info_proyecto::instancia()->get_parametro('sesion_subclase_archivo');
			if( $subclase && $archivo ) {
				require_once($archivo);
				self::$sesion = call_user_func(array($subclase,'instancia'),$subclase);
			} else {
				self::$sesion = sesion_toba::instancia();
			}
		}
		return self::$sesion;
	}

	/**
	 * @return usuario_toba
	 */
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
