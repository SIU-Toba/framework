<?php
class form_tipos_pagina extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		{$id_js}.modificar_vinculo__ef_clase_archivo = function(id_vinculo)
		{
			var estado = this.ef('punto_montaje').get_estado();
			vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
		}

		{$id_js}.evt__punto_montaje__procesar = function(inicial) {
			  if (!inicial) {
				  this.ef('clase_nombre').cambiar_valor('');
				  this.ef('clase_archivo').cambiar_valor('');
			  }
		  }
		";
	}

}
?>