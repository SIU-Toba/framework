<?php
class form_torta_fuente extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__fuente__procesar = function(es_inicial)
		{
			if (!es_inicial) {
				{$this->objeto_js}.submit();
			}
		}
		";
	}


}
?>