<?php 
//--------------------------------------------------------------------
class form_simple extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.modificar_vinculo__popup = function(id) {
			vinculador.agregar_parametros(id, {'clave': 'valor'});
		}
		
		
		";
	}


}

?>