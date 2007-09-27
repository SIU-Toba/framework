<?php 
class form_propbasicas extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo admin_util::get_js_editor();		
		echo "
		//---- Validacion de EFs -----------------------------------
		
		{$this->objeto_js}.evt__ancho__validar = function()
		{
            if (! toba_editor.medida_css_correcta(this.ef('ancho').get_estado())) {
                    this.ef('ancho').set_error(toba_editor.mensaje_error_medida_css());
                    return false;
            }
            return true;			
		}
		
		{$this->objeto_js}.evt__ancho_etiqueta__validar = function()
		{
            if (! toba_editor.medida_css_correcta(this.ef('ancho_etiqueta').get_estado())) {
                    this.ef('ancho_etiqueta').set_error(toba_editor.mensaje_error_medida_css());
                    return false;
            }
            return true;		
		}
		";
	}
}

?>