<?php

class eiform_controles extends toba_ei_formulario_ml
{
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__archivo__procesar = function(inicial) {
			if (!inicial && this.ef('clase').valor() == '') {
				var archivo = this.ef('archivo').valor();
				var basename = archivo.replace( /.*\//, '' );
				var clase = basename.substring(0, basename.lastIndexOf('.'));
				this.ef('clase').cambiar_valor(clase);
			}
		}";
	}   
}
?>