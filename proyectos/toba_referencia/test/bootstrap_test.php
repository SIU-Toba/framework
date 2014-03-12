<?php

$_SERVER['TOBA_PROYECTO'] = 'toba_referencia';

if (! isset($_SERVER['TOBA_INSTANCIA'])) {
	//se podr�a dejar esto como default
	$_SERVER['TOBA_INSTANCIA'] = 'desarrollo';
}

if (! isset($_SERVER['TOBA_DIR'])) {
	//se podr�a dejar esto como default
	//los proyectos podr�an mayormente harcodear este par�metro asi
	$_SERVER['TOBA_DIR'] = realpath(__DIR__.'/../../../');
}


echo "\n";
echo "Iniciando contexto de ejecucion de toba\n";
echo "... \n";
echo "\n";


if (! isset($_SERVER['TOBA_DIR'])) {
    die("ATENCION: La variable de entorno 'TOBA_DIR' no esta definida!");
}

include($_SERVER['TOBA_DIR']."/php/consola/bootstrap_test.php");
