<?php

require_once('lib/admin_instancia.php');
require_once('lib/consultas_instancia.php');

class fuente extends toba_fuente_datos
{
	function get_db($reusar=true)
	{
		return admin_instancia::ref()->db();
	}
}
?>