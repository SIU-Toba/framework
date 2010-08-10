<?php

class eiform_prop_base extends toba_ei_formulario
{
	
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__punto_montaje__procesar = function(inicial) {
				if (!inicial) {
					this.ef('subclase_archivo').cambiar_valor('');
					this.ef('subclase').cambiar_valor('');
				}
			}

			{$this->objeto_js}.evt__subclase_archivo__procesar = function(inicial) {
				var archivo = this.ef('subclase_archivo').valor();
				if (!inicial && this.ef('subclase').valor() == '') {
					var basename = archivo.replace( /.*\//, '' );
					var clase = basename.substring(0, basename.lastIndexOf('.'));
					this.ef('subclase').cambiar_valor(clase);
				}
			}
			
			{$this->objeto_js}.modificar_vinculo__ef_subclase_archivo = function(id_vinculo)
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