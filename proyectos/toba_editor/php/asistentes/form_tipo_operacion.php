<?php 
class form_tipo_operacion extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js) 
		. ".evt__tipo__procesar = function(es_inicial)
		{
			var valor = this.ef('tipo').get_estado();
			var descripcion = '';
			if (isset(tipos_operacion[valor])) {
				descripcion = tipos_operacion[valor]['descripcion'];
				vista_previa = '<img src=\"' + tipos_operacion[valor]['vista_previa'] + '\" />';
			}
			$$('operacion_descripcion').innerHTML = descripcion;
			$$('operacion_vista_previa').innerHTML = vista_previa;
		}
		";
	}
}

?>