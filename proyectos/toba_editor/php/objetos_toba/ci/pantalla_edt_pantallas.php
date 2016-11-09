<?php

class pantalla_edt_pantallas extends toba_ei_pantalla 
{
	function generar_layout()
	{
		$this->dependencia('pantallas_lista')->generar_html();
		if ($this->controlador->hay_pant_sel()) {
			$datos = $this->controlador->get_datos_pantalla_actual();
			$nombre_pantalla = toba::escaper()->escapeHtml("Propiedades de la pantalla '{$datos['etiqueta']}' [{$datos['identificador']}]");
			echo "<fieldset style='margin-top: 10px; padding: 7px;'><legend>$nombre_pantalla</legend>";			
			$this->dependencia('pantallas')->generar_html();			
			echo "<table class='tabla-0'  width='100%'>";
			echo "<tr>\n";
			if ($this->controlador->existen_deps()) {
				echo "<td width='50%' style='vertical-align: top'>\n";
				echo "<fieldset style='margin: 5px;padding: 5px; FONT-SIZE: 10px; COLOR: #990000;'><legend>Dependencias Asociadas</legend>";			
				$this->dependencia('pantallas_ei')->generar_html();			
				echo '</fieldset>';
				echo "</td>\n";
			}
			if ($this->controlador->existen_evts()) {
				echo "<td style='vertical-align: top'>\n";
				echo "<fieldset style='margin: 5px; padding: 5px; FONT-SIZE: 10px; COLOR: #990000;'><legend>Eventos Asociados</legend>";			
				$this->dependencia('pantallas_evt')->generar_html();
				echo '</fieldset>';
				echo "</td>\n";
			}
			echo "</tr>\n";
			echo '</table>';
			$this->generar_boton('aceptar_pantalla');
			$this->generar_boton('cancelar_pantalla');
			echo '</fieldset>';
		}
	}
}
?>