<?php
use SIU\ManejadorSalidaToba\toba_factory;

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
	static private $consultas_php;
	static private $rdi;
	static private $escaper;
	static private $output_handler;

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
	 * Una zona representa un menu alrededor de un concepto central. Utilizada por ejemplo para mostrar un menú de opciones relacionado con un cliente particular
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
			$clase = self::output()->get('Menu');
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
	 * La memoria contiene la información historica de la aplicación, enmascarando a $_GET y $_SESSION
	 * @return toba_memoria
	 */
	static function memoria()
	{
		return toba_memoria::instancia();
	}
	
	/**
	* Retorna el logger de mensajes internos
	* @return toba_logger
	*/
	static function logger()
	{
		return toba_logger::instancia();
	}

	/**
	* Retorna el logger de mensajes internos para servicios web
	* @return toba_logger_ws
	*/
	static function logger_ws()
	{
		return toba_logger_ws::instancia();
	}
	
	/**
	 * Permite hacer validaciones de permisos globales particulares sobre el usuario actual
	 * @return toba_derechos
	 */
	static function derechos()
	{
		return toba_derechos::instancia();
	}

	/**
	 * Clase que mantiene notificaciones al usuario a mostrarse en el página actual
	 * @return toba_notificacion
	 */
	static function notificacion()
	{
		return toba_notificacion::instancia();
	}

	/**
	 * Obtiene los mensajes del proyecto definidos en el editor, útiles para evitar fijar los mensajes del usuario en el código
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
	 * Permite medir el tiempo consumido por el pedido de página, dejar marcas y opcionalmente registrarlo en la base de la instancia
	 * @return toba_cronometro
	 */
	static function cronometro()
	{
		return toba_cronometro::instancia();	
	}

	/**
	 * Representa la sesión del usuario en la aplicacion
	 * @return toba_sesion
	 */
	static function sesion()
	{
		return toba_manejador_sesiones::instancia()->sesion();
	}

	/**
	 * Encapsula al usuario actualmente logueado a la instancia
	 * @return toba_usuario_basico
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
	 * Retorna el objeto que contiene información de los puntos de montaje
	 * @return toba_pms
	 */
	static function puntos_montaje()
	{
		return toba_pms::instancia();
	}
	
	/**
	 * Clase que mantiene el app launcher
	 * @return toba_app_launcher
	 */
	static function app_launcher()
	{
		return toba_app_launcher::instancia();
	}

	/**
	 * Retorna un componente datos_tabla de una tabla específica del sistema
	 * @param string $nombre_tabla
	 * @param string $fuente Fuente a la que pertenece la tabla, si no se especifica se utiliza la por defecto del proyecto
	 * @param boolean $reusar Indica si se reutiliza una instancia existente o se crea un objeto nuevo. Por defecto true.
	 * @return toba_datos_tabla
	 */
	static function tabla($nombre_tabla, $fuente = null, $reusar = true)
	{
		if(!isset($fuente)) {
			$fuente = toba_admin_fuentes::get_fuente_predeterminada(true);	
		}
		$id = array();
		$id['proyecto'] = toba_proyecto::get_id();
		$id['componente'] = toba_admin_fuentes::instancia()->get_fuente($fuente)->get_id_datos_tabla($nombre_tabla);
		//Se pide el dt con el cache activado asi evita duplicar las instancias
		$comp = toba_constructor::get_runtime($id, 'toba_datos_tabla', $reusar);

		if (! $comp->inicializado()) {
			$comp->inicializar();
		}
		return $comp;
	}

	/**
	 * Retorna un componente por INDICE
	 * @param string $indice
	 * @return toba_componente
	 */
	static function componente($indice)
	{
		$id['proyecto'] = toba_proyecto::get_id();
		$temp = toba::proyecto()->get_id_componente_por_indice($indice, $id['proyecto']);
		$id['componente'] = $temp['componente'];
		$comp = toba_constructor::get_runtime($id, $temp['clase'], true);
		if (! $comp->inicializado()) {
			$comp->inicializar();
		}
		return $comp;
	}

	/**
	 * Retorna un componente por ID
	 * @param integer $id
	 * @return toba_componente
	 */
	static function componente_por_id($id)
	{
		$id_comp['proyecto'] = toba_proyecto::get_id();
		$id_comp['componente'] = $id;
		$comp = toba_constructor::get_runtime($id_comp, null, true);
		if (! $comp->inicializado()) {
			$comp->inicializar();
		}
		return $comp;
	}


	/**
	 * Retorna un componente de tipo CONTROLADOR de NEGOCIO por INDICE
	 * @param string $indice
	 * @return toba_cn
	 */
	static function cn($indice)
	{
		$cn = self::componente($indice);
		if(! $cn instanceof toba_cn ) {
			throw new toba_error_def("Error cargando CN por INDICE. El componente identificado con la etiqueta '$indice' no es un CN.");
		}
		return $cn;
	}

	/**
	 * El perfil de datos permite restringir los datos que surgen desde la base de datos en base a una dimensión dada.
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
	 * El perfil funcional permite agrupar derechos y restricciones a acceder a determinados elementos de un proyecto.
	 * 
	 * @return toba_perfil_funcional
	 */
	static function perfil_funcional()
	{
		if (! isset(self::$perfil_funcional)) {
			self::$perfil_funcional = new toba_perfil_funcional();
		}
		return self::$perfil_funcional;
	}

	/**
	 * Permite programar tareas a ejecutarse automáticamente en el servidor
	 * @return toba_planificador_tareas
	 */
	static function planificador_tareas($proyecto=null)
	{
		return new toba_planificador_tareas($proyecto);
	}
	
	/**
	 * Retorna una clase de consultas php declarada en el editor
	 * @param string $clase Nombre de la clase
	 * @return mixed Instancia de la clase especificada
	 */
	static function consulta_php($clase)
	{
		if (!isset(self::$consultas_php[$clase])) {
			$datos = toba::proyecto()->get_info_consulta_php($clase);
			if( $datos['archivo'] ) {
				if (isset($datos['punto_montaje']) && toba::puntos_montaje()->existe_por_id($datos['punto_montaje'])) {
					$path_pm = toba::puntos_montaje()->get_por_id($datos['punto_montaje'])->get_path_absoluto();
					$archivo = $path_pm.'/'.$datos['archivo'];
				} else {
					$archivo = $datos['archivo'];
				}
				require_once($archivo);
				if($datos['archivo_clase'] && $datos['archivo_clase']!=''){
					$clase_php = $datos['archivo_clase'];
				} else {
					$clase_php = $clase;
				}
				self::$consultas_php[$clase] = new $clase_php();
			} else {
				throw new toba_error("La consulta_php solicitada no posee un archivo asociado");
			}
		}
		return self::$consultas_php[$clase];
	}
	
	/**
	 * Retorna un objeto capaz de encolar llamadas javascript
	 * @return toba_acciones_js
	 */
	static function acciones_js()
	{
		return toba_acciones_js::instancia();
	}
	
	/**
	 * @return toba_servicio_web_cliente_soap
	 */
	static function servicio_web($id, $opciones=array()) 
	{
		return toba_servicio_web_cliente_soap::conectar($id, $opciones);
	}

	/**
	 * @return toba_servicio_web_cliente_rest
	 */
	static function servicio_web_rest($id, $opciones=array())
	{
	    return toba_servicio_web_cliente_rest::conectar($id, $opciones);
	}

	/**
	 * Devuelve un contenedor para el manejo de gadgets
	 * @return toba_contenedor_gadgets
	 */
	static function contenedor_gadgets()
	{
	    return toba_contenedor_gadgets::instancia();
	}

	/**
	 * Devuelve una instancia de la clase RDICliente para poder operar contra un ECM
	 * @return RDICliente
	 */
	static function rdi()
	{
		if (!isset(self::$rdi)){
			self::$rdi = new toba_cliente_rdi();
		}
		self::$rdi->set_proyecto(self::proyecto());
		self::$rdi->set_instalacion(self::instalacion());
		return self::$rdi->get_cliente();
	}
	
	static function escaper()
	{
		if (!isset(self::$escaper)){
			self::$escaper = new toba_escapador(apex_default_charset);
		}
		return self::$escaper;
	}
	
	static function output(){
		
		if (!isset(self::$output_handler)){
			self::$output_handler = new toba_factory_service();
			self::$output_handler->registrarServicio(new toba_factory());			
		}
		return self::$output_handler;
	}
	
}
?>
