<?php

class zona_tutorial extends toba_zona 
{

	function cargada()
	{
		return true;
	}	
	
	function generar_html_barra_vinculos()
	{
		parent::generar_html_barra_vinculos();
		foreach($this->items_vecinos as $item){
		}
	}	
}

?>