<?php 
require_once('modelo/consultas/dao_permisos.php');

class ci_previsualizacion extends toba_ci
{
	private $modificacion = false;

	function extender_objeto_js()
	{
		if ($this->modificacion) {
			echo "top.frame_control.document.location.reload();";	
		}
	}
	
	function get_grupos_acceso()
	{
		return dao_permisos::get_grupos_acceso();
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__datos__modificacion($datos)
	{
		toba_editor::set_parametros_previsualizacion($datos);
		$this->modificacion = true;
	}

	function conf__datos()
	{
		return toba_editor::get_parametros_previsualizacion();
	}
}
?>