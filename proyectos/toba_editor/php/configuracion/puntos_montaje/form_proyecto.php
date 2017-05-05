<?php
class form_proyecto extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__proyecto_ref__procesar = function(es_inicial)
		{
//            var estado = this.ef('proyecto_ref').get_estado();
//            if (typeof estado != 'undefined' && estado != '') {
//                alert(estado);
//                this.ef('etiqueta').set_estado(estado);
//            }
		}
		";
	}

}
?>