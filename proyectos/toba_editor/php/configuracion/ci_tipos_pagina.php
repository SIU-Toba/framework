<?php 
require_once('ci_abm_basico.php');

class ci_tipos_pagina extends ci_abm_basico
{
	function get_datos_listado()
	{
		return toba_info_editores::get_tipos_pagina_proyecto();
	}
}

?>