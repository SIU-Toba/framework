<?php
require_once('nucleo/componentes/interface/toba_ei_formulario.php'); 
//----------------------------------------------------------------
class form_prop_basicas extends toba_ei_formulario
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__menu__procesar = function() {
				if (this.ef('menu').chequeado())
					this.ef('orden').mostrar();
				else
					this.ef('orden').ocultar();
			}
			
			{$this->objeto_js}.evt__zona__procesar = function() {
				if (this.ef('zona').valor() != apex_ef_no_seteado) {
					this.ef('zona_listar').mostrar();
				} else {
					this.ef('zona_listar').ocultar();
				}
				this.evt__zona_listar__procesar();
			}
			
			{$this->objeto_js}.evt__zona_listar__procesar = function() {
				if (this.ef('zona_listar').chequeado()) {
					this.ef('zona_orden').mostrar();
				} else {
					this.ef('zona_orden').ocultar();				
				}
			}
		";
	}

}

?>