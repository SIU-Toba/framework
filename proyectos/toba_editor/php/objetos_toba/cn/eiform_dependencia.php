<?php 
class eiform_dependencia extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__identificador__validar = function()
		{
			if (this.ef('identificador').get_estado().indexOf(' ') != -1) {
					this.ef('identificador').set_error('No puede contener espacios');
					return false;
			}
			return true;		
		}
		";
	}
}

?>