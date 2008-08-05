<?php

class toba_carpeta_menu extends toba_item_menu 
{
	protected $icono = "nucleo/carpeta.gif";
	protected $carpeta = true;
	protected $propiedades = false;	
		
	function es_carpeta()
	{
		return true;	
	}

}

?>