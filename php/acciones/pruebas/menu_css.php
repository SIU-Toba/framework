<?

include_once("nucleo/browser/interface/menu_css.php");


	$menu = new menu_css();
	$menu->preparar_arbol();
	$menu->obtener_html();

?>