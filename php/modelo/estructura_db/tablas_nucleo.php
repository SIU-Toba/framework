<?

class tablas_nucleo
{
	static function get_lista()
	{
		return array (
  0 => 'apex_elemento_infra',
  1 => 'apex_elemento_infra_tabla',
  2 => 'apex_elemento_infra_input',
  3 => 'apex_estilo_paleta',
  4 => 'apex_estilo',
  5 => 'apex_menu',
  6 => 'apex_log_sistema_tipo',
  7 => 'apex_fuente_datos_motor',
  8 => 'apex_grafico',
  9 => 'apex_recurso_origen',
  10 => 'apex_repositorio',
  11 => 'apex_nivel_acceso',
  12 => 'apex_nivel_ejecucion',
  13 => 'apex_solicitud_tipo',
  14 => 'apex_columna_estilo',
  15 => 'apex_columna_formato',
  16 => 'apex_columna_proceso',
  17 => 'apex_pdf_propiedad',
  18 => 'apex_usuario_tipodoc',
  19 => 'apex_clase_tipo',
  20 => 'apex_vinculo_tipo',
  21 => 'apex_nucleo_tipo',
  22 => 'apex_dimension_tipo_perfil',
  23 => 'apex_comparacion',
  24 => 'apex_nota_tipo',
  25 => 'apex_msg_tipo',
  26 => 'apex_ap_tarea_tipo',
  27 => 'apex_ap_tarea_estado',
  28 => 'apex_ap_tarea_prioridad',
  29 => 'apex_ap_tarea_tema',
  30 => 'apex_tp_tarea_tipo',
  31 => 'apex_objeto_hoja_directiva_ti',
  32 => 'apex_admin_persistencia',
  33 => 'apex_tipo_datos',
  34 => 'apex_objeto_mt_me_tipo_nav',
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
  5 => 'apex_patron',
  6 => 'apex_patron_info',
  7 => 'apex_buffer',
  8 => 'apex_clase',
  9 => 'apex_clase_info',
  10 => 'apex_clase_dependencias',
  11 => 'apex_patron_dependencias',
  12 => 'apex_solicitud_obj_obs_tipo',
  13 => 'apex_dimension_tipo',
  14 => 'apex_msg',
  15 => 'apex_clase_msg',
);
	}

