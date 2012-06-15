<?php
class form_molde extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------			  
	  
		{$this->objeto_js}.evt__punto_montaje__procesar = function(inicial) {
			if (!inicial) {
				this.ef('carpeta_archivos').cambiar_valor('');
			}
		}

		{$this->objeto_js}.modificar_vinculo__ef_carpeta_archivos = function(id_vinculo)
		{
			var estado = this.ef('punto_montaje').get_estado();
			vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
		}
            
		{$this->objeto_js}.modificar_vinculo__extender = function(id_vinculo)
		{
			var estado = this.ef('punto_montaje').get_estado();
			vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
		}
		";
	}

}

?>