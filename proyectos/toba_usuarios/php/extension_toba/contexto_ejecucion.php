<?php

class contexto_ejecucion implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('lib/admin_instancia.php');
		require_once('lib/consultas_instancia.php');
	}
	
	function conf__final()
	{		
	}
}
?>