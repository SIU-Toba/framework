<?
	include_once("nucleo/browser/interface/arbol_carpetas.php");

	$menu = new arbol_carpetas();
	ei_arbol($menu->obtener_combo(),"carpetas");
	
?>