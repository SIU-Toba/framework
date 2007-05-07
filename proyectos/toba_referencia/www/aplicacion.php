<?php
# apex_proyecto: Proyecto PRIMARIO
define("apex_pa_proyecto",'toba_referencia');
#---- NIVEL de ACCESO minimo permitido
//define("apex_pa_nivel_acceso_item","0");//Nivel de ITEMs que se pueden solicitar
//define("apex_pa_nivel_acceso_usuario","0");//Nivel de USUARIOS
define("apex_pa_validacion_debug",0);
define('apex_pa_metadatos_compilados',0);

//--------------------------------------------------------------------------
//------ Invocacion del nucleo del toba ------------------------------------
//--------------------------------------------------------------------------
if (isset($_SERVER['TOBA_DIR'])) {
	$dir = $_SERVER['TOBA_DIR']."/php"; 
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
	ini_set("include_path", ini_get("include_path"). $separador . $dir);
	define("apex_pa_ID",$_SERVER['PHP_SELF']);
	require_once("nucleo/toba_nucleo.php");
	toba_nucleo::instancia()->acceso_web();		
} else {
	die("Es necesario definir la variable 'TOBA_DIR' en el archivo de configuracion de apache
			(Utilize la directiva 'SetEnv')");
}
//--------------------------------------------------------------------------
?>