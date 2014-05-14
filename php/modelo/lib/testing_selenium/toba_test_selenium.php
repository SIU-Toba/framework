<?php
require_once('PHPWebDriver/__init__.php');
class toba_test_selenium extends toba_test
{	
	/*
	*	Crea un runner customizado por toba (agrega separaciones en el LOG por test)
	*/
	function &_createRunner(&$reporter) 
	{
		$runner = new toba_selenium_test_runner($this, $reporter);
		if($this->separar_pruebas) {
			$runner->separar_pruebas(true);
		}
		return $runner;
	}	
}

?>