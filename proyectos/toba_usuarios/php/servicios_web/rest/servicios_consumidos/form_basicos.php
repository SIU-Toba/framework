<?php
class form_basicos extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__tipo_auth__procesar = function(es_inicial)
		{
			var seleccionado = this.ef('tipo_auth').get_estado();
			if (seleccionado != '0') {
				this.ef('cert_file').mostrar(false);
				this.ef('key_file').mostrar(false);
				this.ef('cert_pwd').mostrar(false),
				this.ef('cert_CA').mostrar(false);				
				this.ef('usr').mostrar(true);
				this.ef('usr_pwd').mostrar(true);
			} else {
				this.ef('cert_file').mostrar(true);
				this.ef('key_file').mostrar(true);
				this.ef('cert_pwd').mostrar(true),
				this.ef('cert_CA').mostrar(true);				
				this.ef('usr').mostrar(false);
				this.ef('usr_pwd').mostrar(false);			
			}
		}
		";
	}

}

?>