<?php
/**
 * @ignore
 */
class toba_codigo_metodo_php extends toba_codigo_metodo
{
	function get_declaracion()
	{
		// Parametros
		$parametros = '';
		foreach($this->parametros as $id => $param){
			$this->parametros[$id] = $param;
		}
		$parametros = implode(', ',$this->parametros);
		// Cabecera
		return "function $this->nombre($parametros)";
	}
}
?>