<?php
require_once('PHPWebDriver/__init__.php');
class toba_test_selenium extends toba_test
{	
	protected $usa_assert = true;												//Reemplazar en los hijos si no se va a hacer llamadas a metodos assertX
	
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
	
	/**
	 *  Define si el test esta haciendo uso de asserts de manerea interna o devuelve el valor al runner.
	 * @return boolean
	 */
	function get_assert_status()
	{
		return $this->usa_assert;
	}
}

?>