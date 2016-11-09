<?php

class form_consultas_php extends toba_ei_formulario
{
	
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "                        
			{$id_js}.evt__archivo__procesar = function(inicial) {
				if (!inicial) {
					var archivo = this.ef('archivo').valor();
					var basename = archivo.replace( /.*\//, '' );
					var clase = basename.substring(0, basename.lastIndexOf('.'));					
					this.ef('archivo_clase').cambiar_valor(clase);
					if (this.ef('clase').valor() == '') {
						this.ef('clase').cambiar_valor(clase);
					}
				}
			}

			{$id_js}.modificar_vinculo__ef_archivo = function(id_vinculo)
			{
				var estado = this.ef('punto_montaje').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
			}

			{$id_js}.evt__punto_montaje__procesar = function(inicial)
			{
				if (!inicial) {
					this.ef('archivo').cambiar_valor('');
					this.ef('archivo_clase').cambiar_valor('');
				}
			}			
		";
	}
	

}

?>