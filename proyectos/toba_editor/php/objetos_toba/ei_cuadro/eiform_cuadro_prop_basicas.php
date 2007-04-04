<?php

class eiform_cuadro_prop_basicas extends toba_ei_formulario
{
	function extender_objeto_js() 
	{
		//$ef = $this->obtener_nombres_ef();
		echo "
		{$this->objeto_js}.evt__clave_dbr__procesar = function (inicial) {
			if( this.ef('clave_dbr').chequeado() ){
				this.ef('columnas_clave').ocultar();
			}else{
				this.ef('columnas_clave').mostrar();
			}
		}

		{$this->objeto_js}.evt__scroll__procesar = function (inicial) {
			if( this.ef('scroll').chequeado() ){
				this.ef('scroll_alto').mostrar();
			}else{
				this.ef('scroll_alto').ocultar();
			}
		}
		
		{$this->objeto_js}.evt__paginar__procesar = function (inicial) {
			if( this.ef('paginar').chequeado() ){
				this.ef('tamano_pagina').mostrar();
				this.ef('tipo_paginado').mostrar();
			}else{
				this.ef('tamano_pagina').ocultar();
				this.ef('tipo_paginado').ocultar();
			}
		}
		
		{$this->objeto_js}.evt__eof_invisible__procesar = function() {
			if (this.ef('eof_invisible').chequeado()) {
				this.ef('eof_customizado').ocultar();
			} else {
				this.ef('eof_customizado').mostrar();
			}
		}
		";
	}
}
?>


