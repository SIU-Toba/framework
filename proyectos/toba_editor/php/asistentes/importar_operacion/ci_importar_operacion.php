<?php
require_once('asistentes/ci_asistente_base.php');

class ci_importar_operacion extends ci_asistente_base
{
	function evt__form_base__modificacion($datos)
	{
		$this->dep('datos')->tabla('base')->set($datos);
	}

	function conf__form_base()
	{
		$datos = $this->dep('datos')->tabla('base')->get();
		return $datos;
	}
}

?>