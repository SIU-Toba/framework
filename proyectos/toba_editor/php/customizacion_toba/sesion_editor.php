<?
require_once('modelo/info/contexto_info.php');

class sesion_editor extends toba_sesion
{
	function iniciar_contexto()
	{
		require_once('admin_util.php');		
		//*********  FRAMES entorno EDICION ************
		//-- FRAME control
		define("apex_frame_control","frame_control");
		//-- FRAME lista
		define("apex_frame_lista","frame_lista");
		//-- FRAME central
		define("apex_frame_centro","frame_centro");
		if (php_sapi_name() === 'cli') {
			toba_editor::iniciar(toba_instancia::get_id(), toba_editor::get_id());
		}		
	}

	protected function conf__actualizar_sesion()
	{
		contexto_info::set_proyecto( toba_editor::get_proyecto_cargado() );
		contexto_info::set_db( toba_editor::get_base_activa() );
	}
	
	protected function conf__inicio($usuario) {
		$this->conf__actualizar_sesion();
	}
}
?>