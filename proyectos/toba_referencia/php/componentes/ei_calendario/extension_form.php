<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_form extends toba_ei_formulario
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