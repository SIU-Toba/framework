<?php
class form_torta_fuente extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$id_js}.evt__fuente__procesar = function(es_inicial)
		{
			if (!es_inicial) {
				{$id_js}.submit();
			}
		}
		";
	}


}
?>