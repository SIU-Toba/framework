<?php

class eiform_eventos extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__accion__procesar = function() {
				if (this.ef('accion').valor() == 'H') {
					this.ef('accion_imphtml_debug').mostrar();
				} else {
					this.ef('accion_imphtml_debug').ocultar();
				}
				if (this.ef('accion').valor() == 'V') {
					this.ef('es_autovinculo').mostrar();
					this.ef('accion_vin_carpeta').mostrar();
					this.ef('accion_vin_item').mostrar();
					this.ef('accion_vin_popup').mostrar();
					this.ef('accion_vin_celda').mostrar();	
					this.ef('accion_vin_servicio_extra').mostrar();
					this.evt__accion_vin_servicio_extra__procesar();				
					if (this.ef('accion_vin_popup').chequeado()) {
						this.ef('accion_vin_popup_par').mostrar();
						this.ef('accion_vin_target').ocultar();
					} else {
						this.ef('accion_vin_popup_par').ocultar();
						this.ef('accion_vin_target').mostrar();
					}
				} else {
					this.ef('es_autovinculo').ocultar();
					this.ef('accion_vin_carpeta').ocultar();
					this.ef('accion_vin_item').ocultar();
					this.ef('accion_vin_target').ocultar();
					this.ef('accion_vin_celda').ocultar();
					this.ef('accion_vin_popup').ocultar();
					this.ef('accion_vin_popup_par').ocultar();
					this.ef('accion_vin_servicio_extra').ocultar();
					this.ef('accion_vin_servicio').ocultar();
				}
			}
			{$id_js}.evt__accion_vin_popup__procesar = function(es_inicial) {
				if (this.ef('accion').valor() == 'V') {
					if (this.ef('accion_vin_popup').chequeado()) {
						this.ef('accion_vin_popup_par').mostrar();
						this.ef('accion_vin_target').ocultar();
						if (! es_inicial) {
							this.ef('accion_vin_celda').set_estado('popup');
						}
					} else {
						this.ef('accion_vin_popup_par').ocultar();
						this.ef('accion_vin_target').mostrar();
						if (! es_inicial) {
							this.ef('accion_vin_celda').set_estado('');
						}
					}
				}
			}
			{$id_js}.evt__accion_vin_servicio_extra__procesar = function() {
				if (this.ef('accion_vin_servicio_extra').valor() == 'O') {					
						this.ef('accion_vin_servicio').mostrar();
				} else {
						this.ef('accion_vin_servicio').ocultar();
						this.ef('accion_vin_servicio').set_estado('');
				}				
			}
			{$id_js}.evt__es_autovinculo__procesar = function(es_inicial)
			{
				var se_muestra = (! this.ef('es_autovinculo').chequeado() && ! this.ef('es_autovinculo').es_oculto());
				this.ef('accion_vin_carpeta').mostrar(se_muestra, true);
				this.ef('accion_vin_item').mostrar(se_muestra, true);
			}
		";
	}
}

?>