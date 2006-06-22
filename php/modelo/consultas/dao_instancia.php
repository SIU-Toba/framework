<?php

class dao_instancia
{
	
	/**
	*	Retorna los proyectos incluidos en la instancia que tienen cierto estilo
	*/
	function get_proyectos_con_estilo($estilo)
	{
		$sql = "SELECT proyecto FROM apex_proyecto WHERE estilo='$estilo'";
		return consultar_fuente($sql);
	}
		
	
}

?>