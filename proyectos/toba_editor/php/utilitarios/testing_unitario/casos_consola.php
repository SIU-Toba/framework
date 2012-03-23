<?php
require_once('lista_casos.php');
include_once('nucleo/consola/emular_web.php');
require_once(toba_dir() . '/php/3ros/simpletest/reporter.php');

$this->registrar_parametros();

$proyecto = $this->parametros['-p'];
lista_casos::$proyecto = $proyecto;

//Selecciono una categoria
if (isset($this->parametros['-c'])) {
	$seleccionados = lista_casos::get_casos($this->parametros['-c']);
} else {
	$seleccionados = lista_casos::get_casos();
}
	
if (isset($this->parametros['-t'])) {
	//Seleccion de un test particular
	if (isset($this->parametros['-t'])) {
		$particular = false;
		foreach ($seleccionados as $caso) {
			if ($caso['id'] == $this->parametros['-t']) {
				$particular = $caso;
			}
		}
		if ($particular) {
			$seleccionados = array($particular);
		} else {
			$seleccionados = array();
		}
	}	
} 

try {
	$test = new GroupTest('Casos de TEST');
	foreach ($seleccionados as $caso) {
		require_once($caso['archivo']);
		$test->addTestCase(new $caso['id']($caso['nombre']));
	}
	
	//Termina la ejecución con 0 o 1 para que pueda comunicarse con al consola
	exit ($test->run(new TextReporter()) ? 0 : 1);
	
} catch (Exception $e) {
	if (method_exists($e, 'mensaje_consola')) {
		echo $e->mensaje_consola();
	} else {
		echo $e;
	}
}


?>