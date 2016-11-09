<?php

class form_prop_basicas extends toba_ei_formulario
{

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo admin_util::get_js_editor();			
		echo "
			{$id_js}.evt__filas_ordenar__procesar = function () {
				var mostrar = this.ef('filas_ordenar').chequeado();
				this.ef('columna_orden').mostrar(mostrar);
				this.ef('filas_ordenar_en_linea').mostrar(mostrar);
			}
			
			{$id_js}.evt__filas_agregar__procesar = function() {
				var mostrar = this.ef('filas_agregar').chequeado();
				this.ef('filas_agregar_online').mostrar(mostrar);
				this.ef('filas_agregar_abajo').mostrar(mostrar);
				this.ef('filas_agregar_texto').mostrar(mostrar);
				this.ef('filas_borrar_en_linea').mostrar(mostrar);
			}

			{$id_js}.evt__scroll__procesar = function() {
				if (this.ef('scroll').chequeado())
					this.ef('alto').mostrar();
				else
					this.ef('alto').ocultar();				
			}
			
			//---- Validacion de EFs -----------------------------------
			
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
