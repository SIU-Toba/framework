<?php

class contexto_ejecucion implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('php_referencia.php');
		toba::db()->set_parser_errores(new toba_parser_error_db_postgres7());
		toba::menu()->set_abrir_nueva_ventana();
	}
	
	function conf__final(){}
}
?>