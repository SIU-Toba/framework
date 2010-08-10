<?php 
class eiform_login extends toba_testing_pers_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.loguearse = function() {
			var valor = this.ef('autologin').get_estado();
			var proyecto = this.ef('proyecto').get_estado();
			
			if( valor != apex_ef_no_seteado &&  valor != ''
				&& proyecto != apex_ef_no_seteado && proyecto != '') {
				this.controlador.submit();
			}
		}
				
		{$this->objeto_js}.evt__autologin__procesar = function(es_inicial)
		{
			if( !es_inicial ) {
				this.loguearse();
			}
		}

		{$this->objeto_js}.evt__proyecto__procesar = function(es_inicial)
		{
			if( !es_inicial && this.ef('autologin')) {
				this.loguearse();
			}
		}
		";
	}
}
?>