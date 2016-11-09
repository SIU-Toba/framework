<?php

class eiform_prop_basicas extends toba_ei_formulario
{

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo admin_util::get_js_editor();
		echo "
			{$id_js}.evt__tipo_navegacion__procesar = function() {
				if (this.ef('tipo_navegacion').valor() != 'wizard') {
					this.ef('con_toc').ocultar();
				} else {
					this.ef('con_toc').mostrar();
				}
			}
			
			{$id_js}.evt__posicion_botonera__procesar = function() {
				valor = this.ef('posicion_botonera').get_estado();
				if (valor == 'arriba' || valor == 'ambos') {
					this.ef('botonera_barra_item').mostrar();
				} else {
					this.ef('botonera_barra_item').ocultar(true);
				}		
			}
			
			{$id_js}.evt__ancho__validar = function()
			{
				if (! toba_editor.medida_css_correcta(this.ef('ancho').get_estado())) {
						this.ef('ancho').set_error(toba_editor.mensaje_error_medida_css());
						return false;
				}
				return true;		
			}			
			
			{$id_js}.evt__alto__validar = function()
			{
				if (! toba_editor.medida_css_correcta(this.ef('alto').get_estado())) {
						this.ef('alto').set_error(toba_editor.mensaje_error_medida_css());
						return false;
				}
				return true;		
			}			
		";
	}


}

?>