<?php
require_once('nucleo/browser/clases/objeto_ei_filtro.php'); 
//--------------------------------------------------------------------
class filtro_opciones extends objeto_ei_filtro
{
	function extender_objeto_js()
	{
		echo " 
			{$this->objeto_js}.filtrar = function() {
				this.set_evento(new evento_ei('filtrar', true, '' ));		
			}
		
			{$this->objeto_js}.evt__proyecto__procesar = function(inicial) {
				if (!inicial) {
					if (this.ef('proyecto').valor() != apex_ef_no_seteado) {
						this.filtrar();
					}
				}
			}
			
			{$this->objeto_js}.evt__fuente__procesar = function(inicial) {
				if (!inicial) {
					if (this.ef('fuente').valor() != apex_ef_no_seteado) {
						this.filtrar();
					}					
				}
			}
		";
	}


}

?>