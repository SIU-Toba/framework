<?php

class pant_eventos extends toba_ei_pantalla 
{
	
	function generar_layout()
	{
		parent::generar_layout();		
		if (! $this->controlador->mostrar_evento_detalle()) {
			$internos = $this->controlador()->controlador()->get_eventos_internos();
			if (! empty($internos)) {
				$this->generar_html_eventos_internos($internos);
			}
		}
	}
	
	function generar_html_eventos_internos($internos)
	{
		echo '<div>';
		echo '<h3>Otros eventos que serán disparados:</h3>';		
		echo '<ul>';
		foreach ($internos as $id => $ayuda) {
			echo "<li><em>$id</em>: $ayuda</li>";
		}
		echo '</ul>';
		echo '</div>';
	}
	
	
}



?>