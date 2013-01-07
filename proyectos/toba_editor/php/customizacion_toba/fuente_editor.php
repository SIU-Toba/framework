<?php

class fuente_editor extends toba_fuente_datos
{
	function get_db($reusar = true)
	{
		return toba_editor::get_base_activa();
	}
}
?>