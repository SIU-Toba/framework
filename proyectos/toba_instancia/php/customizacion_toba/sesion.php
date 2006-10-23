<?php

class sesion extends toba_sesion
{
	function iniciar_contexto()
	{
		require_once('lib/admin_instancia.php');
		require_once('lib/consultas_instancia.php');
	}
	
	function get_id_instancia()
	{
		return 'desarrollo';	
	}
}
?>