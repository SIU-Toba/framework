<?php
require_once("nucleo/browser/clases/objeto_ci_me_tab.php");
require_once("nucleo/browser/clases/objeto_ei_formulario_ml.php");

//-------------------------------------------------------------------------------
//----  Controlador de INTERFACE
//-------------------------------------------------------------------------------

class objeto_ci_me_tab_p3 extends objeto_ci_me_tab
{
	function __construct($id)
	{
		parent::__construct($id);
	}

	function obtener_interface_2()
	{
		echo ei_mensaje("Interface 2 especifica");
		parent::interface_estandar();
	}
}
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
?>