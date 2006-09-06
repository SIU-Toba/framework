<?php 

class ci_ocultar_deshabilitar extends toba_ci
{
	function conf()
	{
		$this->dep('form1')->evento('b')->ocultar();	
		$this->dep('form1')->evento('d')->deshabilitar();	
		$this->pantalla()->evento('b')->ocultar();
		$this->pantalla()->evento('d')->deshabilitar();
	}
}

?>