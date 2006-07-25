<?

class tablas_proyecto
{
	static function get_lista()
	{
		return array (
  0 => 'apex_proyecto',
  1 => 'apex_fuente_datos',
  2 => 'apex_elemento_formulario',
  3 => 'apex_solicitud_obs_tipo',
  4 => 'apex_pagina_tipo',
  5 => 'apex_usuario_perfil_datos',
  6 => 'apex_usuario_grupo_acc',
  7 => 'apex_patron',
  8 => 'apex_patron_info',
  9 => 'apex_buffer',
  10 => 'apex_item_zona',
  11 => 'apex_clase',
  12 => 'apex_clase_info',
  13 => 'apex_clase_dependencias',
  14 => 'apex_patron_dependencias',
  15 => 'apex_objeto_categoria',
  16 => 'apex_solicitud_obj_obs_tipo',
  17 => 'apex_vinculo',
  18 => 'apex_usuario_grupo_acc_item',
  19 => 'apex_nucleo',
  20 => 'apex_nucleo_info',
  21 => 'apex_conversion',
  22 => 'apex_item_proto',
  23 => 'apex_clase_proto',
  24 => 'apex_clase_proto_metodo',
  25 => 'apex_clase_proto_propiedad',
  26 => 'apex_objeto_proto',
  27 => 'apex_objeto_proto_metodo',
  28 => 'apex_objeto_proto_propiedad',
  29 => 'apex_nucleo_proto',
  30 => 'apex_nucleo_proto_metodo',
  31 => 'apex_nucleo_proto_propiedad',
  32 => 'apex_dimension_tipo',
  33 => 'apex_dimension_grupo',
  34 => 'apex_dimension',
  35 => 'apex_dimension_perfil_datos',
  36 => 'apex_nota',
  37 => 'apex_patron_nota',
  38 => 'apex_item_nota',
  39 => 'apex_clase_nota',
  40 => 'apex_objeto_nota',
  41 => 'apex_nucleo_nota',
  42 => 'apex_msg',
  43 => 'apex_patron_msg',
  44 => 'apex_item_msg',
  45 => 'apex_clase_msg',
  46 => 'apex_objeto_msg',
  47 => 'apex_ap_version',
  48 => 'apex_ap_tarea',
  49 => 'apex_tp_tarea',
  50 => 'apex_objeto_mapa',
  51 => 'apex_permiso',
  52 => 'apex_permiso_grupo_acc',
);
	}

