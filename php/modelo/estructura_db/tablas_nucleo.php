<?

class tablas_nucleo
{
	static function get_lista()
	{
		return array (
  0 => 'apex_estilo',
  1 => 'apex_menu',
  2 => 'apex_log_sistema_tipo',
  3 => 'apex_fuente_datos_motor',
  4 => 'apex_grafico',
  5 => 'apex_recurso_origen',
  6 => 'apex_nivel_acceso',
  7 => 'apex_solicitud_tipo',
  8 => 'apex_columna_estilo',
  9 => 'apex_columna_formato',
  10 => 'apex_usuario_tipodoc',
  11 => 'apex_clase_tipo',
  12 => 'apex_nota_tipo',
  13 => 'apex_msg_tipo',
  14 => 'apex_objeto_mt_me_tipo_nav',
  15 => 'apex_admin_persistencia',
  16 => 'apex_tipo_datos',
);
	}

	static function get_lista_nucleo_multiproyecto()
	{
		return array (
  0 => 'apex_proyecto',
  1 => 'apex_fuente_datos',
  2 => 'apex_elemento_formulario',
  3 => 'apex_solicitud_obs_tipo',
  4 => 'apex_pagina_tipo',
  5 => 'apex_clase',
  6 => 'apex_msg',
);
	}

