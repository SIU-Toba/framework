<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_ei_arbol extends toba_ci
{
	function conf__arbol(toba_ei_arbol $arbol)
	{
		//-- Se obtienen los nodos que formarán parte del arbol
		require_once('contrib/catalogo_items_menu/toba_catalogo_items_menu.php');
		$catalogo = new toba_catalogo_items_menu();
		$raiz = '1000206';		
		$catalogo->cargar(array(), $raiz);
		$nodos = $catalogo->get_hijos($raiz);
		
		
		//-- Se configura el arbol
		$arbol->set_mostrar_filtro_rapido(true);
		$arbol->set_mostrar_ayuda(false);		
		$arbol->set_nivel_apertura(0);
		$arbol->set_datos($nodos);
	}
}

?>