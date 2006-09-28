<?php 

class pantalla_previsualizacion extends toba_ei_pantalla 
{
	protected $codigo = '';
	
	function set_codigo($codigo)
	{
		$this->codigo = $codigo;
	}
	
	function generar_html_dependencias()
	{
		parent::generar_html_dependencias();
		if( $this->codigo ) {
			//ei_separador("ARCHIVO: ". $this->controlador->archivo_php()->nombre());
			echo "<div style='overflow: auto; height: 300px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			echo "<pre>";
			echo $this->codigo;
			echo "</pre>";
			echo "</div>";
		}
	}
}
?>