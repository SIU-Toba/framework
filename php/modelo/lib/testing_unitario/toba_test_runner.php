<?php
require_once(toba_dir() . '/php/3ros/simpletest/runner.php');

class toba_test_runner extends SimpleRunner
{
	protected $separar_pruebas = false;

	function separar_pruebas($estado=false)
	{
		$this->separar_pruebas = $estado;
	}
	
	function run()
	{
		$methods = get_class_methods(get_class($this->_test_case));
		$invoker = &$this->_test_case->createInvoker();
		foreach ($methods as $method) {
			if (! $this->_isTest($method)) {
				continue;
			}
			if ($this->_isConstructor($method)) {
				continue;
			}
			if ($this->_scorer->shouldInvoke($this->_test_case->getLabel(), $method)) {
				if($this->separar_pruebas) {
					toba::logger()->debug("\n\n%%%%%%%%%%%--------- INICIO prueba: $method ---------%%%%%%%%%%%%%%%%%%%\n", 'toba');
				}
				$invoker->invoke($method);
				if($this->separar_pruebas) {
					toba::logger()->debug("\n\n%%%%%%%%%%%--------- FIN prueba: $method ---------%%%%%%%%%%%%%%%%%%%", 'toba');
				}
			}
		}
	}

}
?>