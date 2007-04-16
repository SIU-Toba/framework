<?php

class ci_actividad_local extends toba_ci
{
	function conf__cuadro()
	{
		return dao_editores::get_log_modificacion_componentes();
	}
}
?>