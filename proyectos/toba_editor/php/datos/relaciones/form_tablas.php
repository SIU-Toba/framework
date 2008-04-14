<?php 
class form_tablas extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__tabla_1__procesar = function(es_inicial)
		{
		}
		
		{$this->objeto_js}.evt__tabla_2__procesar = function(es_inicial)
		{
		}
		";
	}
}

?>