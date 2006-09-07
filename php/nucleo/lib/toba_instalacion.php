<?php

/**
 * Clase que mantiene información común a la actual instalación de toba
 * Enmascara principalmente al archivo de configuración instalacion.ini
 * 
 * @package Base
 */
class toba_instalacion
{
	static private $instancia;
	
	/**
	 * @return toba_instalacion
	 */
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_instalacion();	
		}
		return self::$instancia;	
	}

	private function __construct()
	{
		if (!isset($_SESSION['toba']['instalacion'])) {
			$_SESSION['toba']['instalacion'] = parse_ini_file( toba_dir() . '/instalacion/instalacion.ini');
		}
	}
	
	/**
	 * Destructor de la clase
	 */
	function limpiar_memoria()
	{
		unset($_SESSION['toba']['instalacion']);
		self::$instancia = null;
	}

	/**
	 * Claves utilizadas para encriptar el querystring y cosas en la base
	 * @return Arreglo asociativo db=>, get=>
	 */
	function get_claves_encriptacion()
	{
		if (isset($_SESSION['toba'])) {
			$claves['db'] = $_SESSION['toba']['instalacion']['clave_db'];
			$claves['get'] = $_SESSION['toba']['instalacion']['clave_querystring'];
			return $claves;
		}
	}
	
	/**
	 * Retorna un número que representa al grupo de trabajo y con el cual se indexaran los metadatos
	 * Pensado para poder hacer trabajos concurrentes entre grupos de trabajo geograficamente distribuidos
	 *
	 * @return integer
	 */
	function get_id_grupo_desarrollo()
	{
		if (isset($_SESSION['toba'])) {
			return $_SESSION['toba']['instalacion']['id_grupo_desarrollo'];
		}		
	}
	
	/**
	 * Retorna el comando que usa la instalación para editar archivos php en forma interactiva
	 */
	function get_editor_php() 
	{
		if (isset($_SESSION['toba']['instalacion']['editor_php'])) {
			return $_SESSION['toba']['instalacion']['editor_php'];
		}
	}
	
	/**
	 * La instalación trabaja con las librerías js comprimidas?
	 */
	function es_js_comprimido()
	{
		if (isset($_SESSION['toba']['instalacion']['js_comprimido'])) {
			return $_SESSION['toba']['instalacion']['js_comprimido'];
		} else {
			return false;	
		}
	}
	
	/**
	 * Número de versión de Toba
	 */
	function get_numero_version()
	{
		return file_get_contents(toba_dir()."/VERSION");
	}
	
	/**
	 * Retorna el path de la instalación de toba
	 */
	function get_path()
	{
		return $_SESSION['toba']["path"];
	}	
	
	/**
	 * Retorna un path donde incluir archivos temporales, el path no es navegable
	 */
	function get_path_temp()
	{
		return toba_dir()."/temp";	
	}
	
	
}
?>