<?
require_once('modelo/info/contexto_info.php');

class sesion_editor extends toba_sesion
{

	protected function conf__actualizar_sesion()
	{
		contexto_info::set_proyecto( editor::get_proyecto_cargado() );
		contexto_info::set_db( editor::get_base_activa() );
	
	}
	
	protected function conf__inicio($usuario) {
		$this->conf__actualizar_sesion();
	}
}
?>