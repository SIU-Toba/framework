<?php 
class form_tipo_operacion extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__tipo__procesar = function(es_inicial)
		{
			var valor = this.ef('tipo').get_estado();
			var descripcion = '';
			if (isset(tipos_operacion[valor])) {
				descripcion = tipos_operacion[valor]['descripcion'];
			}
			$('operacion_descripcion').innerHTML = descripcion;
		}
		";
	}
}

?>