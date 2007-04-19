<?php
/**
 * Clase estática que contiene shortcuts a las clases centrales del nucleo
 * @package Centrales
 */
class toba
{
	static private $mensajes;
	static private $contexto_ejecucion;

	/**
	 * @return toba_nucleo
	 */
	static function nucleo()
	{
		return toba_nucleo::instancia();
	}

	/**
	 * @return toba_contexto_ejecucion
	 */
	static function contexto_ejecucion()
	{
		if (!isset(self::$contexto_ejecucion)) {
			$subclase = toba::proyecto()->get_parametro('contexto_ejecucion_subclase');
			$archivo = toba::proyecto()->get_parametro('contexto_ejecucion_subclase_archivo');
			if( $subclase && $archivo ) {
				require_once($archivo);
				self::$contexto_ejecucion = new $subclase();
			} else {
				self::$contexto_ejecucion = new toba_contexto_ejecucion();
			}
		}
		return self::$contexto_ejecucion;
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
	 * @return toba_memoria
	 */
	static function memoria()
	{
		return toba_memoria::instancia();
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
	 * Retorna la referencia al administrador de permisos globales
	 *	@return toba_derechos
	 */
	static function permisos()
	{
		return toba_derechos::instancia();
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
	static function db($id_fuente=null, $proyecto=null)
	{
		return toba_admin_fuentes::instancia()->get_fuente($id_fuente, $proyecto)->get_db();
	}

	/**
	 * Retorna una referencia al encriptador
	 * @return toba_encriptador
	 */
	static function encriptador()
	{
		return toba_encriptador::instancia();	
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
		return toba_manejador_sesiones::instancia()->sesion();
	}

	/**
	 * @return toba_usuario
	 */
	static function usuario()
	{
		return toba_manejador_sesiones::instancia()->usuario();
	}
	
	/**
	 * Retorna el objeto que contiene información del proyecto toba actual
	 * @return toba_proyecto
	 */
	static function proyecto($id_proyecto=null)
	{
		return toba_proyecto::instancia($id_proyecto);
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
	
	/**
	* @ignore
	* @return toba_manejador_sesiones
	*/
	static function manejador_sesiones()
	{
		return toba_manejador_sesiones::instancia();
	}		
	
/**
	 * Retorna el objeto que contiene información de los puntos de control
	 * @return toba_puntos_control
	 */
  static function puntos_control()
  {
  	return toba_puntos_control::instancia();
  }
}
?>