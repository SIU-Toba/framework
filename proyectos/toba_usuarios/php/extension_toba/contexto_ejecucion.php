<?php

//use SIU\ManejadorSalidaBootstrap\bootstrap_factory;
//use SIU\ManejadorSalidaBootstrap\bootstrap_config;
class contexto_ejecucion implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('lib/admin_instancia.php');
		require_once('lib/consultas_instancia.php');
		/*$bootstrap_config = new bootstrap_factory();
		bootstrap_config::setLogoNombre(toba_recurso::imagen_proyecto('logo.gif', false));*/
	}
	
	function conf__final()
	{		
	}
}
?>