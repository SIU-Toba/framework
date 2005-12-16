<?
/*
	Habre una consola del SIU-TOBA (Utiliza $toba_dir).
*/
//----------------------------------------------
// Determino el directorio de trabajo
//----------------------------------------------
if (isset($_SERVER['toba_dir'])) {
	$dir = $_SERVER['toba_dir']."/php"; 
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
	ini_set("include_path", ini_get("include_path"). $separador . $dir);
} else {
	echo "La variable de entorno 'toba_dir' no esta definida!";	
}
//----------------------------------------------
// Invoco la consola
//----------------------------------------------
require_once('nucleo/consola/consola_toba.php');
$consola = new consola_toba( $_SERVER['toba_dir'] );
array_shift($argv);
$consola->run( $argv );
?>