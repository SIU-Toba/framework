<?php 

class pantalla_previsualizacion extends toba_ei_pantalla 
{
	function generar_layout()
	{
		parent::generar_layout();
		$php = $this->controlador->get_previsualizacion();
		if( $php ) {
			echo "<div style='overflow: auto; height: 350px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			echo $php;
			echo "</div>";
		}
	}
}
?>