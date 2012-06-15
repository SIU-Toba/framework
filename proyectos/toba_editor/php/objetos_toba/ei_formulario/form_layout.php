<?php
class form_layout extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
			//---- Procesamiento de EFs --------------------------------
			{$this->objeto_js}.evt__tipo_layout__procesar = function(es_inicial)
			{
				if (this.ef('tipo_layout').tiene_estado()) {
					this.ef('template').mostrar();
					if (! es_inicial && trim(this.ef('template').get_estado()) == '') {
						this.ef('template').get_editor().execCommand('templates');
					}
				} else {
					this.ef('template').ocultar();
				}
			}
		";
			
		//Saco la siguiente configuracion para el ef_html, de manera que se elimine la identacion y todo caracter de separacion al enviarse al servidor
		echo "
			CKEDITOR.on( 'instanceReady', function( ev )
			{
				ev.editor.dataProcessor.writer.setRules( 'p',
						{
						   indent : false,
						    breakBeforeOpen : false,
						    breakAfterOpen : false,
						    breakBeforeClose : false,
						    breakAfterClose : false
						} );
			});

		";
	}

}

?>