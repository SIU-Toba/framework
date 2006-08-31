<?php 
require_once('ci_abm_basico.php');

class ci_tipos_pagina extends ci_abm_basico
{
	function get_datos_listado()
	{
		$sql = "SELECT proyecto, pagina_tipo, descripcion
				FROM apex_pagina_tipo
				WHERE proyecto = '" . toba_editor::get_proyecto_cargado() . "'";
		return toba::db()->consultar($sql);
	}
}

?>