<?php
/**
 * @package Objetos
 * @subpackage Ei
 */
require_once("componente.php");

/**
 * Calendario para visualizar contenidos diarios y seleccionar días o semanas.
 * @package Objetos
 * @subpackage Ei
 */
class componente_cn extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_dependencias';
		$estructura[2]['registros'] = 'n';
		$estructura[2]['obligatorio'] = false;
		return $estructura;		
	}
		
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		$sql['info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_cn';
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/negocio';
	}

	static function get_tipo_abreviado()
	{
		return "CN";		
	}
}
?>