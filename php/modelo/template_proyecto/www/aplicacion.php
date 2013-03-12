<?php

# Proyecto
define('apex_pa_proyecto', '__proyecto__');

# Ejecuta con metadatos compilados
#define('apex_pa_metadatos_compilados', 1);

# Deshabilita el autologin
#define("apex_pa_validacion_debug", 0);

# Activa el logger en modo 'debug'. Para modo info pasar a '6'
define('apex_pa_log_archivo', true);
define('apex_pa_log_archivo_nivel', 7);

//--------------------------------------------------------------------------
//------ Invocacion del nucleo del toba ------------------------------------
//--------------------------------------------------------------------------
if (isset($_SERVER['TOBA_DIR'])) {
	$dir = $_SERVER['TOBA_DIR'].'/php'; 
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ';.;' : ':.:';
	ini_set('include_path', ini_get('include_path'). $separador . $dir);
	require_once('nucleo/toba_nucleo.php');
	toba_nucleo::instancia()->acceso_web();	
} else {
	die("Es necesario definir la variable 'TOBA_DIR' en el archivo de configuracion de apache
			(Utilize la directiva 'SetEnv')");
}
//--------------------------------------------------------------------------
?>