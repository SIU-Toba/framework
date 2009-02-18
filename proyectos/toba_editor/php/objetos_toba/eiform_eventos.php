<?php

class eiform_eventos extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__accion__procesar = function() {
				if (this.ef('accion').valor() == 'V') {
					this.ef('accion_vin_carpeta').mostrar();
					this.ef('accion_vin_item').mostrar();
					this.ef('accion_vin_popup').mostrar();
					this.ef('accion_vin_celda').mostrar();
					this.ef('accion_vin_servicio_extra').mostrar();
					if (this.ef('accion_vin_popup').chequeado()) {
						this.ef('accion_vin_popup_par').mostrar();
						this.ef('accion_vin_target').ocultar();
					} else {
						this.ef('accion_vin_popup_par').ocultar();
						this.ef('accion_vin_target').mostrar();
					}
				} else {
					this.ef('accion_vin_carpeta').ocultar();
					this.ef('accion_vin_item').ocultar();
					this.ef('accion_vin_target').ocultar();
					this.ef('accion_vin_celda').ocultar();
					this.ef('accion_vin_popup').ocultar();
					this.ef('accion_vin_popup_par').ocultar();
					this.ef('accion_vin_servicio_extra').ocultar();
				}
			}
			{$this->objeto_js}.evt__accion_vin_popup__procesar = function() {
				if (this.ef('accion').valor() == 'V') {
					if (this.ef('accion_vin_popup').chequeado()) {
						this.ef('accion_vin_popup_par').mostrar();
						this.ef('accion_vin_target').ocultar();
					} else {
						this.ef('accion_vin_popup_par').ocultar();
						this.ef('accion_vin_target').mostrar();
					}
				}
			}
			{$this->objeto_js}.evt__accion_vin_servicio_extra__procesar = function() {
				if (this.ef('accion_vin_servicio_extra').valor() == 'O') {					
						this.ef('accion_vin_servicio').mostrar();
				} else {
						this.ef('accion_vin_servicio').ocultar();
						this.ef('accion_vin_servicio').set_estado('');
				}				
			}
		";
	}
}

?>