	static function apex_estilo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'estilo',
  'zona' => 'general',
  'desc' => 'Estilos	CSS',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'estilo',
    1 => 'descripcion',
    2 => 'estilo_paleta_p',
    3 => 'estilo_paleta_s',
    4 => 'estilo_paleta_n',
    5 => 'estilo_paleta_e',
  ),
);
	}

	static function apex_menu()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'menu',
  'zona' => 'general',
  'desc' => 'Tipos de menues',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'menu',
    1 => 'descripcion',
    2 => 'archivo',
    3 => 'soporta_frames',
  ),
);
	}

	static function apex_proyecto()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'proyecto',
  'zona' => 'general',
  'desc' => 'Tabla maestra	de	proyectos',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'descripcion',
    2 => 'descripcion_corta',
    3 => 'estilo',
    4 => 'con_frames',
    5 => 'frames_clase',
    6 => 'frames_archivo',
    7 => 'salida_impr_html_c',
    8 => 'salida_impr_html_a',
    9 => 'menu',
    10 => 'path_includes',
    11 => 'path_browser',
    12 => 'administrador',
    13 => 'listar_multiproyecto',
    14 => 'orden',
    15 => 'palabra_vinculo_std',
    16 => 'version_toba',
    17 => 'requiere_validacion',
    18 => 'usuario_anonimo',
    19 => 'validacion_intentos',
    20 => 'validacion_intentos_min',
    21 => 'validacion_debug',
    22 => 'sesion_tiempo_no_interac_min',
    23 => 'sesion_tiempo_maximo_min',
    24 => 'sesion_subclase',
    25 => 'sesion_subclase_archivo',
    26 => 'usuario_subclase',
    27 => 'usuario_subclase_archivo',
    28 => 'encriptar_qs',
    29 => 'registrar_solicitud',
    30 => 'registrar_cronometro',
    31 => 'item_inicio_sesion',
    32 => 'item_pre_sesion',
    33 => 'log_archivo',
    34 => 'log_archivo_nivel',
    35 => 'fuente_datos',
  ),
);
	}

	static function apex_log_sistema_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'log_sistema_tipo',
  'zona' => 'solicitud',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'log_sistema_tipo',
    1 => 'descripcion',
  ),
);
	}

	static function apex_fuente_datos_motor()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'fuente_datos_motor',
  'zona' => 'general',
  'desc' => 'DBMS	soportados',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'fuente_datos_motor',
    1 => 'nombre',
    2 => 'version',
  ),
);
	}

	static function apex_fuente_datos()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'fuente_datos',
  'zona' => 'general',
  'desc' => 'Bases de datos a	las que se puede acceder',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'fuente_datos',
    2 => 'descripcion',
    3 => 'descripcion_corta',
    4 => 'fuente_datos_motor',
    5 => 'host',
    6 => 'usuario',
    7 => 'clave',
    8 => 'base',
    9 => 'administrador',
    10 => 'link_instancia',
    11 => 'instancia_id',
    12 => 'subclase_archivo',
    13 => 'subclase_nombre',
    14 => 'orden',
  ),
);
	}

	static function apex_grafico()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'grafico',
  'zona' => 'general',
  'desc' => 'Tipo	de	grafico',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'grafico',
    1 => 'descripcion_corta',
    2 => 'descripcion',
    3 => 'parametros',
  ),
);
	}

	static function apex_recurso_origen()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'recurso_origen',
  'zona' => 'general',
  'desc' => 'Origen del	recurso',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'recurso_origen',
    1 => 'descripcion',
  ),
);
	}

	static function apex_nivel_acceso()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'nivel_acceso',
  'zona' => 'general',
  'desc' => 'Categoria organizadora	de	niveles de seguridad	(redobla	la	cualificaciond	e elementos	para fortalecer chequeos)',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nivel_acceso',
    1 => 'nombre',
    2 => 'descripcion',
  ),
);
	}

	static function apex_solicitud_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'solicitud_tipo',
  'zona' => 'general',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud_tipo',
    1 => 'descripcion',
    2 => 'descripcion_corta',
    3 => 'icono',
  ),
);
	}

	static function apex_elemento_formulario()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'elemento_formulario',
  'zona' => 'general',
  'desc' => 'Elementos de formulario soportados',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'elemento_formulario',
    1 => 'padre',
    2 => 'descripcion',
    3 => 'parametros',
    4 => 'proyecto',
    5 => 'exclusivo_toba',
    6 => 'obsoleto',
  ),
);
	}

	static function apex_solicitud_obs_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'solicitud_obs_tipo',
  'zona' => 'general',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'solicitud_obs_tipo',
    2 => 'descripcion',
    3 => 'criterio',
  ),
);
	}

	static function apex_pagina_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'pagina_tipo',
  'zona' => 'general',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'pagina_tipo',
    2 => 'descripcion',
    3 => 'clase_nombre',
    4 => 'clase_archivo',
    5 => 'include_arriba',
    6 => 'include_abajo',
    7 => 'exclusivo_toba',
    8 => 'contexto',
  ),
);
	}

	static function apex_columna_estilo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'columna_estilo',
  'zona' => 'general',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'columna_estilo',
    1 => 'css',
    2 => 'descripcion',
    3 => 'descripcion_corta',
  ),
);
	}

	static function apex_columna_formato()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'columna_formato',
  'zona' => 'general',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'columna_formato',
    1 => 'funcion',
    2 => 'archivo',
    3 => 'descripcion',
    4 => 'descripcion_corta',
    5 => 'parametros',
  ),
);
	}

	static function apex_usuario_tipodoc()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'usuario_tipodoc',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'usuario_tipodoc',
    1 => 'descripcion',
  ),
);
	}

	static function apex_clase_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'clase_tipo',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_tipo',
    1 => 'descripcion_corta',
    2 => 'descripcion',
    3 => 'icono',
    4 => 'orden',
    5 => 'metodologia',
  ),
);
	}

	static function apex_clase()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'clase',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'clase',
    2 => 'clase_tipo',
    3 => 'archivo',
    4 => 'descripcion',
    5 => 'descripcion_corta',
    6 => 'icono',
    7 => 'screenshot',
    8 => 'ancestro_proyecto',
    9 => 'ancestro',
    10 => 'instanciador_id',
    11 => 'instanciador_proyecto',
    12 => 'instanciador_item',
    13 => 'editor_id',
    14 => 'editor_proyecto',
    15 => 'editor_item',
    16 => 'editor_ancestro_proyecto',
    17 => 'editor_ancestro',
    18 => 'plan_dump_objeto',
    19 => 'sql_info',
    20 => 'doc_clase',
    21 => 'doc_db',
    22 => 'doc_sql',
    23 => 'vinculos',
    24 => 'autodoc',
    25 => 'parametro_a',
    26 => 'parametro_b',
    27 => 'parametro_c',
    28 => 'exclusivo_toba',
  ),
);
	}

	static function apex_nota_tipo()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_notas.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'nota_tipo',
  'zona' => 'general',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nota_tipo',
    1 => 'descripcion',
    2 => 'icono',
  ),
);
	}

	static function apex_msg_tipo()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_mensajes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'msg_tipo',
  'zona' => 'general',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'msg_tipo',
    1 => 'descripcion',
    2 => 'icono',
  ),
);
	}

	static function apex_msg()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_mensajes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'msg',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'msg',
    1 => 'indice',
    2 => 'proyecto',
    3 => 'msg_tipo',
    4 => 'descripcion_corta',
    5 => 'mensaje_a',
    6 => 'mensaje_b',
    7 => 'mensaje_c',
    8 => 'mensaje_customizable',
  ),
);
	}

	static function apex_objeto_mt_me_tipo_nav()
	{
		return array (
  'archivo' => 'pgsql_a10_componente_ci.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tipo_navegacion',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tipo_navegacion',
    1 => 'descripcion',
  ),
);
	}

	static function apex_admin_persistencia()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'ap',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'ap',
    1 => 'clase',
    2 => 'archivo',
    3 => 'descripcion',
    4 => 'categoria',
  ),
);
	}

	static function apex_tipo_datos()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tipo',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tipo',
    1 => 'descripcion',
  ),
);
	}

}
?>