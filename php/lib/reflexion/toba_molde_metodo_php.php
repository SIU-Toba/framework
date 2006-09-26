<?php
require_once('toba_molde_metodo.php');

class toba_molde_metodo_php extends toba_molde_metodo
{
	function get_declaracion()
	{
		// Parametros
		$parametros = '';
		foreach($this->parametros as $id => $param){
			$this->parametros[$id] = '$' . $param;
		}
		$parametros = implode(', ',$this->parametros);
		// Cabecera
		return "function $this->nombre($parametros)";
	}
}
?>