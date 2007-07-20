<?php 
class form_basico extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
			//---- Procesamiento de EFs --------------------------------
		
			{$this->objeto_js}.evt__tabla__procesar = function(es_inicial)
			{
				if (! es_inicial) {
					this.submit();
				}
			}
		";
	}
}

?>