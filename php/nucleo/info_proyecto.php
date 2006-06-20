<?php

class info_proyecto
{
	static private $instancia;
	const prefijo_punto_acceso = 'pa_';

	static function get_id()
	{
		if(! defined('apex_pa_proyecto') ){
			throw new excepcion_toba("Es necesario definir la constante 'apex_pa_proyecto'");
		}
		return apex_pa_proyecto;
	}
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new info_proyecto();	
		}
		return self::$instancia;	
	}

	private function __construct()
	{
		if ( !isset($_SESSION['toba']['proyectos'][self::get_id()])) {
			$_SESSION['toba']['proyectos'][self::get_id()] = datos_proyecto::cargar_info_basica();
		}
	}

	function get_parametro($id)
	{
		if( defined( self::prefijo_punto_acceso . $id ) ){
			return constant(self::prefijo_punto_acceso . $id);
		} elseif (isset($_SESSION['toba']['proyectos'][self::get_id()][$id])) {
			return $_SESSION['toba']['proyectos'][self::get_id()][$id];
		} else {
			if( array_key_exists($id,$_SESSION['toba']['proyectos'][self::get_id()])) {
				return null;
			}else{
				throw new excepcion_toba("INFO_PROYECTO: El parametro '$id' no se encuentra definido.");
			}
		}	
	}

	function set_parametro($id, $valor)
	{
		$_SESSION['toba']['proyectos'][self::get_id()][$id] = $valor;
	}

}
?>