<?php
require_once("nucleo/browser/interface/form.php");
require_once('test_toba.php');
require_once('3ros/simpletest/reporter.php');
require_once('nucleo/browser/js.php');

$categorias = array('items');
$casos = array(
				array('categoria' => 'items', 'nombre' => 'Comportamiento básico del ítem', 'clase' => 'test_item'),
				array('categoria' => 'items', 'nombre' => 'Manejo del árbol de ítems', 'clase' => 'test_arbol_items'),
			);
			

if (! isset($_REQUEST['testear']))
{
    echo "<div style='text-align: center; font-size: 10px;'><br><br>";
    echo js::abrir();
    echo js::checks_desmarcar();
    echo js::checks_marcar();
    echo js::checks_intercalar();
    echo js::cerrar();
    echo form::abrir('testing_automatico', '');

    echo "<label for='todos'><input name='todos' type='checkbox' value='TODOS' onClick='return checks_intercalar(this)'>TODOS LOS TEST</label><br>";
	foreach ($categorias as $categoria)	{
		echo "<fieldset style='width: 300; text-align: left;'><legend>$categoria</legend>";
	    foreach ($casos as $caso) {
			if ($caso['categoria'] == $categoria) {
		        echo "<label for='clase_{$caso['clase']}'><input name='clase_{$caso['clase']}' id='clase_{$caso['clase']}' type='checkbox' value='1'>{$caso['nombre']}</label><br>\n";
			}
	    }
		echo "</fieldset><br>";
	}
	enter();
    echo form::submit('testear', 'EJECUTAR');
    echo form::cerrar();
    echo "</div>";
}
else
{
	$test = new GroupTest('Casos de TEST');
	foreach ($categorias as $categoria)	{
	    foreach ($casos as $caso) {
		    if (isset($_REQUEST["clase_{$caso['clase']}"]))
		    {
		        require_once($categoria."/".$caso['clase'].".php");
		        $test->addTestCase(new $caso['clase']($caso['nombre']));
		    }
	    }
	}
	$test->run(new HtmlReporter());
	echo "<br><br><div style='text-align: center'><a href='javascript: history.back()'>Volver</a></div>";
}


?>