<?php
require_once('nucleo/componentes/interface/objeto_ei_formulario.php'); 
//----------------------------------------------------------------
class eiform_prop_basicas extends objeto_ei_formulario
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__tipo_navegacion__procesar = function() {
				if (this.ef('tipo_navegacion').valor() != 'wizard') {
					this.ef('con_toc').ocultar();
				} else {
					this.ef('con_toc').mostrar();
				}
		
			}
		";
	}


}

?>