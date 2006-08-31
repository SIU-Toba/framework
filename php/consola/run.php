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

	array_shift( $argv );	
	$consola = new consola( $directorio_comandos, $clase_menu );
	$consola->run( $argv );
	$dir_logs = instalacion::dir_base()."/logs_consola";
	toba_logger::instancia()->set_directorio_logs($dir_logs);
	toba_logger::instancia()->guardar_en_archivo('comandos.log');

} else {
	echo "  ATENCION: La variable de entorno 'toba_dir' no esta definida!";	
}
?>