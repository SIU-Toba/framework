<?php 
require_once('ci_abm_basico.php');

class ci_zonas extends ci_abm_basico
{
	function get_datos_listado()
	{
		$sql = 'SELECT proyecto, zona, nombre
				FROM apex_item_zona
				WHERE proyecto = ' .quote( toba_editor::get_proyecto_cargado());
		return toba::db()->consultar($sql);
	}

	function conf__formulario(toba_ei_formulario $form)
	{
		parent::conf__formulario($form);
		$form->ef('archivo')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
		$form->ef('consulta_archivo')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
	}
}

?>