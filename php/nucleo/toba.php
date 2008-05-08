<?php
/**
 * Clase estática que contiene shortcuts a las clases centrales del nucleo
 * Se utiliza como toba::zona()->cargar, toba::logger()->trace() o toba::tabla('mi_tabla')->...
 * @package Centrales
 */
class toba
{
	static private $mensajes;
	static private $menu;
	static private $contexto_ejecucion;
	static private $perfil_de_datos;
	static private $perfil_funcional;
	
	static private $cn = array();

	/**
	 * El núcleo es la raiz de ejecución, no tiene mayor utilidad para los proyectos consumidores
	 * @return toba_nucleo
	 */
	static function nucleo()
	{
		return toba_nucleo::instancia();
	}

	/**
	 * El contexto de ejeución permite al proyecto escribir comportamientos generales en las ventanas de inicio/fin de ejecución del pedido de página
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
	 * Una solicitud es la representación de una operación o item accedida por un usuario en runtime Contiene e instancia a los componentes de la operación
	 * @return toba_solicitud_web
	 */
	static function solicitud()
	{
		return toba_nucleo::instancia()->get_solicitud();	
	}
	
	/**
	 * Una zona representa un menu alrededor de un concepto central. Utilizada por ejemplo para mostrar un menú de opciones relacionado con un cliente particular.
	 * @return toba_zona
	 */
	static function zona()
	{
		return toba_nucleo::instancia()->get_solicitud()->zona();
	}
	
	/**
	 * Clase que se encarga de mostrar el menú de operaciones del proyecto
	 * @return toba_menu_css
	 */
	static function menu()
	{
		if (! isset(self::$menu)) {
			$archivo_menu = toba::proyecto()->get_parametro('menu_archivo');
			$clase = basename($archivo_menu, ".php");
			self::$menu = new $clase();
		}
		return self::$menu;
	}
	
	/**
	 * Permite construir links a esta u otras operaciones, ya sea en forma de URL u objetos que la representan
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
	static function derechos()
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
	 * @return toba_db_postgres7
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
	
	/**
	 * Retorna un componente datos_tabla de una tabla específica del sistema
	 * @param string $nombre_tabla
	 * @param string $fuente Fuente a la que pertenece la tabla, si no se especifica se utiliza la por defecto del proyecto
	 * @return toba_datos_tabla
	 */
	static function tabla($nombre_tabla, $fuente = null)
	{
		if(!isset($fuente)) {
			$fuente = toba_admin_fuentes::get_fuente_predeterminada(true);	
		}
		$id = array();
		$id['proyecto'] = toba_proyecto::get_id();
		$id['componente'] = toba_admin_fuentes::instancia()->get_fuente($fuente)->get_id_datos_tabla($nombre_tabla);
		//Se pide el dt con el cache activado asi evita duplicar las instancias
		return toba_constructor::get_runtime($id, 'toba_datos_tabla', true);
	}

	/**
	 * Retorna un componente de tipo Controlador de Negocios
	 * @param string $nombre_cn
	 * @return toba_cn
	 */
	static function cn($nombre)
	{
		$proyecto = toba_proyecto::get_id();
		if(! self::$cn ) {
			self::$cn = toba_proyecto_db::get_mapeo_cn($proyecto);
		}
		if( isset(self::$cn[$nombre] ) ) {
			$id = array();
			$id['proyecto'] = $proyecto;
			$id['componente'] = self::$cn[$nombre];
			return toba_constructor::get_runtime($id, 'toba_cn', true);
		} else {
			throw new toba_error('El cn [$nombre] no puede ser encontrado.');
		}
	}

	/**
	 * @return toba_perfil_datos
	 */
	static function perfil_de_datos()
	{
		if (! isset(self::$perfil_de_datos)) {
			self::$perfil_de_datos = new toba_perfil_datos();
		}
		return self::$perfil_de_datos;
	}

	/**
	 * @return toba_perfil_funcional
	 */
	static function perfil_funcional()
	{
		if (! isset(self::$perfil_funcional)) {
			self::$perfil_funcional = new toba_funcional();
		}
		return self::$perfil_funcional;
	}

}
?>