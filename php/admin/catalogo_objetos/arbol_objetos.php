<?php
require_once('nucleo/browser/clases/objeto_ei_arbol.php'); 
//----------------------------------------------------------------
class arbol_objetos extends objeto_ei_arbol
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = "nombre_de_la_propiedad_a_persistir";
		return $propiedades;
	}

	function extender_objeto_js()
	{
	}


}

?>