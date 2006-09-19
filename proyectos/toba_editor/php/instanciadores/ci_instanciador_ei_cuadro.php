<?php
require_once('ci_instanciadores.php'); 
//----------------------------------------------------------------
class ci_instanciador_ei_cuadro extends ci_instanciadores
{
	function conf__objeto()
	{
		//Se insertan un par de filas de prueba
		$columnas = $this->dependencia('objeto')->get_columnas();
		$estructura = $this->dependencia('objeto')->get_estructura_datos();
		$muestra = array();
		foreach ($columnas as $id => $columna) {
			$muestra[$id] = $id;
		}
		foreach ($estructura as $columna) {
			$muestra[$columna] = $columna;	
		}
		return array($muestra);
	}
	
	function evt__objeto__seleccion($claves)
	{
		$this->informar_msg("Selección:\n ".var_export($claves, true));
	}
	
}

?>