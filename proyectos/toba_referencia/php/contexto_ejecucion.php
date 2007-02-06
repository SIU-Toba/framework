<?php
require_once('nucleo/lib/toba_interface_contexto_ejecucion.php');

class contexto_ejecucion implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('php_referencia.php');
	}
	
	function conf__final(){}
}
?>