	static function apex_elemento_infra()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'elemento_infra',
  'zona' => 'general',
  'desc' => 'Representa	un	elemento	de	la	infraestructura',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'elemento_infra',
    1 => 'descripcion',
  ),
);
	}

	static function apex_elemento_infra_tabla()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'elemento_infra, tabla, columna_clave_proyecto',
  'zona' => 'general',
  'desc' => 'Representa	una tabla donde se almacena parte del elemento',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'elemento_infra',
    1 => 'tabla',
    2 => 'columna_clave_proyecto',
    3 => 'columna_clave',
    4 => 'orden',
    5 => 'descripcion',
    6 => 'dependiente',
    7 => 'proc_borrar',
    8 => 'proc_exportar',
    9 => 'proc_clonar',
    10 => 'obligatoria',
  ),
);
	}

	static function apex_elemento_infra_input()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'entrada',
  'zona' => 'general',
  'desc' => 'En esta tabla se guardan los elementos toba recibidos desde otras instancias',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'entrada',
    1 => 'elemento_infra',
    2 => 'descripcion',
    3 => 'ip_origen',
    4 => 'ip_destino',
    5 => 'datos',
    6 => 'datos2_test',
    7 => 'ingreso',
  ),
);
	}

	static function apex_estilo_paleta()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'estilo_paleta',
  'zona' => 'general',
  'desc' => 'Representa	una serie de colores',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'estilo_paleta',
    1 => 'color_1',
    2 => 'color_2',
    3 => 'color_3',
    4 => 'color_4',
    5 => 'color_5',
    6 => 'color_6',
  ),
);
	}

	static function apex_estilo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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
  'archivo' => 'pgsql_a01_nucleo.sql',
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
  ),
);
	}

	static function apex_log_sistema_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_grafico()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_repositorio()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'repositorio',
  'zona' => 'general',
  'desc' => 'Listado	de	repositorios a	los que me puedo conectar',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'repositorio',
    1 => 'descripcion',
  ),
);
	}

	static function apex_nivel_acceso()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_nivel_ejecucion()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'nivel_ejecucion',
  'zona' => 'general',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nivel_ejecucion',
    1 => 'descripcion',
  ),
);
	}

	static function apex_solicitud_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_columna_estilo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_columna_proceso()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'columna_proceso',
  'zona' => 'general',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'columna_proceso',
    1 => 'funcion',
    2 => 'archivo',
    3 => 'descripcion',
    4 => 'descripcion_corta',
    5 => 'parametros',
  ),
);
	}

	static function apex_pdf_propiedad()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'pdf_propiedad',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'pdf_propiedad',
    1 => 'descripcion',
    2 => 'requerido',
    3 => 'proyecto',
    4 => 'exclusiva_columna',
    5 => 'exclusiva_tabla',
  ),
);
	}

	static function apex_usuario_tipodoc()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_clase_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
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

	static function apex_vinculo_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'vinculo_tipo',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'vinculo_tipo',
    1 => 'descripcion_corta',
    2 => 'descripcion',
  ),
);
	}

	static function apex_nucleo_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'nucleo_tipo',
  'zona' => 'nucleo',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'nucleo_tipo',
    1 => 'descripcion_corta',
    2 => 'descripcion',
    3 => 'orden',
  ),
);
	}

	static function apex_dimension_tipo_perfil()
	{
		return array (
  'archivo' => 'pgsql_a02_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'dimension_tipo_perfil',
  'zona' => 'dimension',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'dimension_tipo_perfil',
    1 => 'descripcion',
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

	static function apex_comparacion()
	{
		return array (
  'archivo' => 'pgsql_a02_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'comparacion',
  'zona' => 'dimension',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'comparacion',
    1 => 'descripcion',
    2 => 'plan_sql',
    3 => 'valor_1_des',
    4 => 'valor_2_des',
    5 => 'valor_3_des',
    6 => 'valor_4_des',
    7 => 'valor_5_des',
  ),
);
	}

	static function apex_nota_tipo()
	{
		return array (
  'archivo' => 'pgsql_a04_notas.sql',
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
  'archivo' => 'pgsql_a05_mensajes.sql',
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

	static function apex_ap_tarea_tipo()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tarea_tipo',
  'zona' => 'admin_proyectos',
  'desc' => 'Tipos de tarea',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tarea_tipo',
    1 => 'descripcion',
  ),
);
	}

	static function apex_ap_tarea_estado()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tarea_estado',
  'zona' => 'admin_proyectos',
  'desc' => 'Estados de Tarea',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tarea_estado',
    1 => 'descripcion',
  ),
);
	}

	static function apex_ap_tarea_prioridad()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tarea_prioridad',
  'zona' => 'admin_proyectos',
  'desc' => 'Prioridad de Tarea',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tarea_prioridad',
    1 => 'descripcion',
  ),
);
	}

	static function apex_ap_tarea_tema()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tarea_tema',
  'zona' => 'admin_proyectos',
  'desc' => 'Tipos de tarea',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tarea_tema',
    1 => 'descripcion',
  ),
);
	}

	static function apex_tp_tarea_tipo()
	{
		return array (
  'archivo' => 'pgsql_a08_tareas_programadas.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tarea_tipo',
  'zona' => 'admin_proyectos',
  'desc' => 'Tipos de tarea',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tarea_tipo',
    1 => 'descripcion',
  ),
);
	}

	static function apex_objeto_hoja_directiva_ti()
	{
		return array (
  'archivo' => 'pgsql_a10_clase_hoja.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'objeto_hoja_directiva_tipo',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_hoja_directiva_tipo',
    1 => 'nombre',
    2 => 'descripcion',
  ),
);
	}

	static function apex_admin_persistencia()
	{
		return array (
  'archivo' => 'pgsql_a21_clase_db_registros.sql',
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
  'archivo' => 'pgsql_a21_clase_db_registros.sql',
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

	static function apex_objeto_mt_me_tipo_nav()
	{
		return array (
  'archivo' => 'pgsql_a52_clase_mt_me.sql',
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

}
?>