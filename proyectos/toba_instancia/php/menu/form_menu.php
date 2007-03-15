<?php 
class form_menu extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
		{$this->objeto_js}.evt__instancia__procesar = function(es_inicial)
		{
			if(!es_inicial) {
				this.submit();
			}
		}
		";
	}
}

?>