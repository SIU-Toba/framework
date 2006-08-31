<?php

abstract class menu
{
	function plantilla_css()
	{
		return "";
	}
	
	abstract function mostrar();
	
	protected function items_de_menu($solo_primer_nivel=false)
	{
		$proyecto = toba_proyecto::get_id();
		$grupo = toba::get_sesion()->get_grupo_acceso();
		return toba_proyecto::items_menu($solo_primer_nivel, $proyecto, $grupo);
	}	
}
?>