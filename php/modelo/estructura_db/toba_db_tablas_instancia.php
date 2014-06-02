<?php

class toba_db_tablas_instancia
{
	static function get_lista_global()
	{
		return array (
  0 => 'apex_revision',
  1 => 'apex_instancia',
  2 => 'apex_checksum_proyectos',
);
	}

	static function get_lista_proyecto()
	{
		return array (
  0 => 'apex_tarea',
  1 => 'apex_arbol_items_fotos',
  2 => 'apex_admin_album_fotos',
  3 => 'apex_admin_param_previsualizazion',
  4 => 'apex_usuario_proyecto_gadgets',
);
	}

	static function get_lista_global_usuario()
	{
		return array (
  0 => 'apex_usuario',
  1 => 'apex_usuario_pregunta_secreta',
  2 => 'apex_usuario_pwd_usados',
);
	}

	static function get_lista_proyecto_log()
	{
		return array (
  0 => 'apex_solicitud',
  1 => 'apex_sesion_browser',
  2 => 'apex_solicitud_browser',
  3 => 'apex_solicitud_consola',
  4 => 'apex_solicitud_observacion',
  5 => 'apex_log_tarea',
  6 => 'apex_log_objeto',
  7 => 'apex_solicitud_web_service',
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

	static function get_lista_proyecto_usuario()
	{
		return array (
  0 => 'apex_usuario_proyecto',
  1 => 'apex_usuario_proyecto_perfil_datos',
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

	static function apex_checksum_proyectos()
	{
		return array (
  'archivo' => 'pgsql_a00_tablas_instancia.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'clave_proyecto' => 'proyecto',
  'dump_order_by' => 'proyecto',
  'zona' => 'general',
  'desc' => 'Especifica el checksum surgido de los metadatos actuales del proyecto',
  'instancia' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'checksum',
    1 => 'proyecto',
  ),
);
	}

	static function apex_tarea()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'proyecto, tarea',
  'dump_where' => '(	proyecto =	\\\'%%\\\' )',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'tarea',
  'zona' => 'nucleo',
  'instancia' => '1',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'tarea',
    2 => 'nombre',
    3 => 'tarea_clase',
    4 => 'tarea_objeto',
    5 => 'ejecucion_proxima',
    6 => 'intervalo_repeticion',
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
    3 => 'email',
    4 => 'autentificacion',
    5 => 'bloqueado',
    6 => 'parametro_a',
    7 => 'parametro_b',
    8 => 'parametro_c',
    9 => 'solicitud_registrar',
    10 => 'solicitud_obs_tipo_proyecto',
    11 => 'solicitud_obs_tipo',
    12 => 'solicitud_observacion',
    13 => 'usuario_tipodoc',
    14 => 'pre',
    15 => 'ciu',
    16 => 'suf',
    17 => 'telefono',
    18 => 'vencimiento',
    19 => 'dias',
    20 => 'hora_entrada',
    21 => 'hora_salida',
    22 => 'ip_permitida',
    23 => 'forzar_cambio_pwd',
  ),
);
	}

	static function apex_usuario_pregunta_secreta()
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
  'version' => '2.0',
  'columnas' => 
  array (
    0 => 'cod_pregunta_secreta',
    1 => 'usuario',
    2 => 'pregunta',
    3 => 'respuesta',
    4 => 'activa',
  ),
);
	}

	static function apex_usuario_pwd_usados()
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
  'version' => '2.0',
  'columnas' => 
  array (
    0 => 'cod_pwd_pasados',
    1 => 'usuario',
    2 => 'clave',
    3 => 'algoritmo',
    4 => 'fecha_cambio',
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
    4 => 'perfil_datos',
  ),
);
	}

	static function apex_solicitud()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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

	static function apex_solicitud_observacion()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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

	static function apex_log_tarea()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'log_tarea',
  'dump_where' => '(	proyecto =	\\\'%%\\\' )',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'log_tarea',
  'zona' => 'nucleo',
  'desc' => '',
  'historica' => '1',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'log_tarea',
    2 => 'tarea',
    3 => 'nombre',
    4 => 'tarea_clase',
    5 => 'tarea_objeto',
    6 => 'ejecucion',
  ),
);
	}

	static function apex_log_objeto()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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

	static function apex_solicitud_web_service()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_log_instancia.sql',
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
    2 => 'metodo',
    3 => 'ip',
  ),
);
	}

	static function apex_usuario_proyecto_gadgets()
	{
		return array (
  'archivo' => 'pgsql_a07_tablas_gadgets.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'proyecto, gadget, usuario',
  'dump_where' => '(	proyecto =	\\\'%%\\\' )',
  'zona' => 'nucleo',
  'desc' => '',
  'version' => '1.0',
  'instancia' => '1',
  'columnas' => 
  array (
    0 => 'usuario',
    1 => 'proyecto',
    2 => 'gadget',
    3 => 'orden',
    4 => 'eliminable',
  ),
);
	}

	static function apex_usuario_proyecto()
	{
		return array (
  'archivo' => 'pgsql_a70_tablas_relacion_usuario_perfil.sql',
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
    1 => 'usuario_grupo_acc',
    2 => 'usuario',
    3 => 'usuario_perfil_datos',
  ),
);
	}

	static function apex_usuario_proyecto_perfil_datos()
	{
		return array (
  'archivo' => 'pgsql_a70_tablas_relacion_usuario_perfil.sql',
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
    1 => 'usuario_perfil_datos',
    2 => 'usuario',
  ),
);
	}

}

?>