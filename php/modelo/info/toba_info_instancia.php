<?php

class toba_info_instancia
{
	
	/**
	*	Retorna los proyectos incluidos en la instancia que tienen cierto estilo
	*/
	function get_proyectos_con_estilo($estilo)
	{
		$sql = "SELECT proyecto FROM apex_proyecto WHERE estilo='$estilo'";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	function get_usuarios()
	{
		
	}
		
	
}

?>