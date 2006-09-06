<?php 

class ci_ocultar_deshabilitar extends toba_ci
{
	function conf__pant_inicial()
	{
		$this->dep('form1')->evento('b')->ocultar();	
		$this->dep('form1')->evento('d')->deshabilitar();	
		$this->pantalla()->evento('b')->ocultar();
		$this->pantalla()->evento('d')->deshabilitar();
	}
}

?>