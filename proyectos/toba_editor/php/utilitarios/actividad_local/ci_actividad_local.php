<?php

class ci_actividad_local extends toba_ci
{
	function conf__cuadro()
	{
		return toba_info_editores::get_log_modificacion_componentes();
	}
}
?>