<?php

class ci_selector_archivos extends toba_ci
{
	protected $archivo;
		
	function conf__listado()
	{
		$inicial = toba::memoria()->get_parametro('ef_popup_valor');
		$absoluto = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()).'/php/';
		$this->dependencia('listado')->set_path_absoluto($absoluto);
		if ($inicial != null) {
			$this->dependencia('listado')->set_path(dirname($inicial));
		}
	}

	
}
?>