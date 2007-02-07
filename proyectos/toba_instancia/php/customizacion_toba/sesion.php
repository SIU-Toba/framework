<?php
require_once('modelo/info/contexto_info.php');

class sesion extends toba_sesion
{
	protected function conf__actualizar_sesion()
	{
		//contexto_info::set_proyecto( toba_editor::get_proyecto_cargado() );
		contexto_info::set_db( admin_instancia::ref()->db() );
	}
	
	
	function get_id_instancia()
	{
		//Por ahora solo funciona sobre la instancia en la que esta corriendo
		return toba::instancia()->get_id();	
	}
}
?>