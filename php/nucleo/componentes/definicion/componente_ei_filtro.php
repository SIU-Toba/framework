<?php
require_once("componente_ei_formulario.php");

/**
 * Un filtro presenta una grilla de campos similar al formulario, pero con el objetivo de reducir el conjunto de datos mostrados por otro objeto. 
 * @package Componentes
 * @subpackage Eis
 */
class componente_ei_filtro extends componente_ei_formulario
{
	static function get_tipo_abreviado()
	{
		return "Filtro";		
	}	
}
?>