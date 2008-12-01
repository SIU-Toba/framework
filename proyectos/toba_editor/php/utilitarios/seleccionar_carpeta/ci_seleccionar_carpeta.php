<?php

class ci_seleccionar_carpeta extends toba_ci
{
	protected $archivo;
	
	function conf__listado(toba_ei_archivos $ei)
	{
		$ei->set_solo_carpetas(true);
		$inicial = toba::memoria()->get_parametro('ef_popup_valor');
		$absoluto = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado())."/php/";
		$ei->set_path_absoluto($absoluto);
		if ($inicial != null) {
			$ei->set_path(dirname($inicial));
		}
	}


	
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__seleccionar = function() {
				var listado = this.dep('listado');
				var path = listado._path_relativo;
				seleccionar(path, path);	//Comunicacion con la ventana padre
				return false;
			}
		";
	}
	
}
?>