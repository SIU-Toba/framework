<?php 
//--------------------------------------------------------------------
class ci_validacion_js extends objeto_ci
{
	protected $datos;
	
	function evt__form__modificar($datos)
	{
		$this->datos = $datos;
	}

	function evt__form__carga()
	{
		return $this->datos;
	}


}

?>