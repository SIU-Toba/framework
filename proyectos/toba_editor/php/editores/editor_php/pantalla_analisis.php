<?php 

class pantalla_analisis extends toba_ei_pantalla 
{
	function archivo_php()
	{
		return $this->controlador->archivo_php();	
	}	

	function generar_html_dependencias()
	{
		parent::generar_html_dependencias();
		echo "<br>";	
		if($this->controlador->archivo_php()->existe()){
			ei_separador("ARCHIVO: ". $this->archivo_php()->nombre());
			$this->archivo_php()->incluir();
			$this->controlador->clase_php()->analizar();
		} else {
			echo ei_mensaje('El componente no posee una extension PHP');	
		}
	}	
}
?>