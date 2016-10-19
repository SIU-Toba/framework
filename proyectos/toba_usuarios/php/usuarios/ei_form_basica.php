<?php 
class ei_form_basica extends toba_ei_formulario
{
	
	function extender_objeto_js()
	{
		echo  toba::escaper()->escapeJs($this->objeto_js).
		".evt__usuario__validar = function()
		{
			if (this.ef('usuario').get_estado().indexOf(' ') != -1) {
				this.ef('usuario').set_error('No puede contener espacios.');
				return false;
			}
			return true;		
		}
		";
	}
}

?>