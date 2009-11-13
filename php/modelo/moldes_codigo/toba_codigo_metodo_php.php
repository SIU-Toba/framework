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
		if ($this->tipo_funcion != '') {
			$this->tipo_funcion .= ' ' ;			//Le agrego un espacio para separacion
		}
		// Cabecera
		return "{$this->tipo_funcion}function $this->nombre($parametros)";
	}
}
?>