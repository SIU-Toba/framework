<?php 
require_once("tutorial/pant_tutorial.php");

class pant_concepto extends pant_tutorial
{
}

class pant_creacion extends pant_tutorial
{
	function generar_layout()
	{
		echo mostrar_video('componentes/componente-crear');
	}	
	
}

?>