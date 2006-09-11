<?php 

class pantalla_codigo extends toba_ei_pantalla 
{
	function archivo_php()
	{
		return $this->controlador->archivo_php();	
	}
	
	//--- Archivo Plano	
	function generar_html_dependencias()
	{
		parent::generar_html_dependencias();
		echo "<br>";
		if($this->controlador->archivo_php()->existe()){
			ei_separador("ARCHIVO: ". $this->archivo_php()->nombre());
			echo "<div style='overflow: auto; height: 420px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			$this->archivo_php()->mostrar();
			echo "</div>";
		} else {
			echo ei_mensaje('El componente no posee una extension PHP');	
		}
	}	
}
?>