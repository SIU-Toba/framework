<?php

class form_consultas_php extends toba_ei_formulario
{
	
	function extender_objeto_js()
	{
		echo "						
			{$this->objeto_js}.evt__archivo__procesar = function(inicial) {
				if (!inicial && this.ef('clase').valor() == '') {
					var archivo = this.ef('archivo').valor();
					var basename = archivo.replace( /.*\//, '' );
					var clase = basename.substring(0, basename.lastIndexOf('.'));
					this.ef('clase').cambiar_valor(clase);
				}
			}
			";
	}
	

}

?>