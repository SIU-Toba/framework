<?

class consultas_instancia
{
	static function get_lista_proyectos()
	{
		$sql = "SELECT proyecto FROM apex_proyecto;";
		return toba::db()->consultar($sql);
	}

	static function get_datos_proyecto($proyecto)
	{
		$sql = "SELECT * FROM apex_proyecto WHERE proyecto = '$proyecto';";
		$rs = toba::db()->consultar($sql);
		return $rs[0];
	}

	static function get_cantidad_usuarios()
	{
		$sql = "SELECT count(*) as cantidad FROM apex_usuario;";
		$rs = toba::db()->consultar($sql);
		return $rs[0]['cantidad'];
	}

	static function get_cantidad_usuarios_proyecto($proyecto)
	{
		$sql = "SELECT count(*) as cantidad FROM apex_usuario_proyecto WHERE proyecto = '$proyecto';";
		$rs = toba::db()->consultar($sql);
		return $rs[0]['cantidad'];
	}

	static function get_cantidad_ips_rechazadas()
	{
		$sql = "SELECT count(*) as cantidad FROM apex_log_ip_rechazada;";
		$rs = toba::db()->consultar($sql);
		return $rs[0]['cantidad'];
	}
	
	//---------------------------------------------------------------------
	//------ SESIONES -----------------------------------------------------
	//---------------------------------------------------------------------

	static function get_cantidad_sesiones_proyecto($proyecto)
	{
		$sql = "SELECT count(*) as cantidad FROM apex_sesion_browser WHERE proyecto = '$proyecto';";
		$rs = toba::db()->consultar($sql);
		return $rs[0]['cantidad'];
	}

	static function get_lista_sesiones($proyecto, $filtro)
	{
		$sql = "SELECT * FROM apex_sesion_browser WHERE proyecto = '$proyecto';";
		return toba::db()->consultar($sql);		
	}
}
?>