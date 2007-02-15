<?php
require_once("tutorial/pant_tutorial.php");

class pant_introduccion extends pant_tutorial 
{
	function generar_layout()
	{
		
	}
}


class pant_acceso extends pant_tutorial 
{
	function generar_layout()
	{
		echo mostrar_video('editor/editor-acceso', 1016, 535, true);		
	}
}



class pant_items extends pant_tutorial 
{
	function generar_layout()
	{
		
	}
}



class pant_prev extends pant_tutorial 
{
	function generar_layout()
	{
		
	}
}


?>