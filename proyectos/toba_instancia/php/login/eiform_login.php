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
		
		{$this->objeto_js}.evt__autologin__procesar = function(es_inicial)
		{
			if( !es_inicial ) {
				var valor = this.ef('autologin').valor();
				if( valor != 'nopar' ) {
					this.controlador.submit();
				}
			}
		}
		";
	}
}
?>