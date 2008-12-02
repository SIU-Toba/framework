<?php

class eiform_prop_base extends toba_ei_formulario
{
	
	function extender_objeto_js()
	{
		echo "						
			{$this->objeto_js}.evt__subclase_archivo__procesar = function(inicial) {
				var archivo = this.ef('subclase_archivo').valor();			
				if (!inicial && this.ef('subclase').valor() == '') {
					var basename = archivo.replace( /.*\//, '' );
					var clase = basename.substring(0, basename.lastIndexOf('.'));
					this.ef('subclase').cambiar_valor(clase);
				}
			}
			";
	}
	

}

?>