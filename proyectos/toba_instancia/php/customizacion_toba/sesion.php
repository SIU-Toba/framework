<?php
require_once('modelo/info/contexto_info.php');

class sesion extends toba_sesion
{
	function iniciar_contexto()
	{
		require_once('lib/admin_instancia.php');
		require_once('lib/consultas_instancia.php');
	}

	protected function conf__actualizar_sesion()
	{
		//contexto_info::set_proyecto( toba_editor::get_proyecto_cargado() );
		contexto_info::set_db( admin_instancia::ref()->db() );
	}
	
	function get_id_instancia()
	{
		return 'desarrollo';	
	}
}
?>