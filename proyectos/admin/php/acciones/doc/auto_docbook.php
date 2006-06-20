<?
//include_once("nucleo/consola/emular_web.php");
include_once('nucleo/lib/manual_docbook.php');

	$doc = new manual_docbook('toba');
	$doc->generar_xml();

?>