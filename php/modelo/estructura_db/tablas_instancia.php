<?

class tablas_instancia
{
	static function get_lista_proyecto()
	{
		return array (
  0 => 'apex_et_item',
  1 => 'apex_et_objeto',
  2 => 'apex_et_preferencias',
  3 => 'apex_arbol_items_fotos',
  4 => 'apex_admin_album_fotos',
  5 => 'apex_ap_tarea_usuario',
);
	}

	static function get_lista_global_usuario()
	{
		return array (
  0 => 'apex_usuario',
);
	}

	static function get_lista_proyecto_usuario()
	{
		return array (
  0 => 'apex_usuario_proyecto',
);
	}

	static function get_lista_proyecto_log()
	{
		return array (
  0 => 'apex_solicitud',
  1 => 'apex_sesion_browser',
  2 => 'apex_solicitud_browser',
  3 => 'apex_solicitud_wddx',
  4 => 'apex_solicitud_consola',
  5 => 'apex_solicitud_cronometro',
  6 => 'apex_solicitud_observacion',
  7 => 'apex_solicitud_obj_observacion',
  8 => 'apex_log_objeto',
);
	}

	static function get_lista_global_log()
	{
		return array (
  0 => 'apex_log_sistema',
  1 => 'apex_log_error_login',
  2 => 'apex_log_ip_rechazada',
);
	}

	static function get_lista_global()
	{
		return array (
  0 => 'apex_revision',
  1 => 'apex_instancia',
);
	}

	static function apex_et_item()
	{
		return array (
  'archivo' => 'pgsql_a09_entorno_trabajo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_where' => '(item_proyecto =\\\'%%\\\')',
  'dump_order_by' => 'usuario, item',
  'zona' => 'entorno_trabajo',
  'desc' => 'Portafolios de items',
  'version' => '1.0',
  'instancia' => '1',
  'columnas' => 
  array (
    0 => 'item_proyecto',
    1 => 'item',
    2 => 'usuario',
    3 => 'creacion',
  ),
);
	}

