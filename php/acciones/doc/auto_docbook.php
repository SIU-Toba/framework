<?
//include_once("nucleo/consola/emular_web.php");
include_once('nucleo/lib/manual_docbook.php');

	$doc = new manual_docbook('comechingones','usuario');

	//$doc->preparar_arbol();
	echo $doc->obtener_xml();





?>