<?php
require_once(toba_dir() . '/php/3ros/simpletest/test_case.php');

class toba_test_grupo_casos extends GroupTest
{
	protected $separar_casos=false;
	protected $separar_pruebas=false;
	
	function __construct($descripcion, $separar_casos=false, $separar_pruebas=false)
	{
		parent::GroupTest($descripcion);
		$this->separar_casos = $separar_casos;
		$this->separar_pruebas = $separar_pruebas;
	}
	
	function run(&$reporter)
	{
		$reporter->paintGroupStart($this->getLabel(), $this->getSize());
		for ($i = 0, $count = count($this->_test_cases); $i < $count; $i++) {
			if($this->separar_casos) {
				toba::logger()->debug(	"\n\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%% INICIO caso: ".$this->_test_cases[$i]->getLabel()." %%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n"
									, 'toba');
			}
			if (is_string($this->_test_cases[$i])) {
				$class = $this->_test_cases[$i];
				$test = new $class();
				if($this->separar_pruebas) {
					$test->separar_pruebas(true);
				}
				$test->run($reporter);
			} else {
				if($this->separar_pruebas) {
					$this->_test_cases[$i]->separar_pruebas(true);
				}
				$this->_test_cases[$i]->run($reporter);
			}
			if($this->separar_casos) {
				toba::logger()->debug(	"\n\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%% FIN caso: ".$this->_test_cases[$i]->getLabel()." %%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n"
									, 'toba');
			}
		}
		$reporter->paintGroupEnd($this->getLabel());
		return $reporter->getStatus();
	}	
}
?>