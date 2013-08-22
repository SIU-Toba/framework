<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php');

class ci_principal extends toba_ci
{

	function conf__cuadro($cuadro)
	{
		$deporte = toba::memoria()->get_parametro('deportes');
		$cuadro->desactivar_modo_clave_segura();
		if (isset($deporte)) {
			return consultas::get_personas_con_deporte($deporte);
		} else {
			return consultas::get_personas();
		}
	}

}
?>