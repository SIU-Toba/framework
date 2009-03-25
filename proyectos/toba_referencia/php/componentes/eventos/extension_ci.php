<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{

	function conf__eventos_a()
	{
		return $this->get_info_localidades();
	}

	private function get_info_localidades()
	{
		require_once('componentes/datos_ejemplos.php');
		return datos_ejemplos::get_localidades();
	}

	function evt__sobrefiltrar()
	{
		$this->dep('eventos_a')->evento('eliminar')->desactivar();	
	}
}
?>