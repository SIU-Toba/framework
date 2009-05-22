<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
	function conf__formulario()
	{
		return array(
			'id' => '12',
			'descripcion' => 'Esta es la descripción.',
			'comentarios' => 'Este es un comentario.'
		);
	}
	
	
	//-- PANTALLAS
	
	function conf__columnas()
	{
		$this->dep('formulario')->cambiar_layout();	
	}

	
	function conf__basico()
	{
		$this->dep('formulario')->set_template(null);
	}

}


?>