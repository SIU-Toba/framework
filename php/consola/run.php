<?
/*
	Este script abre una consola del SIU-TOBA 
*/
if ( isset( $_SERVER['toba_dir'] ) ) {

	//----------------------------------------------
	// Defino el 'include_path'
	//----------------------------------------------

	$dir = $_SERVER['toba_dir']."/php"; 
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ';.;' : ':.:';
	ini_set('include_path', ini_get('include_path'). $separador . $dir);

	//----------------------------------------------
	// Invoco la consola
	//----------------------------------------------
	
	require_once('consola/consola.php');
	$directorio_comandos = $dir . "/consola/comandos";
	$clase_menu = 'menu_toba';
	$consola = new consola( $directorio_comandos, $clase_menu );
	array_shift( $argv );
	$consola->run( $argv );

} else {
	echo "  ATENCION: La variable de entorno 'toba_dir' no esta definida!";	
}
?>