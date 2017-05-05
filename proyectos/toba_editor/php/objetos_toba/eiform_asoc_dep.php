<?php 
class eiform_asoc_dep extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js).
		".evt__id_dependencia__validar = function()
		{
			if (this.ef('id_dependencia').get_estado().indexOf(' ') != -1) {
					this.ef('id_dependencia').set_error('No puede contener espacios');
					return false;
			}
			return true;		
		}
		";
	}
}

?>