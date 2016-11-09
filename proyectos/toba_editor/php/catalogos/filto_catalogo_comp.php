<?php 
class filto_catalogo_comp extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__extendidos__procesar = function(es_inicial)
		{
			if (this.ef('extendidos').get_estado() == 'SI') {
				this.ef('subclase').mostrar();
			} else {
				this.ef('subclase').ocultar();
			}
		}
		";
	}
}

?>