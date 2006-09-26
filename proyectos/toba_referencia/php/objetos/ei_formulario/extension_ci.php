<?php
require_once('nucleo/componentes/interface/toba_ci.php');

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
	
	function conf__1()
	{
		$this->dep('formulario')->cambiar_layout();	
	}

}


?>