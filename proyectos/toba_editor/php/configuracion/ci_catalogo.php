<?php 
require_once('configuracion/catalogo/catalogo.php');

class ci_catalogo extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- arbol ------------------------------------------------------------------------

	function evt__arbol__cambio_apertura($apertura)
	{
	}

	function evt__arbol__ver_propiedades($nodo)
	{
	}

	function conf__arbol($componente)
	{
		$instancia = new catalogo();
		$componente->set_datos( array($instancia) );
		$componente->set_nivel_apertura(5);
	}
}

?>