<?php 
require_once(toba_dir() . '/php/contrib/lib/toba_nodo_basico.php');
require_once('configuracion/catalogo/catalogo_general.php');
require_once('configuracion/catalogo/catalogo_perfiles.php');

class ci_catalogo extends toba_ci
{
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
		$arbol = array( new catalogo_general(),
						new catalogo_perfiles );
		$componente->set_datos( $arbol );
		$componente->set_nivel_apertura(5);
	}
}

?>