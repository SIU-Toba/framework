<?php

class toba_test_grupo_casos extends TestSuite
{
	protected $separar_casos=false;
	protected $separar_pruebas=false;
	//private $test_cases;
	
	function __construct($descripcion, $separar_casos=false, $separar_pruebas=false)
	{
		parent::__construct($descripcion);
	/*	$this->separar_casos = $separar_casos;
		$this->separar_pruebas = $separar_pruebas;*/
	}
	/*
	protected function getTestCases()
	{
		return $this->test_cases;
	}
	
	function run($reporter)
	{
		$reporter->paintGroupStart($this->getLabel(), $this->getSize());
		$casos = $this->getTestCases();		
		$cant = count($casos);
		var_dump($casos);
		for ($i = 0; $i < $cant; $i++) {
			if($this->separar_casos) {
				toba::logger()->debug(	"\n\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%% INICIO caso: ".$casos[$i]->getLabel()." %%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n"
									, 'toba');
			}
			if (is_string($casos[$i])) {
				$class = $casos[$i];
				$test = new $class();
				if($this->separar_pruebas) {
					$test->separar_pruebas(true);
				}
				$test->run($reporter);
			} else {
				if($this->separar_pruebas) {
					$casos[$i]->separar_pruebas(true);
				}
				$casos[$i]->run($reporter);
			}
			if($this->separar_casos) {
				toba::logger()->debug(	"\n\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%% FIN caso: ".$casos[$i]->getLabel()." %%%%%%%%%%%%%%%%%%%".
										"\n%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%\n"
									, 'toba');
			}
		}
		$reporter->paintGroupEnd($this->getLabel());
		return $reporter->getStatus();
	}*/	
}
?>