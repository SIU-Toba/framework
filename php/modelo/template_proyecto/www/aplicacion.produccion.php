<?php

# Proyecto
define('apex_pa_proyecto', '__proyecto__');

# Ejecuta con metadatos compilados
define('apex_pa_metadatos_compilados', 1);

# Deshabilita el autologin
define('apex_pa_validacion_debug', 0);

# Cambia el nivel del logger a modo 'info'. Para modo debug pasar a '7'
define('apex_pa_log_archivo_nivel', 6);

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