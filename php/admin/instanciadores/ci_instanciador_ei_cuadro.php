<?php
require_once('ci_instanciadores.php'); 
//----------------------------------------------------------------
class ci_instanciador_ei_cuadro extends ci_instanciadores
{
	function evt__objeto__carga()
	{
		//Se insertan un par de filas de prueba
		$columnas = $this->dependencias['objeto']->get_columnas();
		$estructura = $this->dependencias['objeto']->get_estructura_datos();
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
	
	function obtener_html_contenido__simulacion()
	{
		if (isset($this->datos)) {
			ei_arbol($this->datos, "Datos actuales");	
		}
		$this->obtener_html_dependencias();
	}
	

}

?>