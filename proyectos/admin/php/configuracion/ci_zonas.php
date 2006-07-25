<?php 
require_once('ci_abm_basico.php');

class ci_zonas extends ci_abm_basico
{
	function get_datos_listado()
	{
		$sql = "SELECT proyecto, zona, nombre, descripcion
				FROM apex_item_zona
				WHERE proyecto = '" . editor::get_proyecto_cargado() . "'";
		return toba::get_db()->consultar($sql);
	}
}

?>