	static function apex_et_objeto()
	{
		return array (
  'archivo' => 'pgsql_a09_entorno_trabajo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_where' => '(objeto_proyecto =\\\'%%\\\')',
  'dump_order_by' => 'usuario, objeto',
  'zona' => 'entorno_trabajo',
  'desc' => 'Portafolios de objetos',
  'version' => '1.0',
  'instancia' => '1',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'usuario',
    3 => 'creacion',
  ),
);
	}

	static function apex_et_preferencias()
	{
		return array (
  'archivo' => 'pgsql_a09_entorno_trabajo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_where' => '(usuario_proyecto =\\\'%%\\\')',
  'dump_order_by' => 'usuario',
  'zona' => 'entorno_trabajo',
  'desc' => 'Portafolios de Item',
  'version' => '1.0',
  'instancia' => '1',
  'columnas' => 
  array (
    0 => 'usuario_proyecto',
    1 => 'usuario',
    2 => 'listado_obj_pref',
    3 => 'listado_item_pref',
    4 => 'item_proyecto',
    5 => 'item',
  ),
);
	}

	static function apex_usuario()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'usuario',
  'zona' => 'usuario',
  'desc' => '',
  'instancia' => '1',
  'usuario' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'usuario',
    1 => 'clave',
    2 => 'nombre',
    3 => 'usuario_tipodoc',
    4 => 'pre',
    5 => 'ciu',
    6 => 'suf',
    7 => 'email',
    8 => 'telefono',
    9 => 'vencimiento',
    10 => 'dias',
    11 => 'hora_entrada',
    12 => 'hora_salida',
    13 => 'ip_permitida',
    14 => 'solicitud_registrar',
    15 => 'solicitud_obs_tipo_proyecto',
    16 => 'solicitud_obs_tipo',
    17 => 'solicitud_observacion',
    18 => 'parametro_a',
    19 => 'parametro_b',
    20 => 'parametro_c',
    21 => 'autentificacion',
  ),
);
	}

	static function apex_usuario_proyecto()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario',
  'zona' => 'usuario',
  'instancia' => '1',
  'usuario' => '1',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario',
    2 => 'usuario_grupo_acc',
    3 => 'usuario_perfil_datos',
  ),
);
	}

	static function apex_arbol_items_fotos()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario, foto_nombre',
  'zona' => 'usuario',
  'instancia' => '1',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario',
    2 => 'foto_nombre',
    3 => 'foto_nodos_visibles',
    4 => 'foto_opciones',
  ),
);
	}

	static function apex_admin_album_fotos()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario, foto_tipo, foto_nombre',
  'zona' => 'usuario',
  'instancia' => '1',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario',
    2 => 'foto_tipo',
    3 => 'foto_nombre',
    4 => 'foto_nodos_visibles',
    5 => 'foto_opciones',
    6 => 'predeterminada',
  ),
);
	}

	static function apex_solicitud()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'solicitud',
    2 => 'solicitud_tipo',
    3 => 'item_proyecto',
    4 => 'item',
    5 => 'item_id',
    6 => 'momento',
    7 => 'tiempo_respuesta',
  ),
);
	}

	static function apex_sesion_browser()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'sesion_browser',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'sesion_browser',
    1 => 'proyecto',
    2 => 'usuario',
    3 => 'ingreso',
    4 => 'egreso',
    5 => 'observaciones',
    6 => 'php_id',
    7 => 'ip',
    8 => 'punto_acceso',
  ),
);
	}

	static function apex_solicitud_browser()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_browser',
  'dump_from' => 'apex_solicitud',
  'dump_where' => '(apex_solicitud.solicitud = dd.solicitud_browser) AND (apex_solicitud.proyecto =\\\'%%\\\')',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud_browser',
    1 => 'sesion_browser',
    2 => 'ip',
  ),
);
	}

	static function apex_solicitud_wddx()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_wddx',
  'dump_from' => 'apex_solicitud',
  'dump_where' => '((apex_solicitud.solicitud = dd.solicitud_wddx) AND (apex_solicitud.proyecto =\\\'%%\\\'))',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud_wddx',
    1 => 'usuario',
    2 => 'ip',
    3 => 'instancia',
    4 => 'instancia_usuario',
    5 => 'paquete',
  ),
);
	}

	static function apex_solicitud_consola()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_consola',
  'dump_from' => 'apex_solicitud',
  'dump_where' => '((apex_solicitud.solicitud = dd.solicitud_consola) AND (apex_solicitud.proyecto =\\\'%%\\\'))',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud_consola',
    1 => 'usuario',
    2 => 'ip',
    3 => 'llamada',
    4 => 'entorno',
  ),
);
	}

	static function apex_solicitud_cronometro()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud',
  'dump_from' => 'apex_solicitud',
  'dump_where' => '((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.proyecto =\\\'%%\\\'))',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud',
    1 => 'marca',
    2 => 'nivel_ejecucion',
    3 => 'texto',
    4 => 'tiempo',
  ),
);
	}

	static function apex_solicitud_observacion()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_observacion',
  'dump_from' => 'apex_solicitud',
  'dump_where' => '((apex_solicitud.solicitud = dd.solicitud_observacion) AND (apex_solicitud.proyecto =\\\'%%\\\'))',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud_observacion',
    1 => 'solicitud_obs_tipo_proyecto',
    2 => 'solicitud_obs_tipo',
    3 => 'solicitud',
    4 => 'observacion',
  ),
);
	}

	static function apex_solicitud_obj_observacion()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_obj_observacion',
  'dump_from' => 'apex_solicitud',
  'dump_where' => '((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.proyecto =\\\'%%\\\'))',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud_obj_observacion',
    1 => 'solicitud_obj_obs_tipo',
    2 => 'solicitud',
    3 => 'objeto_proyecto',
    4 => 'objeto',
    5 => 'observacion',
  ),
);
	}

	static function apex_log_objeto()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'log_objeto',
  'dump_where' => 'objeto_proyecto =\\\'%%\\\'',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'log_objeto',
    1 => 'momento',
    2 => 'usuario',
    3 => 'objeto_proyecto',
    4 => 'objeto',
    5 => 'observacion',
  ),
);
	}

	static function apex_log_sistema()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'log_sistema',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'log_sistema',
    1 => 'momento',
    2 => 'usuario',
    3 => 'log_sistema_tipo',
    4 => 'observaciones',
  ),
);
	}

	static function apex_log_error_login()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'log_error_login',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'log_error_login',
    1 => 'momento',
    2 => 'usuario',
    3 => 'clave',
    4 => 'ip',
    5 => 'gravedad',
    6 => 'mensaje',
    7 => 'punto_acceso',
  ),
);
	}

	static function apex_log_ip_rechazada()
	{
		return array (
  'archivo' => 'pgsql_a03_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'ip',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'ip',
    1 => 'momento',
  ),
);
	}

	static function apex_ap_tarea_usuario()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'tarea, usuario',
  'dump_from' => 'apex_ap_tarea',
  'dump_where' => '(apex_ap_tarea.tarea = dd.tarea) AND (apex_ap_tarea.proyecto =\\\'%%\\\')',
  'zona' => 'admin_proyectos',
  'instancia' => '1',
  'desc' => 'Prioridad de Tarea',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tarea',
    1 => 'usuario',
    2 => 'fecha_inicio',
    3 => 'fecha_fin',
    4 => 'observacion',
  ),
);
	}

	static function apex_revision()
	{
		return array (
  'archivo' => 'pgsql_a00_instancia.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'revision',
  'zona' => 'general',
  'desc' => 'Especifica la revision del SVN con que se creo el proyecto',
  'version' => '1.0',
  'instancia' => '1',
  'columnas' => 
  array (
    0 => 'revision',
    1 => 'creacion',
  ),
);
	}

	static function apex_instancia()
	{
		return array (
  'archivo' => 'pgsql_a00_instancia.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'instancia',
  'instancia' => '1',
  'zona' => 'general',
  'desc' => 'Datos de la instancia',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'instancia',
    1 => 'version',
    2 => 'institucion',
    3 => 'observaciones',
    4 => 'administrador_1',
    5 => 'administrador_2',
    6 => 'administrador_3',
    7 => 'creacion',
  ),
);
	}

}
?>