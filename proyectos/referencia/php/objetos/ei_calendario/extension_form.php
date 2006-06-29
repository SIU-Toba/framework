<?php
  
require_once('nucleo/componentes/interface/objeto_ei_formulario.php');

class extension_form extends objeto_ei_formulario
{
	function get_lista_eventos()
	{
		//Se agrega el evento de modificación a la botonera
		$eventos = parent::get_lista_eventos();
		$eventos["modificacion"]["etiqueta"] = 'Grabar';
		$eventos["modificacion"]["en_botonera"] = true;
		$this->set_evento_defecto(null);
		return $eventos;
	}

}
  
?>