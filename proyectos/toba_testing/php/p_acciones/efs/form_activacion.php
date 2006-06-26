<?php 
//--------------------------------------------------------------------
class form_activacion extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.validar_ef = function() {
				return true;
			}
		";
	}
}

?>