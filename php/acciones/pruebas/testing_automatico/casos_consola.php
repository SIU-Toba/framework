<?php
include_once("nucleo/consola/emular_web.php");
require_once('test_toba.php');
require_once('3ros/simpletest/reporter.php');
require_once('casos_dao.php');

$this->registrar_parametros();

if(isset($this->parametros["-c"]) || isset($this->parametros["-t"])) {
	//Selecciono una categoria
	if (isset($this->parametros["-c"])) {
		$seleccionados = array_keys(casos_dao::get_casos($this->parametros["-c"]));
	}
	//Selecciono un test particular
	if (isset($this->parametros["-t"])) {
		$seleccionados = array($this->parametros["-t"]);
	}	
} else {
	//Si no selecciona nada, son todos los casos
	$seleccionados = array_keys(casos_dao::get_casos());
}


try {
	$test = new GroupTest('Casos de TEST');
    foreach (casos_dao::get_casos() as $clase =>$caso) {
	    if (in_array($clase, $seleccionados)) {
	        require_once($caso['categoria']."/".$clase.".php");
	        $test->addTestCase(new $clase($caso['nombre']));
	    }
	}
	
	//Termina la ejecución con 0 o 1 para que pueda comunicarse con al consola
	exit ($test->run(new TextReporter()) ? 0 : 1);
	
	
} catch (Exception $e) {
	die(ei_mensaje($e->getMessage(), "error"));
}


?>