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
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/runtime/negocio';
	}

	static function get_tipo_abreviado()
	{
		return "CN";		
	}
}
?>