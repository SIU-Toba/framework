<?php

require_once('3ros/simplesamlphp/lib/_autoload.php');

class contexto_ejecucion extends toba_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('php_referencia.php');
		//toba::menu()->set_abrir_nueva_ventana();
		toba::db()->set_parser_errores(new toba_parser_error_db_postgres7());
		toba::mensajes()->set_fuente_ini(toba::proyecto()->get_path().'/mensajes.ini');

		//Autenticacion personalizada
		/*$autentificacion = new toba_autenticacion_ldap('ldap-test.siu.edu.ar', "dc=ldap,dc=siu,dc=edu,dc=ar");
		toba::manejador_sesiones()->set_autenticacion($autentificacion);*/
	}
	
	function conf__final()
	{
		
	}
}
?>