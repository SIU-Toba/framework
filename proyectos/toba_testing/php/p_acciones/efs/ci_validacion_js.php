<?php 
//--------------------------------------------------------------------
class ci_validacion_js extends toba_testing_pers_ci
{
	protected $datos;
	
	function evt__form__modificar($datos)
	{
		$this->datos = $datos;
	}

	function conf__form()
	{
		return $this->datos;
	}


}

?>