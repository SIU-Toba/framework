<?php
class form_proyecto extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js) .
		".evt__proyecto__procesar = function(es_inicial)
		{
			if (! es_inicial) {
				this.set_evento( new evento_ei('filtrar', true, '' ));
			}
		}
		";
	}

}

?>