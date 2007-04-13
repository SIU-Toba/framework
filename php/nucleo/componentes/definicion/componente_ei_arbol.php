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
class componente_ei_arbol extends componente_ei
{
	static function get_tipo_abreviado()
	{
		return "Árbol";		
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_ei_arbol';
	}
}
?>