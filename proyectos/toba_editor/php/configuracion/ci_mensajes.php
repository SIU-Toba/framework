<?php 
require_once('ci_abm_basico.php');

class ci_mensajes extends ci_abm_basico
{
	function get_datos_listado()
	{
		$sql = "SELECT proyecto, msg, indice, msg_tipo as tipo, descripcion_corta
				FROM 	apex_msg
				WHERE proyecto = '" . toba_editor::get_proyecto_cargado() . "';";
		return toba::get_db()->consultar($sql);
	}
}

?>