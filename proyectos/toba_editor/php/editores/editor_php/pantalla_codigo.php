<?php 

class pantalla_codigo extends toba_ei_pantalla 
{
	function generar_layout()
	{
		ei_separador($this->controlador->archivo_php()->nombre());
		echo "<div style='overflow: auto; height: 420px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
		$this->controlador->archivo_php()->mostrar();
		echo "</div>";
	}
}
?>