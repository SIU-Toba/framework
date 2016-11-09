<?php

class eiform_cuadro_prop_basicas extends toba_ei_formulario
{
	function extender_objeto_js() 
	{
		echo admin_util::get_js_editor();			
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		//$ef = $this->obtener_nombres_ef();
		echo "
		{$id_js}.evt__clave_dbr__procesar = function (inicial) {
			if( this.ef('clave_dbr').chequeado() ){
				this.ef('columnas_clave').ocultar(true);
			}else{
				this.ef('columnas_clave').mostrar();
			}
		}

		{$id_js}.evt__scroll__procesar = function (inicial) {
			if( this.ef('scroll').chequeado() ){
				this.ef('scroll_alto').mostrar();
			}else{
				this.ef('scroll_alto').ocultar();
			}
		}
		
		{$id_js}.evt__paginar__procesar = function (inicial) {
			if( this.ef('paginar').chequeado() ){
				this.ef('tamano_pagina').mostrar();
				this.ef('tipo_paginado').mostrar();
				this.ef('exportar_paginado').mostrar();
			}else{
				this.ef('tamano_pagina').ocultar();
				this.ef('tipo_paginado').ocultar();
				this.ef('exportar_paginado').ocultar();
			}
		}
		
		{$id_js}.evt__eof_invisible__procesar = function() {
			if (this.ef('eof_invisible').chequeado()) {
				this.ef('eof_customizado').ocultar();
			} else {
				this.ef('eof_customizado').mostrar();
			}
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
		
		{$id_js}.evt__scroll_alto__validar = function()
		{
			if (! toba_editor.medida_css_correcta(this.ef('scroll_alto').get_estado())) {
					this.ef('scroll_alto').set_error(toba_editor.mensaje_error_medida_css());
					return false;
			}
			return true;			
		}			
		";
	}
}
?>


