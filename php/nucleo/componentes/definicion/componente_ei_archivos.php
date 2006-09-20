<?php
/**
 * @package Componentes
 * @subpackage Eis
 */
require_once("componente_ei.php");

/**
 * Calendario para visualizar contenidos diarios y seleccionar días o semanas.
 * @package Componentes
 * @subpackage Eis
 */
class componente_ei_archivos extends componente_ei
{
	static function get_tipo_abreviado()
	{
		return "Archivos";		
	}

	static function get_nombre_clase_info()
	{
		return 'info_ei_archivos';
	}
}
?>