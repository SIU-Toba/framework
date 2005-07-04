<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ci extends elemento_objeto
{
	function eventos_predefinidos()
	{
		return array('procesar', 'cancelar');	
	}


}


?>