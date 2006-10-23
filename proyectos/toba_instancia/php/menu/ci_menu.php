<?php 
require_once('menu/arbol/menu_instancia.php');

class ci_menu extends toba_ci
{
	function ini()
	{
	}

	//---- arbol ------------------------------------------------------------------------

	//arreglo asociativo 'id_del_nodo' => 0|1 determinando si esta abierto o no
	function evt__arbol__cambio_apertura($apertura)
	{
	}

	function evt__arbol__ver_propiedades($nodo)
	{
	}

	//
	function conf__arbol($componente)
	{
		$instancia = new menu_instancia('desarrollo');
		$componente->set_datos( array($instancia) );
		$componente->set_nivel_apertura(2);
	}
}

?>