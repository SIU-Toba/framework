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

	static function get_sesiones($proyecto, $filtro)
	{
		$where = '';
		if(isset($filtro['sesion'])){
			$where .= " AND se.sesion_browser = {$filtro['sesion']} ";		
		} else {
			if(isset($filtro['desde'])) {
				$where .= " AND date(se.ingreso) >= '{$filtro['desde']}' ";
			}
			if(isset($filtro['hasta'])) {
				$where .= " AND date(se.ingreso) <= '{$filtro['hasta']}' ";
			}
		}
		$sql = "
				SELECT	se.sesion_browser as id,
						usuario,
						ingreso,
						egreso,
						se.ip as ip,
						count(so.solicitud_browser) as solicitudes
					FROM apex_sesion_browser se
						LEFT OUTER JOIN apex_solicitud_browser so
						ON se.sesion_browser = so.sesion_browser
						AND se.proyecto = so.proyecto
					WHERE se.proyecto = '$proyecto'
					$where
					GROUP BY 1,2,3,4,5
					ORDER BY 3 DESC;";
		return toba::db()->consultar($sql);		
	}

	static function get_solicitudes_browser($sesion)
	{
		$sql = "
				SELECT	s.solicitud as id,
						s.item_proyecto as item_proyecto,
						s.item as item,
						s.momento as momento,
						s.tiempo_respuesta as tiempo,
						count(so.solicitud_observacion) as observaciones
				FROM 	apex_solicitud s,
						apex_solicitud_browser sb
						LEFT OUTER JOIN apex_solicitud_observacion so
							ON sb.solicitud_browser = so.solicitud_observacion
							AND sb.solicitud_proyecto = so.proyecto
				WHERE	s.solicitud = sb.solicitud_browser
				AND	s.proyecto = sb.solicitud_proyecto
				AND	sb.sesion_browser = '$sesion'
				GROUP BY 1,2,3,4,5
				ORDER BY 4 DESC;";
		return toba::db()->consultar($sql);		
	}
	
	static function get_solicitud_observaciones($solicitud)
	{
		$sql = "
				SELECT 	observacion,
						ot.descripcion
				FROM apex_solicitud_observacion o
					LEFT OUTER JOIN apex_solicitud_obs_tipo ot
						ON ot.solicitud_obs_tipo = o.solicitud_obs_tipo
						AND ot.proyecto = o.solicitud_obs_tipo_proyecto
				WHERE o.solicitud_observacion = '$solicitud';";
		return toba::db()->consultar($sql);		
	}
	
	static function get_solicitudes_consola($proyecto, $filtro)
	{
		$sql = "		
				SELECT	s.solicitud as id,
						s.momento as momento,
						s.item_proyecto as item_proyecto,
						s.item as item,
						s.tiempo_respuesta as tiempo,
						sc.usuario as usuario,
						sc.llamada as llamada
				FROM	apex_solicitud s,
						apex_solicitud_consola sc
				WHERE	s.proyecto = sc.proyecto
				AND	s.solicitud = sc.solicitud_consola
				AND	s.proyecto = '$proyecto';";
		return toba::db()->consultar($sql);		
	}
}
?>