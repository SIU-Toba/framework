<?php
require_once('toba_molde_metodo.php');

/**
 * @ignore
 */
class toba_molde_metodo_js extends toba_molde_metodo
{
	function get_declaracion()
	{
		// Parametros
		$parametros = implode(', ',$this->parametros);
		// Cabecera
		return "{\$this->objeto_js}.$this->nombre = function($parametros)";
	}
}
?>