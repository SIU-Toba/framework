<?php
require_once('nucleo/persistencia/objeto_datos_tabla.php');

class test_datos_tabla_caso1 extends test_datos_tabla
{

	function crear_objeto()
	{
		$dbr = new objeto_datos_tabla(array('toba_testing','1424'));
		return $dbr;
	}
	
	function test_crear()
	{
		$dbr = $this->crear_objeto();
		$dbr->info();
	}
	
		
	
	
	
}
?>