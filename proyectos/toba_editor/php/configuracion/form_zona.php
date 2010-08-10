<?php
class form_zona extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.modificar_vinculo__ef_archivo = function(id_vinculo)
			{
				var estado = this.ef('punto_montaje').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
			}

			{$this->objeto_js}.evt__punto_montaje__procesar = function(inicial)
			{
				if (!inicial) {
					this.ef('archivo').cambiar_valor('');
				}
			}

			{$this->objeto_js}.modificar_vinculo__ef_consulta_archivo = function(id_vinculo)
			{
				var estado = this.ef('consulta_pm').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
			}

			{$this->objeto_js}.evt__consulta_pm__procesar = function(inicial)
			{
				if (!inicial) {
					this.ef('consulta_archivo').cambiar_valor('');
					this.ef('consulta_clase').cambiar_valor('');
					this.ef('consulta_metodo').cambiar_valor('');
				}
			}
		";
	}
}
?>