<?php

class fuente extends toba_fuente_datos
{
	function get_db()
	{
		return toba::instancia()->get_db();
		return admin_instancia::ref()->db();
	}
}
?>