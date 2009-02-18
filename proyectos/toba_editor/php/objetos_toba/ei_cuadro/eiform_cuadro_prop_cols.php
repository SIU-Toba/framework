<?php

class eiform_cuadro_prop_cols extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		//--- Se insertan los css de cada estilo para hacer la visualización
		$estilos = rs_convertir_asociativo(toba_info_editores::get_lista_estilos_columnas(), array('columna_estilo'), 'css');
		echo "var editor_col_css=".toba_js::arreglo($estilos, true);
		echo "
			{$this->objeto_js}.evt__usar_vinculo__procesar = function() {
				if (this.ef('usar_vinculo').valor() == '1') {
					this.ef('vinculo_carpeta').mostrar();
					this.ef('vinculo_item').mostrar();
					this.ef('vinculo_popup').mostrar();
					this.ef('vinculo_celda').mostrar();
					this.ef('vinculo_indice').mostrar();
					this.ef('accion_vin_servicio_extra').mostrar();
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
					this.ef('accion_vin_servicio_extra').ocultar();
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
			
			{$this->objeto_js}.evt__estilo__procesar = function(inicial) {
				var estado = this.ef('estilo').get_estado();	
				var input = this.ef('prueba_estilo').input();
				input.className = editor_col_css[estado] + ' columna-preview';
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