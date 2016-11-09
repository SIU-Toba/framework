<?php

class filtro_catalogo_items extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__con_objeto__procesar = function() {
				if (this.ef('con_objeto').chequeado()) {
					this.ef('objeto_clase').mostrar();
					this.ef('objeto').mostrar();
				} else {
					this.ef('objeto_clase').ocultar();
					this.ef('objeto').ocultar();				
				}
			}
		";
	}
}
?>