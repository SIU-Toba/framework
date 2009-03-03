<?php
class filtro_opciones extends toba_ei_formulario
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
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__ip__procesar = function(inicial)
		{
			if (!inicial) {
				if (trim(this.ef('ip').valor()) != '') {
					this.filtrar();
				}					
			}
		}
		
		{$this->objeto_js}.evt__usuario__procesar = function(inicial)
		{
			if (!inicial) {
				if (trim(this.ef('usuario').valor()) != '') {
					this.filtrar();
				}					
			}
		}
		";
	}



}
?>