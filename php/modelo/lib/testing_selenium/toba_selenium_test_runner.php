<?php
require_once(toba_dir() . '/php/3ros/simpletest/runner.php');

class toba_selenium_test_runner extends SimpleRunner
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
				try {
					$rsp = $invoker->invoke($method);
					$asrt = $invoker->getTestCase()->get_assert_status();							//Recupero si el caso, usa Assert o no internamente.
					if (!is_null($rsp) && is_array($rsp)  && !$asrt) {								//En base a ello, uso o no los datos devueltos.
						$result = (isset($rsp['resultado'])) ? $rsp['resultado'] : false;
						$msg = $this->parsear_extra_resultado($rsp);
						$invoker->getTestCase()->assertTrue($result, $msg);					
					}
				} catch (PHPWebDriver_WebDriverCurlException $e) {
					toba::logger()->debug($e->getMessage());
					throw new toba_error_usuario('Inicie el servidor Selenium por favor');
				}
				if($this->separar_pruebas) {
					toba::logger()->debug("\n\n%%%%%%%%%%%--------- FIN prueba: $method ---------%%%%%%%%%%%%%%%%%%%", 'toba');
				}
			}
		}
	}
	
	/**
	 * Parsea lo que devuelvan en la componente 'extra' de un arreglo y en base a eso devolvera un msg que podra ser texto, link u otra cosa.
	 * @param array $datos
	 * @return text
	 */
	protected function parsear_extra_resultado($datos)
	{
		$msg = '';
		if (isset($datos['extra'])) {
			$msg = (is_array($datos['extra']))? var_export($datos['extra'], true) : $datos['extra'];			
		}
		return $msg;
	}
}
?>