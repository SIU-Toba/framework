<?php
require_once('nucleo/persistencia/objeto_datos_tabla.php');
require_once('nucleo/browser/clases/objeto_ci.php');

class test_datos_tabla extends test_toba
{

	function crear_objeto()
	{
		$dbr = new objeto_datos_tabla(array('toba_testing','1424'));	
	}
	
	function test_crear()
	{
		$dbr = $this->crear_objeto();
		$dbr->info();
	}
	
		
	
	
	
}
?>