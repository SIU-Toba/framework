<?php

class contexto_ejecucion implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('php_referencia.php');
		toba::menu()->set_abrir_nueva_ventana();
	}
	
	function conf__final(){}
}
?>