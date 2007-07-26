<?php
/**
 * @ignore
 */
class toba_codigo_metodo_js extends toba_codigo_metodo
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