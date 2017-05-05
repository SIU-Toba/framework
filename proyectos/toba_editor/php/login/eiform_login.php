<?php 
class eiform_login extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$id_js}.loguearse = function() {
			var valor = this.ef('autologin').get_estado();
			var proyecto = this.ef('proyecto').get_estado();
			
			if( valor != apex_ef_no_seteado &&  valor != ''
				&& proyecto != apex_ef_no_seteado && proyecto != '') {
				this.controlador.submit();
			}
		}
				
		{$id_js}.evt__autologin__procesar = function(es_inicial)
		{
			if( !es_inicial ) {
				this.loguearse();
			}
		}

		{$id_js}.evt__proyecto__procesar = function(es_inicial)
		{
			if( !es_inicial && this.ef('autologin')) {
				this.loguearse();
			}
			var proyecto = this.ef('proyecto').get_estado();
			if( !es_inicial && !this.ef('autologin')) {
				if (this.ef('usuario').get_estado() != '' && this.ef('clave').get_estado() != ''
					&& proyecto != apex_ef_no_seteado && proyecto != '') {
					this.controlador.submit();				
				}
			}
			
		}
		";
	}
}
?>