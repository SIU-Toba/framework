<?php 

class pantalla_analisis extends toba_ei_pantalla 
{
	function generar_html_dependencias()
	{
		$this->controlador->archivo_php()->incluir();
		$this->controlador->clase_php()->analizar();
	}	
}
?>