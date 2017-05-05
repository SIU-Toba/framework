<?php 
class eiform_ini_sesion extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__proyecto__procesar = function(es_inicial)
		{
			if(!es_inicial) {
				this.submit();
			}
		}
		";
	}
}

?>