<?php

class sesion_referencia extends toba_sesion 
{
	function iniciar_contexto()
	{
		require_once('php_referencia.php');
	}	
}

?>