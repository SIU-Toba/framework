<?php
class form_institucion extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__nombre_completo__procesar = function(es_inicial)
		{
			var nombre = this.ef('nombre_completo').get_estado();
			var abrev = nombre.substring(0,20);
			this.ef('nombre_abreviado').set_estado(abrev);
		}
		
		//---- Validacion de EFs -----------------------------------
		
		{$this->objeto_js}.evt__sigla__validar = function()
		{
			var sigla = this.ef('sigla').get_estado();
			if ( sigla.length < 3 ) {
				this.ef('sigla').set_error('La sigla debe tener al menos 3 caracteres');
				return false;
			}
			return true;
		}
		";
	}

}

?>