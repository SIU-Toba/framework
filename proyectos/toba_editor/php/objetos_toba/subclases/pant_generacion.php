<?php 
class pant_generacion extends toba_ei_pantalla
{
	function generar_layout()
	{
		$this->dep('form_opciones')->generar_html();
		echo '<br>';
		$this->dep('form_metodos')->generar_html();
		echo '<br>';
		$previa = $this->controlador()->get_previsualizacion();
		if(isset($previa)) {
			echo "<div style='overflow: auto; height: 350px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			echo $previa;
			echo "</div>";
		}
	}
}

?>