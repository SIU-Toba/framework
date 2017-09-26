<?php 

class ci_previsualizacion extends toba_ci
{
	private $modificacion = false;

	/*function extender_objeto_js()
	{
		if ($this->modificacion) {
			echo "top.frame_control.document.location.reload();";	
		}
	}*/
	
	function get_grupos_acceso()
	{
		return toba_info_permisos::get_perfiles_funcionales();
	}

	function get_perfiles_datos()
	{
		return toba_info_permisos::get_perfiles_datos();
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
		$parametros = toba_editor::get_parametros_previsualizacion();
		if (! isset($parametros)) {
			$parametros['punto_acceso'] = toba::instancia()->get_url_proyecto(toba_editor::get_proyecto_cargado());
		}
		return $parametros;
	}
}
?>