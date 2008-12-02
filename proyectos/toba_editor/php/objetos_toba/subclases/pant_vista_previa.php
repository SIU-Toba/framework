<?php 
class pant_vista_previa extends toba_ei_pantalla
{
	function generar_layout()
	{
		ei_separador($this->controlador->controlador()->get_path_archivo());		
		$previa = $this->controlador()->get_previsualizacion();
		if(isset($previa)) {
			echo "<div style='overflow: auto; width: 580px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			echo $previa;
			echo "</div>";
		}
	}
}

?>