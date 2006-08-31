<?php 
require_once('ci_abm_basico.php');

class ci_zonas extends ci_abm_basico
{
	function get_datos_listado()
	{
		$sql = "SELECT proyecto, zona, nombre
				FROM apex_item_zona
				WHERE proyecto = '" . toba_editor::get_proyecto_cargado() . "'";
		return toba::db()->consultar($sql);
	}
}

?>