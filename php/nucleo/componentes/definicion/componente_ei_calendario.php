<?php
/**
 * @package Componentes
 * @subpackage Eis
 */
/**
 * Calendario para visualizar contenidos diarios y seleccionar días o semanas.
 * @package Componentes
 * @subpackage Eis
 */
class componente_ei_calendario extends componente_ei
{
	static function get_tipo_abreviado()
	{
		return "Calendario";		
	}

	static function get_nombre_clase_info()
	{
		return 'toba_info_ei_calendario';
	}
}
?>