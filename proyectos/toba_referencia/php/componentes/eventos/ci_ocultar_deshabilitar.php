<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_ocultar_deshabilitar extends toba_ci
{
	function conf()
	{
		$this->dep('form1')->evento('b')->ocultar();	
		$this->dep('form1')->evento('d')->desactivar();	
		$this->pantalla()->evento('b')->ocultar();
		$this->pantalla()->evento('d')->desactivar();
	}
}

?>