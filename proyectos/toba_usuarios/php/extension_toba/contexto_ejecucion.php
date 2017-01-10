<?php
	
use SIU\ManejadorSalidaBootstrap\bootstrap_factory;
use SIU\ManejadorSalidaBootstrap\bootstrap_config;

class contexto_ejecucion implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('lib/admin_instancia.php');
		require_once('lib/consultas_instancia.php');
		
		$bootstrap_config = new bootstrap_factory();
		toba::output()->registrarServicio($bootstrap_config);
		toba::output()->setProvider('bootstrap');
		
		bootstrap_config::setMainColor( '#8B0C73');
		bootstrap_config::setLogoNombre(toba_recurso::imagen_proyecto('logo.gif', false));
	}
	
	function conf__final()
	{		
	}
}
?>