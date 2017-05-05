<?php 
class form_opciones extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$id_js}.evt__origen__procesar = function(es_inicial)
		{
			var mostrar = (this.ef('origen').get_estado() == 'mensaje_manual');
			this.ef('texto').mostrar(mostrar);
		}
		
		{$id_js}.evt__destino__procesar = function(es_inicial)
		{
			var mostrar = (this.ef('destino').get_estado() == 'descripcion');
			this.ef('componente').mostrar(mostrar);		
		}
		";
	}
}

?>