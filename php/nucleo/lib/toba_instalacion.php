<?php

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
	
	function limpiar_memoria()
	{
		unset($_SESSION['toba']['instalacion']);
		self::$instancia = null;
	}

	function get_claves_encriptacion()
	{
		if (isset($_SESSION['toba'])) {
			$claves['db'] = $_SESSION['toba']['instalacion']['clave_querystring'];
			$claves['get'] = $_SESSION['toba']['instalacion']['clave_db'];
			return $claves;
		}
	}
	
	function get_id_grupo_desarrollo()
	{
		if (isset($_SESSION['toba'])) {
			return $_SESSION['toba']['instalacion']['id_grupo_desarrollo'];
		}		
	}
	
	function get_editor_php() 
	{
		if (isset($_SESSION['toba']['instalacion']['editor_php'])) {
			return $_SESSION['toba']['instalacion']['editor_php'];
		}
	}
	
	function es_js_comprimido()
	{
		if (isset($_SESSION['toba']['instalacion']['js_comprimido'])) {
			return $_SESSION['toba']['instalacion']['js_comprimido'];
		} else {
			return false;	
		}
	}
	
	function get_numero_version()
	{
		return file_get_contents(toba_dir()."/VERSION");
	}
}
?>