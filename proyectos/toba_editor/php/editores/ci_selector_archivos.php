<?php

class ci_selector_archivos extends toba_ci
{
	protected $archivo;
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		return $propiedades;
	}
	
	function conf__listado()
	{
		$inicial = toba::memoria()->get_parametro('ef_popup_valor');
		$relativo = toba_instancia::get_path_proyecto(toba_editor::get_proyecto_cargado())."/php/";
		$this->dependencia('listado')->set_path_relativo_inicial($relativo);
		if ($inicial != null) {
			$this->dependencia('listado')->set_path(dirname($inicial));
		}
	}

	
}
?>