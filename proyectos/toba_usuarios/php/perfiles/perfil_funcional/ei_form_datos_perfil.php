<?php 
class ei_form_datos_perfil extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js) .
		".evt__usuario_grupo_acc__validar = function()
		{
			if (this.ef('usuario_grupo_acc').get_estado().indexOf(' ') != -1) {
				this.ef('usuario_grupo_acc').set_error('No puede contener espacios.');
				return false;
			}
			return true;		
		}
		";
	}
}

?>