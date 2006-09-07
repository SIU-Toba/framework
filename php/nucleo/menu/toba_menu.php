<?php

abstract class toba_menu
{
	function plantilla_css()
	{
		return "";
	}
	
	abstract function mostrar();
	
	protected function items_de_menu($solo_primer_nivel=false)
	{
		return toba_proyecto::items_menu($solo_primer_nivel);
	}	
}
?>