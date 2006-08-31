<?php
require_once('nucleo/toba_nucleo.php');

/**
 * Clase estática que contiene shorcuts a las clases centrales del nucleo
 */
class toba
{
	static private $sesion;

	/**
	 * @return toba_nucleo
	 */
	static function get_nucleo()
	{
		return toba_nucleo::instancia();
	}
	
	/**
	 * @return toba_solicitud_web
	 */
	static function get_solicitud()
	{
		return toba_nucleo::instancia()->get_solicitud();	
	}
	
	/**
	 * @return zona
	 */
	static function get_zona()
	{
		return toba_nucleo::instancia()->get_solicitud()->zona();
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
	*	Retorna el logger de mensajes internos
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
		return toba_admin_fuentes::instancia()->get_fuente($id_fuente);
	}
	
	/**
	 * Retorna una referencia a una base de datos
	 * @param string $id_fuente
	 * @return db
	 */
	static function get_db($id_fuente=null)
	{
		return toba_admin_fuentes::instancia()->get_fuente($id_fuente)->get_db();
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
	 * @return toba_sesion
	 */
	static function get_sesion()
	{
		if (!isset(self::$sesion)) {
			$subclase = toba_proyecto::instancia()->get_parametro('sesion_subclase');
			$archivo = toba_proyecto::instancia()->get_parametro('sesion_subclase_archivo');
			if( $subclase && $archivo ) {
				require_once($archivo);
				self::$sesion = call_user_func(array($subclase,'instancia'),$subclase);
			} else {
				self::$sesion = toba_sesion::instancia();
			}
		}
		return self::$sesion;
	}

	/**
	 * @return toba_usuario
	 */
	static function get_usuario()
	{
		$subclase = toba_proyecto::instancia()->get_parametro('usuario_subclase');
		$archivo = toba_proyecto::instancia()->get_parametro('usuario_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
			return call_user_func(array($subclase,'instancia'));
		} else {
			return toba_usuario::instancia();
		}
	}
}
?>
