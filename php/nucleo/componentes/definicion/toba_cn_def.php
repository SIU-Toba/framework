<?php
/**
 * @package Componentes
 * @subpackage Eis
 */

/**
 * Calendario para visualizar contenidos diarios y seleccionar d�as o semanas.
 * @package Componentes
 * @subpackage Eis
 */
class toba_cn_def extends toba_componente_def
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
		$sql['_info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);
		return $sql;
	}
	
	static function get_tipo_abreviado()
	{
		return "CN";		
	}
}
?>