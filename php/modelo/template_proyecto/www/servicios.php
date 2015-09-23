<?php
# apex_proyecto: Proyecto PRIMARIO
define('apex_pa_proyecto', '__proyecto__');

//--------------------------------------------------------------------------
//------ Carga autoload composer -------------------------------------------
//--------------------------------------------------------------------------
$path = realpath(__DIR__ . '/../vendor/autoload.php');
if (file_exists($path)) {
	require_once($path);
}

//--------------------------------------------------------------------------
//------ Invocacion del nucleo del toba ------------------------------------
//--------------------------------------------------------------------------
if (isset($_SERVER['TOBA_DIR'])) {
	$dir = $_SERVER['TOBA_DIR'].'/php'; 
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ';.;' : ':.:';
	ini_set('include_path', ini_get('include_path'). $separador . $dir);
	require_once('nucleo/toba_nucleo.php');
	toba_nucleo::instancia()->acceso_servicio();		
} else {
	die("Es necesario definir la variable 'TOBA_DIR' en el archivo de configuracion de apache
			(Utilize la directiva 'SetEnv')");
}
//--------------------------------------------------------------------------
?>