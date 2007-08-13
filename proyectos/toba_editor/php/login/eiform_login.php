<?php 
class eiform_login extends toba_ei_formulario
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
			if( valor != 'nopar' &&  proyecto != 'nopar' && proyecto != '') {
				this.controlador.submit();
			}
		}
				
		{$this->objeto_js}.evt__autologin__procesar = function(es_inicial)
		{
			if( !es_inicial ) {
				this.loguearse();
			}
		}
		";
	}
}
?>