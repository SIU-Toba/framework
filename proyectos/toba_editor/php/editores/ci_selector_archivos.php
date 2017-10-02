<?php

class ci_selector_archivos extends toba_ci
{
	protected $archivo;
		
	function conf__listado()
	{
		$inicial = toba::memoria()->get_parametro('ef_popup_valor');
		if (!is_null($inicial)) {
			$id_pm  = toba::memoria()->get_parametro('punto_montaje');
			$punto  = toba_modelo_pms::get_pm($id_pm, toba_editor::get_proyecto_cargado());
			$absoluto = $punto->get_path_absoluto().'/';
			$this->dependencia('listado')->set_path_absoluto($absoluto);		
			$this->dependencia('listado')->set_path(dirname($inicial));
		}
	}
}
?>