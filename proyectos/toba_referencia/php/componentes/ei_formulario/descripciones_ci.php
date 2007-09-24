<?php 
class descripciones_ci extends toba_ci
{
	
	function get_personas()
	{
		require_once('operaciones_simples/consultas.php');
		return consultas::get_personas();	
	}
}

?>