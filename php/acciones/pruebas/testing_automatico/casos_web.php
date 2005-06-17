<?php
require_once("nucleo/browser/interface/form.php");
require_once('test_toba.php');
require_once('3ros/simpletest/reporter.php');
require_once('nucleo/browser/js.php');
require_once('lista_casos.php');
/*
//Definición de los casos

$formulario = $this->cargar_objeto("objeto_ei_formulario", 0);
$this->objetos[$formulario]->inicializar( array('nombre_formulario' => 'testing_automatico'));	
$this->objetos[$formulario]->recuperar_interaccion();
$this->objetos[$formulario]->procesar_dependencias();

echo "<div style='text-align: center;'><br>";
echo form::abrir('testing_automatico', '', " onSubmit='return validar_form_testing_automatico(this)'");
$this->objetos[$formulario]->obtener_html();
echo js::abrir();
	echo "\nfunction validar_form_testing_automatico(formulario) { ";
	echo $this->objetos[$formulario]->obtener_javascript();
	echo "\nreturn true; }";
echo js::cerrar();
echo form::cerrar();
echo "</div>";

if ($this->objetos[$formulario]->controlar_agregar()) {
	try {
		if ($this->objetos[$formulario]->validar_estado()) {
			$datos = $this->objetos[$formulario]->obtener_datos();
			$test = new GroupTest('Casos de TEST');
		    foreach (casos_dao::get_casos() as $clase =>$caso) {
			    if (in_array($clase, $datos['casos']))
			    {
			        require_once($caso['categoria']."/".$clase.".php");
			        $test->addTestCase(new $clase($caso['nombre']));
			    }
			}
			$test->run(new HtmlReporter());
		} else {
			$this->objetos[$formulario]->mostrar_info_proceso();
		}
	} catch (Exception $e) {
		die(ei_mensaje($e->getMessage(), "error"));
	}
}
*/
	$test = new GroupTest('Casos de TEST');
	require_once("persistencia/test_db_tablas_cd.php");
	$test->addTestCase(new test_db_tablas_cd());
	$test->run(new HtmlReporter());

?>