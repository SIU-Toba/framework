<?php

class ci_principal extends toba_testing_pers_ci
{
	function extender_objeto_js()
	{
	}


	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------
	function conf__cuadro($cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$datos = array(
			array('clave' => 1, 'valor' => 'Uno'),
			array('clave' => 2, 'valor' => 'Dos'),
			array('clave' => 3, 'valor' => 'Tres')
		);
		$cuadro->set_datos($datos);
	}

}

?>