	static function apex_proyecto()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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
    29 => 'combo_cambiar_proyecto',
    30 => 'registrar_solicitud',
    31 => 'registrar_cronometro',
    32 => 'item_inicio_sesion',
    33 => 'item_pre_sesion',
    34 => 'log_archivo',
    35 => 'log_archivo_nivel',
    36 => 'fuente_datos',
  ),
);
	}

	static function apex_fuente_datos()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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
    2 => 'fuente_datos_motor',
    3 => 'descripcion',
    4 => 'descripcion_corta',
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

	static function apex_elemento_formulario()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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
  'archivo' => 'pgsql_a01_nucleo.sql',
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
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_usuario_perfil_datos()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario_perfil_datos',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario_perfil_datos',
    2 => 'nombre',
    3 => 'descripcion',
    4 => 'listar',
  ),
);
	}

	static function apex_usuario_grupo_acc()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario_grupo_acc',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario_grupo_acc',
    2 => 'nombre',
    3 => 'nivel_acceso',
    4 => 'descripcion',
    5 => 'vencimiento',
    6 => 'dias',
    7 => 'hora_entrada',
    8 => 'hora_salida',
    9 => 'listar',
  ),
);
	}

	static function apex_patron()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'patron',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'patron',
    2 => 'archivo',
    3 => 'descripcion',
    4 => 'descripcion_corta',
    5 => 'exclusivo_toba',
    6 => 'autodoc',
  ),
);
	}

	static function apex_patron_info()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'patron',
  'dump_where' => '( patron_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'patron_proyecto',
    1 => 'patron',
    2 => 'descripcion_breve',
    3 => 'descripcion_larga',
  ),
);
	}

	static function apex_buffer()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'buffer',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'buffer',
    2 => 'descripcion_corta',
    3 => 'descripcion',
    4 => 'cuerpo',
    5 => 'archivo_origen',
  ),
);
	}

	static function apex_item_zona()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'zona',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'zona',
    2 => 'nombre',
    3 => 'clave_editable',
    4 => 'archivo',
    5 => 'descripcion',
  ),
);
	}

	static function apex_clase()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_clase_info()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'clase',
  'dump_where' => '(	clase_proyecto	= \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_proyecto',
    1 => 'clase',
    2 => 'descripcion_breve',
    3 => 'descripcion_larga',
  ),
);
	}

	static function apex_clase_dependencias()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'clase_consumidora, identificador',
  'dump_where' => '(	clase_consumidora_proyecto	= \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_consumidora_proyecto',
    1 => 'clase_consumidora',
    2 => 'identificador',
    3 => 'descripcion',
    4 => 'clase_proveedora_proyecto',
    5 => 'clase_proveedora',
  ),
);
	}

	static function apex_patron_dependencias()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'patron, clase',
  'dump_where' => '(	patron_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'patron_proyecto',
    1 => 'patron',
    2 => 'clase_proyecto',
    3 => 'clase',
    4 => 'cantidad_minima',
    5 => 'cantidad_maxima',
    6 => 'descripcion',
  ),
);
	}

	static function apex_objeto_categoria()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'objeto_categoria',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'objeto_categoria',
    2 => 'descripcion',
  ),
);
	}

	static function apex_solicitud_obj_obs_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'solicitud_obj_obs_tipo',
  'dump_where' => '(	clase_proyecto	= \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'solicitud_obj_obs_tipo',
    1 => 'descripcion',
    2 => 'clase_proyecto',
    3 => 'clase',
  ),
);
	}

	static function apex_vinculo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'origen_item, origen_objeto, destino_item, destino_objeto',
  'dump_where' => '(	origen_item_proyecto	= \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'origen_item_id',
    1 => 'origen_item_proyecto',
    2 => 'origen_item',
    3 => 'origen_objeto_proyecto',
    4 => 'origen_objeto',
    5 => 'destino_item_id',
    6 => 'destino_item_proyecto',
    7 => 'destino_item',
    8 => 'destino_objeto_proyecto',
    9 => 'destino_objeto',
    10 => 'frame',
    11 => 'canal',
    12 => 'indice',
    13 => 'vinculo_tipo',
    14 => 'inicializacion',
    15 => 'operacion',
    16 => 'texto',
    17 => 'imagen_recurso_origen',
    18 => 'imagen',
  ),
);
	}

	static function apex_usuario_grupo_acc_item()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario_grupo_acc, item',
  'zona' => 'usuario, item',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario_grupo_acc',
    2 => 'item_id',
    3 => 'item',
  ),
);
	}

	static function apex_nucleo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'nucleo',
  'zona' => 'nucleo',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'nucleo',
    2 => 'nucleo_tipo',
    3 => 'archivo',
    4 => 'descripcion',
    5 => 'descripcion_corta',
    6 => 'doc_nucleo',
    7 => 'doc_db',
    8 => 'doc_sql',
    9 => 'autodoc',
    10 => 'orden',
  ),
);
	}

	static function apex_nucleo_info()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'nucleo',
  'dump_where' => '(	nucleo_proyecto =	\\\'%%\\\' )',
  'zona' => 'nucleo',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nucleo_proyecto',
    1 => 'nucleo',
    2 => 'descripcion_breve',
    3 => 'descripcion_larga',
  ),
);
	}

	static function apex_conversion()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'proyecto',
  'dump_where' => '(	proyecto =	\\\'%%\\\' )',
  'zona' => 'nucleo',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'conversion_aplicada',
    2 => 'fecha',
  ),
);
	}

	static function apex_item_proto()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'item',
  'dump_where' => '(	item_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'item_proyecto',
    1 => 'item',
    2 => 'descripcion',
    3 => 'logica',
  ),
);
	}

	static function apex_clase_proto()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'clase',
  'dump_where' => '(	clase_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_proyecto',
    1 => 'clase',
    2 => 'descripcion',
    3 => 'logica',
  ),
);
	}

	static function apex_clase_proto_metodo()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'clase',
  'dump_where' => '(	clase_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_proyecto',
    1 => 'clase',
    2 => 'metodo',
    3 => 'orden',
    4 => 'acceso',
    5 => 'descripcion',
    6 => 'parametros',
    7 => 'retorno',
    8 => 'logica',
    9 => 'php',
    10 => 'auto_subclase',
  ),
);
	}

	static function apex_clase_proto_propiedad()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'clase',
  'dump_where' => '(	clase_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_proyecto',
    1 => 'clase',
    2 => 'propiedad',
    3 => 'orden',
    4 => 'tipo',
    5 => 'descripcion',
  ),
);
	}

	static function apex_objeto_proto()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'objeto',
  'dump_where' => '(	objeto_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'descripcion',
    3 => 'logica',
  ),
);
	}

	static function apex_objeto_proto_metodo()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'objeto',
  'dump_where' => '(	objeto_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'metodo',
    3 => 'orden',
    4 => 'acceso',
    5 => 'descripcion',
    6 => 'parametros',
    7 => 'retorno',
    8 => 'logica',
    9 => 'php',
  ),
);
	}

	static function apex_objeto_proto_propiedad()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'objeto',
  'dump_where' => '(	objeto_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'propiedad',
    3 => 'orden',
    4 => 'tipo',
    5 => 'descripcion',
  ),
);
	}

	static function apex_nucleo_proto()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'nucleo',
  'dump_where' => '(	nucleo_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nucleo_proyecto',
    1 => 'nucleo',
    2 => 'descripcion',
    3 => 'logica',
  ),
);
	}

	static function apex_nucleo_proto_metodo()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'nucleo',
  'dump_where' => '(	nucleo_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nucleo_proyecto',
    1 => 'nucleo',
    2 => 'metodo',
    3 => 'orden',
    4 => 'acceso',
    5 => 'descripcion',
    6 => 'parametros',
    7 => 'retorno',
    8 => 'logica',
    9 => 'php',
  ),
);
	}

	static function apex_nucleo_proto_propiedad()
	{
		return array (
  'archivo' => 'pgsql_a021_prototipacion.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'nucleo',
  'dump_where' => '(	nucleo_proyecto =	\\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nucleo_proyecto',
    1 => 'nucleo',
    2 => 'propiedad',
    3 => 'orden',
    4 => 'tipo',
    5 => 'descripcion',
  ),
);
	}

	static function apex_dimension_tipo()
	{
		return array (
  'archivo' => 'pgsql_a02_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'dimension_tipo',
  'zona' => 'dimension',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'dimension_tipo',
    2 => 'nombre',
    3 => 'descripcion',
    4 => 'parametros',
    5 => 'dimension_tipo_perfil',
    6 => 'editor_restric_id',
    7 => 'item_editor_restric_proyecto',
    8 => 'item_editor_restric',
    9 => 'ventana_editor_x',
    10 => 'ventana_editor_y',
    11 => 'exclusivo_toba',
  ),
);
	}

	static function apex_dimension_grupo()
	{
		return array (
  'archivo' => 'pgsql_a02_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'dimension_grupo',
  'zona' => 'dimension',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'dimension_grupo',
    2 => 'nombre',
    3 => 'descripcion',
    4 => 'orden',
  ),
);
	}

	static function apex_dimension()
	{
		return array (
  'archivo' => 'pgsql_a02_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'dimension',
  'zona' => 'dimension',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'dimension',
    2 => 'dimension_tipo_proyecto',
    3 => 'dimension_tipo',
    4 => 'dimension_grupo_proyecto',
    5 => 'dimension_grupo',
    6 => 'nombre',
    7 => 'descripcion',
    8 => 'inicializacion',
    9 => 'fuente_datos_proyecto',
    10 => 'fuente_datos',
    11 => 'tabla_ref',
    12 => 'tabla_ref_clave',
    13 => 'tabla_ref_desc',
    14 => 'tabla_restric',
  ),
);
	}

	static function apex_dimension_perfil_datos()
	{
		return array (
  'archivo' => 'pgsql_a02_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'usuario_perfil_datos, dimension',
  'dump_where' => '( usuario_perfil_datos_proyecto = \\\'%%\\\' )',
  'zona' => 'dimension dimension',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'usuario_perfil_datos_proyecto',
    1 => 'usuario_perfil_datos',
    2 => 'dimension_proyecto',
    3 => 'dimension',
    4 => 'comparacion',
    5 => 'valor_1',
    6 => 'valor_2',
    7 => 'valor_3',
    8 => 'valor_4',
    9 => 'valor_5',
  ),
);
	}

	static function apex_nota()
	{
		return array (
  'archivo' => 'pgsql_a04_notas.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'nota',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nota',
    1 => 'nota_tipo',
    2 => 'proyecto',
    3 => 'usuario_origen',
    4 => 'usuario_destino',
    5 => 'titulo',
    6 => 'texto',
    7 => 'leido',
    8 => 'bl',
    9 => 'creacion',
  ),
);
	}

	static function apex_patron_nota()
	{
		return array (
  'archivo' => 'pgsql_a04_notas.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'patron_nota',
  'dump_where' => '( patron_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'patron_nota',
    1 => 'nota_tipo',
    2 => 'patron_proyecto',
    3 => 'patron',
    4 => 'usuario_origen',
    5 => 'usuario_destino',
    6 => 'titulo',
    7 => 'texto',
    8 => 'leido',
    9 => 'bl',
    10 => 'creacion',
  ),
);
	}

	static function apex_item_nota()
	{
		return array (
  'archivo' => 'pgsql_a04_notas.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'item_nota',
  'dump_where' => '( item_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'item_nota',
    1 => 'nota_tipo',
    2 => 'item_id',
    3 => 'item_proyecto',
    4 => 'item',
    5 => 'usuario_origen',
    6 => 'usuario_destino',
    7 => 'titulo',
    8 => 'texto',
    9 => 'leido',
    10 => 'bl',
    11 => 'creacion',
  ),
);
	}

	static function apex_clase_nota()
	{
		return array (
  'archivo' => 'pgsql_a04_notas.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'clase_nota',
  'dump_where' => '( clase_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_nota',
    1 => 'nota_tipo',
    2 => 'clase_proyecto',
    3 => 'clase',
    4 => 'usuario_origen',
    5 => 'usuario_destino',
    6 => 'titulo',
    7 => 'texto',
    8 => 'bl',
    9 => 'leido',
    10 => 'creacion',
  ),
);
	}

	static function apex_objeto_nota()
	{
		return array (
  'archivo' => 'pgsql_a04_notas.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'objeto_nota',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_nota',
    1 => 'nota_tipo',
    2 => 'objeto_proyecto',
    3 => 'objeto',
    4 => 'usuario_origen',
    5 => 'usuario_destino',
    6 => 'titulo',
    7 => 'texto',
    8 => 'bl',
    9 => 'leido',
    10 => 'creacion',
  ),
);
	}

	static function apex_nucleo_nota()
	{
		return array (
  'archivo' => 'pgsql_a04_notas.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'nucleo_nota',
  'dump_where' => '( nucleo_proyecto = \\\'%%\\\' )',
  'zona' => 'nucleo',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nucleo_nota',
    1 => 'nota_tipo',
    2 => 'nucleo_proyecto',
    3 => 'nucleo',
    4 => 'usuario_origen',
    5 => 'usuario_destino',
    6 => 'titulo',
    7 => 'texto',
    8 => 'bl',
    9 => 'leido',
    10 => 'creacion',
  ),
);
	}

	static function apex_msg()
	{
		return array (
  'archivo' => 'pgsql_a05_mensajes.sql',
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

	static function apex_patron_msg()
	{
		return array (
  'archivo' => 'pgsql_a05_mensajes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'patron_msg',
  'dump_where' => '( patron_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'patron_msg',
    1 => 'msg_tipo',
    2 => 'indice',
    3 => 'patron_proyecto',
    4 => 'patron',
    5 => 'descripcion_corta',
    6 => 'mensaje_a',
    7 => 'mensaje_b',
    8 => 'mensaje_c',
    9 => 'mensaje_customizable',
  ),
);
	}

	static function apex_item_msg()
	{
		return array (
  'archivo' => 'pgsql_a05_mensajes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'item_msg',
  'dump_where' => '( item_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'item_msg',
    1 => 'msg_tipo',
    2 => 'indice',
    3 => 'item_id',
    4 => 'item_proyecto',
    5 => 'item',
    6 => 'descripcion_corta',
    7 => 'mensaje_a',
    8 => 'mensaje_b',
    9 => 'mensaje_c',
    10 => 'mensaje_customizable',
    11 => 'parametro_patron',
  ),
);
	}

	static function apex_clase_msg()
	{
		return array (
  'archivo' => 'pgsql_a05_mensajes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'clase_msg',
  'dump_where' => '( clase_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'clase_msg',
    1 => 'msg_tipo',
    2 => 'indice',
    3 => 'clase_proyecto',
    4 => 'clase',
    5 => 'descripcion_corta',
    6 => 'mensaje_a',
    7 => 'mensaje_b',
    8 => 'mensaje_c',
    9 => 'mensaje_customizable',
  ),
);
	}

	static function apex_objeto_msg()
	{
		return array (
  'archivo' => 'pgsql_a05_mensajes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'objeto_msg',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_msg',
    1 => 'msg_tipo',
    2 => 'indice',
    3 => 'objeto_proyecto',
    4 => 'objeto',
    5 => 'descripcion_corta',
    6 => 'mensaje_a',
    7 => 'mensaje_b',
    8 => 'mensaje_c',
    9 => 'mensaje_customizable',
    10 => 'parametro_clase',
  ),
);
	}

	static function apex_ap_version()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'version',
  'zona' => 'admin_proyectos',
  'desc' => 'Tabla de manejo de versiones',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'version',
    2 => 'descripcion',
    3 => 'fecha',
    4 => 'observaciones',
    5 => 'actual',
    6 => 'cerrada',
  ),
);
	}

	static function apex_ap_tarea()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'tarea',
  'zona' => 'admin_proyectos',
  'desc' => 'Estados de Tarea',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'tarea',
    2 => 'tarea_tipo',
    3 => 'tarea_estado',
    4 => 'tarea_prioridad',
    5 => 'tarea_tema',
    6 => 'descripcion',
    7 => 'version_proyecto',
    8 => 'version',
    9 => 'grado_avance',
  ),
);
	}

	static function apex_tp_tarea()
	{
		return array (
  'archivo' => 'pgsql_a08_tareas_programadas.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'tarea',
  'zona' => 'admin_proyectos',
  'desc' => 'Tabla de manejo de versiones',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'tarea',
    2 => 'item_id',
    3 => 'item_proyecto',
    4 => 'item',
    5 => 'activada',
    6 => 'descripcion',
    7 => 'tarea_tipo',
    8 => 'fecha',
    9 => 'hora',
  ),
);
	}

	static function apex_objeto_mapa()
	{
		return array (
  'archivo' => 'pgsql_a17_clase_mapa.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'objeto_mapa',
  'dump_where' => '( objeto_mapa_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_mapa_proyecto',
    1 => 'objeto_mapa',
    2 => 'sql',
    3 => 'descripcion',
  ),
);
	}

	static function apex_permiso()
	{
		return array (
  'archivo' => 'pgsql_a22_permisos.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'permiso',
  'zona' => 'general',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'permiso',
    1 => 'proyecto',
    2 => 'nombre',
    3 => 'descripcion',
    4 => 'mensaje_particular',
  ),
);
	}

	static function apex_permiso_grupo_acc()
	{
		return array (
  'archivo' => 'pgsql_a22_permisos.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'permiso, usuario_grupo_acc',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario_grupo_acc',
    2 => 'permiso',
  ),
);
	}

}
?>