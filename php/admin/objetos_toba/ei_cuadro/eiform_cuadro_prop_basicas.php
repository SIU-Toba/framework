<?
require_once("nucleo/browser/clases/objeto_ei_formulario.php");

class eiform_cuadro_prop_basicas extends objeto_ei_formulario
{
	function extender_objeto_js() 
	{
		//$ef = $this->obtener_nombres_ef();
		echo "
		{$this->objeto_js}.evt__clave_dbr__procesar = function (inicial) {
			var flag;
			flag = this.ef('clave_dbr').valor();
			if( flag == 1 ){
				this.ef('columnas_clave').ocultar();
			}else{
				this.ef('columnas_clave').mostrar();
			}
		}

		{$this->objeto_js}.evt__scroll__procesar = function (inicial) {
			var flag;
			flag = this.ef('scroll').valor();
			if( flag == 1 ){
				this.ef('scroll_alto').mostrar();
			}else{
				this.ef('scroll_alto').ocultar();
			}
		}
		
		{$this->objeto_js}.evt__paginar__procesar = function (inicial) {
			var flag;
			flag = this.ef('paginar').valor();
			if( flag == 1 ){
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
/*
		{$this->objeto_js}.evt__cc_modo__procesar = function (inicial) {
			var flag;
			flag = this.ef('cc_modo').valor();
			if( flag == 'a' ){
				this.ef('cc_modo_a_colap').mostrar();
				this.ef('cc_modo_a_totcol').mostrar();
				this.ef('cc_modo_a_totcua').mostrar();
			}else{
				this.ef('cc_modo_a_colap').ocultar();
				this.ef('cc_modo_a_totcol').ocultar();
				this.ef('cc_modo_a_totcua').ocultar();
			}
		}*/
		";
	}
}
?>


