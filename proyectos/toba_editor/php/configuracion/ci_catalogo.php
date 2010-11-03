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

	function conf__arbol(toba_ei_arbol $componente)
	{
		$catalogo = new catalogo_general();
		$componente->set_datos($catalogo->get_hijos());
	}
}

?>