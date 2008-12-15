<?php 
class pant_vista_previa extends toba_ei_pantalla
{
	function generar_layout()
	{
		echo "<div class='editor-vista-previa'>".$this->controlador()->get_info_archivo();
		echo "<div>";
		$this->generar_botones_eventos(true);
		echo "</div></div>";
		$previa = $this->controlador()->get_previsualizacion();
		if(isset($previa)) {
			echo "<div style='overflow: auto; width: 580px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			echo $previa;
			echo "</div>";
		}
	}
}

?>