<?php
require_once('nucleo/componentes/interface/objeto_ci.php');

class extension_ci extends objeto_ci
{
	protected $tope = 10;
	protected $s__tope;
	
	function conf__formulario()
	{
		return array(
			'id' => '12',
			'descripcion' => 'Esta descripción se especifica en la carga',
			'comentarios' => 'Este es un comentario'
		);
	}
	
	function evt__pre_cargar_datos_dependencias()
	{
		echo "HEY";
		$this->dependencia('formulario')->set_grupo_eventos_activo('A');
	}
	
	function evt__formulario__mi_accion($datos)
	{
		if ($datos['importe'] > $this->tope )
			throw new toba_excepcion('El importe no puede se mayor que '.$this->tope);
		ei_arbol($datos, 'Resultado de mi acción');
	}

	

}


?>