<?php

/**
 * Clase que mantiene información común a la actual instalación de toba
 * Enmascara principalmente al archivo de configuración instalacion.ini
 * 
 * @package Centrales
 */
class toba_instalacion
{
	static private $instancia;
	private $memoria;								//Referencia al segmento de $_SESSION asignado
	
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

	static function eliminar_instancia()
	{
		self::$instancia = null;
	}

	private function __construct()
	{
		$this->memoria =& toba::manejador_sesiones()->segmento_info_instalacion();
		if(!$this->memoria) {
			$this->memoria = parse_ini_file( toba_dir() . '/instalacion/instalacion.ini');
		}
	}
	
	/**
	 * Claves utilizadas para encriptar el querystring y cosas en la base
	 * @return Arreglo asociativo db=>, get=>
	 */
	function get_claves_encriptacion()
	{
		if (isset($this->memoria)) {
			$claves['db'] = $this->memoria['clave_db'];
			$claves['get'] = $this->memoria['clave_querystring'];
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
		if (isset($this->memoria)) {
			return $this->memoria['id_grupo_desarrollo'];
		}		
	}
	
	/**
	 * Retorna el comando que usa la instalación para editar archivos php en forma interactiva
	 */
	function get_editor_php() 
	{
		if (isset($this->memoria['editor_php'])) {
			return $this->memoria['editor_php'];
		}
	}
	
	/**
	 * La instalación trabaja con las librerías js comprimidas?
	 */
	function es_js_comprimido()
	{
		if (isset($this->memoria['js_comprimido'])) {
			return $this->memoria['js_comprimido'];
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
		return toba_dir();
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