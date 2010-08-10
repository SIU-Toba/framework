<?php 
//--------------------------------------------------------------------
class form_simple extends toba_testing_pers_ei_formulario
{
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.modificar_vinculo__ef_popup = function(id) {
			vinculador.agregar_parametros(id, {'clave': 'valor'});
		}
		
		
		";
	}


}

?>