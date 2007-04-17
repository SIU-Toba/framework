<?php 
require_once('ci_abm_basico.php');

class ci_mensajes extends ci_abm_basico
{
	function get_datos_listado()
	{
		return toba_info_editores::get_mensajes();
	}
}

?>