<?php 

class pantalla_analisis extends toba_ei_pantalla 
{
	function generar_layout()
	{
		$this->controlador->clase_php()->analizar();
	}	
}
?>