<?php

/**
 * Clase que mantiene informaci�n com�n a la actual instalaci�n de toba
 * Enmascara principalmente al archivo de configuraci�n instalacion.ini
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
	static function instancia($recargar=false)
	{
		if (!isset(self::$instancia) || $recargar) {
			self::$instancia = new toba_instalacion($recargar);	
		}
		return self::$instancia;	
	}

	static function eliminar_instancia()
	{
		self::$instancia = null;
	}

	private function __construct($recargar)
	{
		$this->memoria =& toba::manejador_sesiones()->segmento_info_instalacion();
		if(!$this->memoria || $recargar) {
			$this->memoria = parse_ini_file( toba::nucleo()->toba_instalacion_dir() . '/instalacion.ini');
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
	 * Retorna un n�mero que representa al grupo de trabajo y con el cual se indexaran los metadatos
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
	 * Retorna el comando que usa la instalaci�n para editar archivos php en forma interactiva
	 */
	function get_editor_php() 
	{
		if (isset($this->memoria['editor_php'])) {
			return $this->memoria['editor_php'];
		}
	}
	
	/**
	 * La instalaci�n trabaja con las librer�as js comprimidas?
	 */
	function es_js_comprimido()
	{
		if (isset($this->memoria['js_comprimido'])) {
			return $this->memoria['js_comprimido'];
		} else {
			return false;	
		}
	}
	
	function arreglo_png_ie()
	{
		if (isset($this->memoria['arreglo_png_ie'])) {
			return $this->memoria['arreglo_png_ie'];
		} else {
			return true;	
		}		
	}
	
	/**
	 * N�mero de versi�n de Toba
	 */
	function get_numero_version()
	{
		return file_get_contents(toba_dir()."/VERSION");
	}
	
	/**
	 * Retorna el path de la instalaci�n de toba
	 */
	function get_path()
	{
		return toba_nucleo::toba_dir();
	}	
	
	/**
	 * Retorna la URL base del runtime toba (donde esta el js, img y demas recursos globales a todos los proyectos)
	 * @return string
	 */			
	function get_url()
	{
		if (isset($this->memoria['url'])) {
			return $this->memoria['url'];
		}		
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