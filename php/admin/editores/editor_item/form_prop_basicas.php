<?php
require_once('nucleo/browser/clases/objeto_ei_formulario.php'); 
//----------------------------------------------------------------
class form_prop_basicas extends objeto_ei_formulario
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__comportamiento__procesar = function() {
				this.ef('accion').ocultar();
				this.ef('patron').ocultar();
				this.ef('buffer').ocultar();								
				this.ef(this.ef('comportamiento').valor()).mostrar();
			}
			
			{$this->objeto_js}.evt__menu__procesar = function() {
				if (this.ef('menu').chequeado())
					this.ef('orden').mostrar();
				else
					this.ef('orden').ocultar();				
			}
		";
	}


}

?>