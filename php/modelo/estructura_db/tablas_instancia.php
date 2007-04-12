<?php

class tablas_instancia
{
	static function get_lista_global()
	{
		return array (
  0 => 'apex_revision',
  1 => 'apex_instancia',
);
	}

	static function get_lista_proyecto_log()
	{
		return array (
  0 => 'apex_log_objeto',
  1 => 'apex_solicitud',
  2 => 'apex_sesion_browser',
  3 => 'apex_solicitud_browser',
  4 => 'apex_solicitud_consola',
  5 => 'apex_solicitud_cronometro',
  6 => 'apex_solicitud_observacion',
);
	}

	static function get_lista_proyecto()
	{
		return array (
  0 => 'apex_arbol_items_fotos',
  1 => 'apex_admin_album_fotos',
  2 => 'apex_admin_param_previsualizazion',
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

	static function get_lista_global_log()
	{
		return array (
  0 => 'apex_log_sistema',
  1 => 'apex_log_error_login',
  2 => 'apex_log_ip_rechazada',
);
	}

	static function apex_revision()
	{
		return array (
  'archivo' => 'pgsql_a00_tablas_instancia.sql',
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
  'archivo' => 'pgsql_a00_tablas_instancia.sql',
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

	static function apex_log_objeto()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
    5 => 'item',
    6 => 'observacion',
  ),
);
	}

	static function apex_arbol_items_fotos()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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

	static function apex_admin_param_previsualizazion()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario, proyecto',
  'zona' => 'usuario',
  'instancia' => '1',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario',
    2 => 'grupo_acceso',
    3 => 'punto_acceso',
  ),
);
	}

	static function apex_usuario()
	{
		return array (
  'archivo' => 'pgsql_a02_tablas_usuario.sql',
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
  'archivo' => 'pgsql_a02_tablas_usuario.sql',
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

	static function apex_solicitud()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
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
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
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
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_browser',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'sesion_browser',
    2 => 'solicitud_proyecto',
    3 => 'solicitud_browser',
    4 => 'ip',
  ),
);
	}

	static function apex_solicitud_consola()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_consola',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'solicitud_consola',
    2 => 'usuario',
    3 => 'ip',
    4 => 'llamada',
    5 => 'entorno',
  ),
);
	}

	static function apex_solicitud_cronometro()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud, marca',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'solicitud',
    2 => 'marca',
    3 => 'nivel_ejecucion',
    4 => 'texto',
    5 => 'tiempo',
  ),
);
	}

	static function apex_solicitud_observacion()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'solicitud_observacion',
  'zona' => 'solicitud',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'solicitud',
    2 => 'solicitud_observacion',
    3 => 'solicitud_obs_tipo_proyecto',
    4 => 'solicitud_obs_tipo',
    5 => 'observacion',
  ),
);
	}

	static function apex_log_sistema()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
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
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
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
  'archivo' => 'pgsql_a04_tablas_solicitudes.sql',
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

}
?>