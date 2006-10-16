<?php
require_once('operaciones_simples/consultas.php');

class ci_principal extends toba_ci
{

	function conf__cuadro()
	{
		return consultas::get_personas();
	}

}

?>