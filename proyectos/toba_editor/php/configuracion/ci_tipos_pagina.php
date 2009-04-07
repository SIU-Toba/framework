<?php 
require_once('ci_abm_basico.php');

class ci_tipos_pagina extends ci_abm_basico
{
	function get_datos_listado()
	{
		return toba_info_editores::get_tipos_pagina_proyecto();
	}

	function conf__formulario(toba_ei_formulario $form)
	{
		parent::conf__formulario($form);
		$form->ef('clase_archivo')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
	}
}

?>