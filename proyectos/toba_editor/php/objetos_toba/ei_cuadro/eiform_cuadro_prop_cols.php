<?php

class eiform_cuadro_prop_cols extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__usar_vinculo__procesar = function() {
				if (this.ef('usar_vinculo').valor() == '1') {
					this.ef('vinculo_carpeta').mostrar();
					this.ef('vinculo_item').mostrar();
					this.ef('vinculo_popup').mostrar();
					this.ef('vinculo_celda').mostrar();
					this.ef('vinculo_indice').mostrar();
					if (this.ef('vinculo_popup').chequeado()) {
						this.ef('vinculo_popup_par').mostrar();
						this.ef('vinculo_target').ocultar();
					} else {
						this.ef('vinculo_popup_par').ocultar();
						this.ef('vinculo_target').mostrar();
					}
				} else {
					this.ef('vinculo_carpeta').ocultar();
					this.ef('vinculo_item').ocultar();
					this.ef('vinculo_target').ocultar();
					this.ef('vinculo_celda').ocultar();
					this.ef('vinculo_popup').ocultar();
					this.ef('vinculo_indice').ocultar();
					this.ef('vinculo_popup_par').ocultar();
				}
			}
			{$this->objeto_js}.evt__vinculo_popup__procesar = function() {
				if (this.ef('usar_vinculo').valor() == '1') {
					if (this.ef('vinculo_popup').chequeado()) {
						this.ef('vinculo_popup_par').mostrar();
						this.ef('vinculo_target').ocultar();
					} else {
						this.ef('vinculo_popup_par').ocultar();
						this.ef('vinculo_target').mostrar();
					}
				}
			}
		";
	}
}

?>