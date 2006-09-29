<?php 

class pantalla_analisis extends toba_ei_pantalla 
{
	function generar_layout()
	{
		ei_separador($this->controlador->archivo_php()->nombre());
		$this->controlador->clase_php()->analizar();
	}	
}
?>