<?php

class ci_seleccionar_carpeta extends toba_ci
{
	protected $archivo;
	protected $s__dir_absoluto; 
	
	function conf__listado(toba_ei_archivos $ei)
	{
		$ei->set_solo_carpetas(true);
		$inicial = toba::memoria()->get_parametro('ef_popup_valor');		
		
		if (! isset($this->s__dir_absoluto)) {											//Si no hay valor previo de la instancia esta
			$id_pm  = toba::memoria()->get_parametro('punto_montaje');
			if (! is_null($id_pm)) {												//Si existe PM cargo ese dir, sino el defecto del proyecto
				$punto  = toba_modelo_pms::get_pm($id_pm, toba_editor::get_proyecto_cargado());
				$this->s__dir_absoluto = $punto->get_path_absoluto().'/';
			} else {
				$this->s__dir_absoluto = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()).'/php/';			
			}
		}
		$ei->set_path_absoluto($this->s__dir_absoluto);
		if ($inicial != null) {
			$ei->set_path(dirname($inicial));
		}
	}


	
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__seleccionar = function() {
				var listado = this.dep('listado');
				var path = listado._path_relativo;
				seleccionar(path, path);	//Comunicacion con la ventana padre
				return false;
			}
		";
	}
	
}
?>