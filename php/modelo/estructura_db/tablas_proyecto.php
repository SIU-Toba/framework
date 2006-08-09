<?

class tablas_proyecto
{
	static function get_lista()
	{
		return array (
  0 => 'apex_permiso',
  1 => 'apex_permiso_grupo_acc',
  2 => 'apex_proyecto',
  3 => 'apex_fuente_datos',
  4 => 'apex_elemento_formulario',
  5 => 'apex_solicitud_obs_tipo',
  6 => 'apex_pagina_tipo',
  7 => 'apex_usuario_perfil_datos',
  8 => 'apex_usuario_grupo_acc',
  9 => 'apex_patron',
  10 => 'apex_patron_info',
  11 => 'apex_buffer',
  12 => 'apex_item_zona',
  13 => 'apex_clase',
  14 => 'apex_clase_info',
  15 => 'apex_clase_dependencias',
  16 => 'apex_patron_dependencias',
  17 => 'apex_objeto_categoria',
  18 => 'apex_solicitud_obj_obs_tipo',
  19 => 'apex_vinculo',
  20 => 'apex_usuario_grupo_acc_item',
  21 => 'apex_nucleo',
  22 => 'apex_nucleo_info',
  23 => 'apex_conversion',
  24 => 'apex_nota',
  25 => 'apex_patron_nota',
  26 => 'apex_item_nota',
  27 => 'apex_clase_nota',
  28 => 'apex_objeto_nota',
  29 => 'apex_nucleo_nota',
  30 => 'apex_msg',
  31 => 'apex_patron_msg',
  32 => 'apex_item_msg',
  33 => 'apex_clase_msg',
  34 => 'apex_objeto_msg',
);
	}

	static function apex_permiso()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_permisos.sql',
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
  'archivo' => 'pgsql_a05_tablas_permisos.sql',
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

	static function apex_usuario_perfil_datos()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
    6 => 'consulta_archivo',
    7 => 'consulta_clase',
    8 => 'consulta_metodo',
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

	static function apex_clase_info()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
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

	static function apex_nota()
	{
		return array (
  'archivo' => 'pgsql_a04_tablas_notas.sql',
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
  'archivo' => 'pgsql_a04_tablas_notas.sql',
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
  'archivo' => 'pgsql_a04_tablas_notas.sql',
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
  'archivo' => 'pgsql_a04_tablas_notas.sql',
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
  'archivo' => 'pgsql_a04_tablas_notas.sql',
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
  'archivo' => 'pgsql_a04_tablas_notas.sql',
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

	static function apex_patron_msg()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_mensajes.sql',
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
  'archivo' => 'pgsql_a05_tablas_mensajes.sql',
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
  'archivo' => 'pgsql_a05_tablas_mensajes.sql',
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
  'archivo' => 'pgsql_a05_tablas_mensajes.sql',
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

}
?>