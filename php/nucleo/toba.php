<?php
require_once('nucleo/toba_nucleo.php');

/**
 * Clase estática que contiene shorcuts a las clases centrales del nucleo
 */
class toba
{
	static private $sesion;
	static private $mensajes;

	/**
	 * @return toba_nucleo
	 */
	static function nucleo()
	{
		return toba_nucleo::instancia();
	}
	
	/**
	 * @return toba_solicitud_web
	 */
	static function solicitud()
	{
		return toba_nucleo::instancia()->get_solicitud();	
	}
	
	/**
	 * @return toba_zona
	 */
	static function zona()
	{
		return toba_nucleo::instancia()->get_solicitud()->zona();
	}
	
	/**
	 * @return toba_vinculador
	 */
	static function vinculador()
	{
		return toba_vinculador::instancia();
	}
	
	/**
	 * @return toba_hilo
	 */
	static function hilo()
	{
		return toba_hilo::instancia();
	}
	
	/**
	*	Retorna el logger de mensajes internos
	*	@return toba_logger
	*/
	static function logger()
	{
		return toba_logger::instancia();
	}
	
	/**
	 * Retorna la referencia al administrador de permisos especiales
	 *	@return toba_permisos
	 */
	static function permisos()
	{
		return toba_permisos::instancia();
	}

	/**
	 * @return toba_notificacion
	 */
	static function notificacion()
	{
		return toba_notificacion::instancia();
	}

	/**
	 * @return toba_mensajes
	 */	
	static function mensajes()
	{
		if (!isset(self::$mensajes)) {
			self::$mensajes = new toba_mensajes();
		}
		return self::$mensajes;
	}
	
	/**
	 * Retorna una referencia a una fuente de datos declarada en el proyecto
	 * @param string $id_fuente
	 * @return toba_fuente_datos
	 */
	static function fuente($id_fuente=null)
	{
		return toba_admin_fuentes::instancia()->get_fuente($id_fuente);
	}
	
	/**
	 * Retorna una referencia a una base de datos
	 * @param string $id_fuente
	 * @return toba_db
	 */
	static function db($id_fuente=null)
	{
		return toba_admin_fuentes::instancia()->get_fuente($id_fuente)->get_db();
	}

	static function encriptador()
	{
		return encriptador::instancia();	
	}

	/**
	 * @return toba_cronometro
	 */
	static function cronometro()
	{
		return toba_cronometro::instancia();	
	}

	/**
	 * @return toba_sesion
	 */
	static function sesion()
	{
		if (!isset(self::$sesion)) {
			$subclase = toba::proyecto()->get_parametro('sesion_subclase');
			$archivo = toba::proyecto()->get_parametro('sesion_subclase_archivo');
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
	static function usuario()
	{
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');
		$archivo = toba::proyecto()->get_parametro('usuario_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
			return call_user_func(array($subclase,'instancia'));
		} else {
			return toba_usuario::instancia();
		}
	}
	
	/**
	 * Retorna el objeto que contiene información del proyecto toba actual
	 * @return toba_proyecto
	 */
	static function proyecto()
	{
		return toba_proyecto::instancia();
	}
	
	/**
	 * Retorna el objeto que contiene información de la instancia toba actual
	 * @return toba_instancia
	 */
	static function instancia()
	{
		return toba_instancia::instancia();
	}	
	
	/**
	 * Retorna el objeto que contiene información de la instalacion toba actual
	 * @return toba_instalacion
	 */
	static function instalacion()
	{
		return toba_instalacion::instancia();
	}	
	
	
}
?>
