<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php');

class ci_principal extends toba_ci
{

	function conf__cuadro()
	{
		$deporte = toba::memoria()->get_parametro('deportes');
		if (isset($deporte)) {
			return consultas::get_personas_con_deporte($deporte);
		}
	}

}

?>