<?php

class contexto_ejecucion_curso implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('lib/soe_consultas.php');
	}

	function conf__final() {}

}
?>