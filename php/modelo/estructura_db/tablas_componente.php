<?

class tablas_componente
{
	static function get_lista()
	{
		return array (
  0 => 'apex_item',
  1 => 'apex_item_info',
  2 => 'apex_objeto',
  3 => 'apex_objeto_info',
  4 => 'apex_objeto_dependencias',
  5 => 'apex_objeto_eventos',
  6 => 'apex_item_objeto',
  7 => 'apex_objeto_hoja',
  8 => 'apex_objeto_hoja_directiva',
  9 => 'apex_objeto_filtro',
  10 => 'apex_objeto_lista',
  11 => 'apex_objeto_grafico',
  12 => 'apex_objeto_cuadro',
  13 => 'apex_objeto_cuadro_columna',
  14 => 'apex_objeto_cuadro_cc',
  15 => 'apex_objeto_ei_cuadro_columna',
  16 => 'apex_objeto_plan',
  17 => 'apex_objeto_plan_activ',
  18 => 'apex_objeto_plan_activ_usu',
  19 => 'apex_objeto_plan_hito',
  20 => 'apex_objeto_plan_linea',
  21 => 'apex_objeto_db_registros',
  22 => 'apex_objeto_db_registros_col',
  23 => 'apex_objeto_datos_rel',
  24 => 'apex_objeto_datos_rel_asoc',
  25 => 'apex_objeto_ut_formulario',
  26 => 'apex_objeto_ut_formulario_ef',
  27 => 'apex_objeto_ei_formulario_ef',
  28 => 'apex_objeto_mt_me',
  29 => 'apex_objeto_mt_me_etapa',
  30 => 'apex_objeto_ci_pantalla',
  31 => 'apex_objeto_negocio',
  32 => 'apex_objeto_negocio_regla',
  33 => 'apex_objeto_esquema',
  34 => 'apex_objeto_html',
);
	}

	static function apex_item()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'item',
  'dump_order_by' => 'item',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'item_id',
    1 => 'proyecto',
    2 => 'item',
    3 => 'padre_id',
    4 => 'padre_proyecto',
    5 => 'padre',
    6 => 'carpeta',
    7 => 'nivel_acceso',
    8 => 'solicitud_tipo',
    9 => 'pagina_tipo_proyecto',
    10 => 'pagina_tipo',
    11 => 'nombre',
    12 => 'descripcion',
    13 => 'actividad_buffer_proyecto',
    14 => 'actividad_buffer',
    15 => 'actividad_patron_proyecto',
    16 => 'actividad_patron',
    17 => 'actividad_accion',
    18 => 'menu',
    19 => 'orden',
    20 => 'solicitud_registrar',
    21 => 'solicitud_obs_tipo_proyecto',
    22 => 'solicitud_obs_tipo',
    23 => 'solicitud_observacion',
    24 => 'solicitud_registrar_cron',
    25 => 'prueba_directorios',
    26 => 'zona_proyecto',
    27 => 'zona',
    28 => 'zona_orden',
    29 => 'zona_listar',
    30 => 'imagen_recurso_origen',
    31 => 'imagen',
    32 => 'parametro_a',
    33 => 'parametro_b',
    34 => 'parametro_c',
    35 => 'publico',
    36 => 'redirecciona',
    37 => 'usuario',
    38 => 'creacion',
  ),
);
	}

	static function apex_item_info()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'item_proyecto',
  'dump_clave_componente' => 'item',
  'dump_order_by' => 'item',
  'dump_where' => '(	item_proyecto = \\\'%%\\\'	)',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'item_id',
    1 => 'item_proyecto',
    2 => 'item',
    3 => 'descripcion_breve',
    4 => 'descripcion_larga',
  ),
);
	}

	static function apex_objeto()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'objeto',
    2 => 'anterior',
    3 => 'reflexivo',
    4 => 'clase_proyecto',
    5 => 'clase',
    6 => 'subclase',
    7 => 'subclase_archivo',
    8 => 'objeto_categoria_proyecto',
    9 => 'objeto_categoria',
    10 => 'nombre',
    11 => 'titulo',
    12 => 'colapsable',
    13 => 'descripcion',
    14 => 'fuente_datos_proyecto',
    15 => 'fuente_datos',
    16 => 'solicitud_registrar',
    17 => 'solicitud_obj_obs_tipo',
    18 => 'solicitud_obj_observacion',
    19 => 'parametro_a',
    20 => 'parametro_b',
    21 => 'parametro_c',
    22 => 'parametro_d',
    23 => 'parametro_e',
    24 => 'parametro_f',
    25 => 'usuario',
    26 => 'creacion',
  ),
);
	}

	static function apex_objeto_info()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'descripcion_breve',
    3 => 'descripcion_larga',
  ),
);
	}

	static function apex_objeto_dependencias()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto_consumidor',
  'dump_order_by' => 'objeto_consumidor, identificador',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'dep_id',
    2 => 'objeto_consumidor',
    3 => 'objeto_proveedor',
    4 => 'identificador',
    5 => 'parametros_a',
    6 => 'parametros_b',
    7 => 'parametros_c',
    8 => 'inicializar',
    9 => 'orden',
  ),
);
	}

	static function apex_objeto_eventos()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto, orden, identificador',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'evento_id',
    2 => 'objeto',
    3 => 'identificador',
    4 => 'etiqueta',
    5 => 'maneja_datos',
    6 => 'sobre_fila',
    7 => 'confirmacion',
    8 => 'estilo',
    9 => 'imagen_recurso_origen',
    10 => 'imagen',
    11 => 'en_botonera',
    12 => 'ayuda',
    13 => 'orden',
    14 => 'ci_predep',
    15 => 'implicito',
    16 => 'display_datos_cargados',
    17 => 'grupo',
    18 => 'accion',
    19 => 'accion_imphtml_debug',
    20 => 'accion_vinculo_carpeta',
    21 => 'accion_vinculo_item',
    22 => 'accion_vinculo_objeto',
    23 => 'accion_vinculo_popup',
    24 => 'accion_vinculo_popup_param',
    25 => 'accion_vinculo_target',
    26 => 'accion_vinculo_celda',
  ),
);
	}

	static function apex_item_objeto()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'item',
  'dump_order_by' => 'item, objeto',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'item_id',
    1 => 'proyecto',
    2 => 'item',
    3 => 'objeto',
    4 => 'orden',
    5 => 'inicializar',
  ),
);
	}

	static function apex_objeto_hoja()
	{
		return array (
  'archivo' => 'pgsql_a10_clase_hoja.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_hoja_proyecto',
  'dump_clave_componente' => 'objeto_hoja',
  'dump_order_by' => 'objeto_hoja',
  'dump_where' => '( objeto_hoja_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_hoja_proyecto',
    1 => 'objeto_hoja',
    2 => 'sql',
    3 => 'ancho',
    4 => 'total_y',
    5 => 'total_x',
    6 => 'total_x_formato',
    7 => 'columna_entrada',
    8 => 'ordenable',
    9 => 'grafico',
    10 => 'graf_columnas',
    11 => 'graf_filas',
    12 => 'graf_gen_invertir',
    13 => 'graf_gen_invertible',
    14 => 'graf_gen_ancho',
    15 => 'graf_gen_alto',
  ),
);
	}

	static function apex_objeto_hoja_directiva()
	{
		return array (
  'archivo' => 'pgsql_a10_clase_hoja.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_hoja_proyecto',
  'dump_clave_componente' => 'objeto_hoja',
  'dump_order_by' => 'objeto_hoja, columna',
  'dump_where' => '( objeto_hoja_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_hoja_proyecto',
    1 => 'objeto_hoja',
    2 => 'columna',
    3 => 'objeto_hoja_directiva_tipo',
    4 => 'nombre',
    5 => 'columna_formato',
    6 => 'columna_estilo',
    7 => 'par_dimension_proyecto',
    8 => 'par_dimension',
    9 => 'par_tabla',
    10 => 'par_columna',
  ),
);
	}

	static function apex_objeto_filtro()
	{
		return array (
  'archivo' => 'pgsql_a11_clase_filtro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_filtro_proyecto',
  'dump_clave_componente' => 'objeto_filtro',
  'dump_order_by' => 'objeto_filtro',
  'dump_where' => '( objeto_filtro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_filtro_proyecto',
    1 => 'objeto_filtro',
    2 => 'dimension_proyecto',
    3 => 'dimension',
    4 => 'etiqueta',
    5 => 'tabla',
    6 => 'columna',
    7 => 'orden',
    8 => 'requerido',
    9 => 'no_interactivo',
    10 => 'predeterminado',
  ),
);
	}

	static function apex_objeto_lista()
	{
		return array (
  'archivo' => 'pgsql_a14_clase_lista.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_lista_proyecto',
  'dump_clave_componente' => 'objeto_lista',
  'dump_order_by' => 'objeto_lista',
  'dump_where' => '( objeto_lista_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_lista_proyecto',
    1 => 'objeto_lista',
    2 => 'titulo',
    3 => 'subtitulo',
    4 => 'sql',
    5 => 'col_ver',
    6 => 'col_titulos',
    7 => 'col_formato',
    8 => 'ancho',
    9 => 'ordenar',
    10 => 'exportar',
    11 => 'vinculo_clave',
    12 => 'vinculo_indice',
  ),
);
	}

	static function apex_objeto_grafico()
	{
		return array (
  'archivo' => 'pgsql_a15_clase_grafico.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_grafico_proyecto',
  'dump_clave_componente' => 'objeto_grafico',
  'dump_order_by' => 'objeto_grafico',
  'dump_where' => '( objeto_grafico_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_grafico_proyecto',
    1 => 'objeto_grafico',
    2 => 'grafico',
    3 => 'sql',
    4 => 'inicializacion',
  ),
);
	}

	static function apex_objeto_cuadro()
	{
		return array (
  'archivo' => 'pgsql_a16_clase_cuadro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_cuadro_proyecto',
  'dump_clave_componente' => 'objeto_cuadro',
  'dump_order_by' => 'objeto_cuadro',
  'dump_where' => '( objeto_cuadro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_cuadro_proyecto',
    1 => 'objeto_cuadro',
    2 => 'titulo',
    3 => 'subtitulo',
    4 => 'sql',
    5 => 'columnas_clave',
    6 => 'clave_dbr',
    7 => 'archivos_callbacks',
    8 => 'ancho',
    9 => 'ordenar',
    10 => 'paginar',
    11 => 'tamano_pagina',
    12 => 'tipo_paginado',
    13 => 'eof_invisible',
    14 => 'eof_customizado',
    15 => 'exportar',
    16 => 'exportar_rtf',
    17 => 'pdf_propiedades',
    18 => 'pdf_respetar_paginacion',
    19 => 'asociacion_columnas',
    20 => 'ev_seleccion',
    21 => 'ev_eliminar',
    22 => 'dao_nucleo_proyecto',
    23 => 'dao_nucleo',
    24 => 'dao_metodo',
    25 => 'dao_parametros',
    26 => 'desplegable',
    27 => 'desplegable_activo',
    28 => 'scroll',
    29 => 'scroll_alto',
    30 => 'cc_modo',
    31 => 'cc_modo_anidado_colap',
    32 => 'cc_modo_anidado_totcol',
    33 => 'cc_modo_anidado_totcua',
  ),
);
	}

	static function apex_objeto_cuadro_columna()
	{
		return array (
  'archivo' => 'pgsql_a16_clase_cuadro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_cuadro_proyecto',
  'dump_clave_componente' => 'objeto_cuadro',
  'dump_order_by' => 'objeto_cuadro, orden',
  'dump_where' => '( objeto_cuadro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_cuadro_proyecto',
    1 => 'objeto_cuadro',
    2 => 'orden',
    3 => 'titulo',
    4 => 'columna_estilo',
    5 => 'columna_ancho',
    6 => 'ancho_html',
    7 => 'total',
    8 => 'total_cc',
    9 => 'valor_sql',
    10 => 'valor_sql_formato',
    11 => 'valor_fijo',
    12 => 'valor_proceso',
    13 => 'valor_proceso_esp',
    14 => 'valor_proceso_parametros',
    15 => 'vinculo_indice',
    16 => 'par_dimension_proyecto',
    17 => 'par_dimension',
    18 => 'par_tabla',
    19 => 'par_columna',
    20 => 'no_ordenar',
    21 => 'mostrar_xls',
    22 => 'mostrar_pdf',
    23 => 'pdf_propiedades',
    24 => 'desabilitado',
  ),
);
	}

	static function apex_objeto_cuadro_cc()
	{
		return array (
  'archivo' => 'pgsql_a16_clase_cuadro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_cuadro_proyecto',
  'dump_clave_componente' => 'objeto_cuadro',
  'dump_order_by' => 'objeto_cuadro, objeto_cuadro_cc',
  'dump_where' => '( objeto_cuadro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_cuadro_proyecto',
    1 => 'objeto_cuadro',
    2 => 'objeto_cuadro_cc',
    3 => 'identificador',
    4 => 'descripcion',
    5 => 'orden',
    6 => 'columnas_id',
    7 => 'columnas_descripcion',
    8 => 'pie_contar_filas',
    9 => 'pie_mostrar_titular',
    10 => 'pie_mostrar_titulos',
    11 => 'imp_paginar',
  ),
);
	}

	static function apex_objeto_ei_cuadro_columna()
	{
		return array (
  'archivo' => 'pgsql_a16_clase_cuadro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_cuadro_proyecto',
  'dump_clave_componente' => 'objeto_cuadro',
  'dump_order_by' => 'objeto_cuadro, objeto_cuadro_col',
  'dump_where' => '( objeto_cuadro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_cuadro_proyecto',
    1 => 'objeto_cuadro',
    2 => 'objeto_cuadro_col',
    3 => 'clave',
    4 => 'orden',
    5 => 'titulo',
    6 => 'estilo_titulo',
    7 => 'estilo',
    8 => 'ancho',
    9 => 'formateo',
    10 => 'vinculo_indice',
    11 => 'no_ordenar',
    12 => 'mostrar_xls',
    13 => 'mostrar_pdf',
    14 => 'pdf_propiedades',
    15 => 'desabilitado',
    16 => 'total',
    17 => 'total_cc',
  ),
);
	}

	static function apex_objeto_plan()
	{
		return array (
  'archivo' => 'pgsql_a20_clase_plan.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_plan_proyecto',
  'dump_clave_componente' => 'objeto_plan',
  'dump_order_by' => 'objeto_plan',
  'dump_where' => '( objeto_plan_proyecto = \\\'%%\\\' )',
  'zona' => 'plan',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_plan_proyecto',
    1 => 'objeto_plan',
    2 => 'descripcion',
  ),
);
	}

	static function apex_objeto_plan_activ()
	{
		return array (
  'archivo' => 'pgsql_a20_clase_plan.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_plan_proyecto',
  'dump_clave_componente' => 'objeto_plan',
  'dump_order_by' => 'objeto_plan, posicion',
  'dump_where' => '( objeto_plan_proyecto = \\\'%%\\\' )',
  'zona' => 'plan',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_plan_proyecto',
    1 => 'objeto_plan',
    2 => 'posicion',
    3 => 'descripcion_corta',
    4 => 'descripcion',
    5 => 'fecha_inicio',
    6 => 'fecha_fin',
    7 => 'duracion',
    8 => 'anotacion',
    9 => 'altura',
  ),
);
	}

	static function apex_objeto_plan_activ_usu()
	{
		return array (
  'archivo' => 'pgsql_a20_clase_plan.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_plan_proyecto',
  'dump_clave_componente' => 'objeto_plan',
  'dump_order_by' => 'objeto_plan, posicion',
  'dump_where' => '( objeto_plan_proyecto = \\\'%%\\\' )',
  'zona' => 'plan',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_plan_proyecto',
    1 => 'objeto_plan',
    2 => 'posicion',
    3 => 'usuario',
    4 => 'observaciones',
  ),
);
	}

	static function apex_objeto_plan_hito()
	{
		return array (
  'archivo' => 'pgsql_a20_clase_plan.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_plan_proyecto',
  'dump_clave_componente' => 'objeto_plan',
  'dump_order_by' => 'objeto_plan, posicion',
  'dump_where' => '( objeto_plan_proyecto = \\\'%%\\\' )',
  'zona' => 'plan',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_plan_proyecto',
    1 => 'objeto_plan',
    2 => 'posicion',
    3 => 'descripcion_corta',
    4 => 'descripcion',
    5 => 'fecha',
    6 => 'anotacion',
  ),
);
	}

	static function apex_objeto_plan_linea()
	{
		return array (
  'archivo' => 'pgsql_a20_clase_plan.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_plan_proyecto',
  'dump_clave_componente' => 'objeto_plan',
  'dump_order_by' => 'objeto_plan, linea',
  'dump_where' => '( objeto_plan_proyecto = \\\'%%\\\' )',
  'zona' => 'plan',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_plan_proyecto',
    1 => 'objeto_plan',
    2 => 'linea',
    3 => 'descripcion_corta',
    4 => 'descripcion',
    5 => 'fecha',
    6 => 'color',
    7 => 'ancho',
    8 => 'estilo',
  ),
);
	}

	static function apex_objeto_db_registros()
	{
		return array (
  'archivo' => 'pgsql_a21_clase_db_registros.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'max_registros',
    3 => 'min_registros',
    4 => 'ap',
    5 => 'ap_clase',
    6 => 'ap_archivo',
    7 => 'tabla',
    8 => 'alias',
    9 => 'modificar_claves',
  ),
);
	}

	static function apex_objeto_db_registros_col()
	{
		return array (
  'archivo' => 'pgsql_a21_clase_db_registros.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto, col_id',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'col_id',
    3 => 'columna',
    4 => 'tipo',
    5 => 'pk',
    6 => 'secuencia',
    7 => 'largo',
    8 => 'no_nulo',
    9 => 'no_nulo_db',
    10 => 'externa',
  ),
);
	}

	static function apex_objeto_datos_rel()
	{
		return array (
  'archivo' => 'pgsql_a21_clase_db_registros.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'objeto',
    2 => 'debug',
    3 => 'clave',
    4 => 'ap',
    5 => 'ap_clase',
    6 => 'ap_archivo',
    7 => 'sinc_susp_constraints',
    8 => 'sinc_orden_automatico',
  ),
);
	}

	static function apex_objeto_datos_rel_asoc()
	{
		return array (
  'archivo' => 'pgsql_a21_clase_db_registros.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto, asoc_id',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'objeto',
    2 => 'asoc_id',
    3 => 'identificador',
    4 => 'padre_proyecto',
    5 => 'padre_objeto',
    6 => 'padre_id',
    7 => 'padre_clave',
    8 => 'hijo_proyecto',
    9 => 'hijo_objeto',
    10 => 'hijo_id',
    11 => 'hijo_clave',
    12 => 'cascada',
    13 => 'orden',
  ),
);
	}

	static function apex_objeto_ut_formulario()
	{
		return array (
  'archivo' => 'pgsql_a50_clase_ut_formulario.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_ut_formulario_proyecto',
  'dump_clave_componente' => 'objeto_ut_formulario',
  'dump_order_by' => 'objeto_ut_formulario',
  'dump_where' => '( objeto_ut_formulario_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_ut_formulario_proyecto',
    1 => 'objeto_ut_formulario',
    2 => 'tabla',
    3 => 'titulo',
    4 => 'ev_agregar',
    5 => 'ev_agregar_etiq',
    6 => 'ev_mod_modificar',
    7 => 'ev_mod_modificar_etiq',
    8 => 'ev_mod_eliminar',
    9 => 'ev_mod_eliminar_etiq',
    10 => 'ev_mod_limpiar',
    11 => 'ev_mod_limpiar_etiq',
    12 => 'ev_mod_clave',
    13 => 'clase_proyecto',
    14 => 'clase',
    15 => 'auto_reset',
    16 => 'ancho',
    17 => 'ancho_etiqueta',
    18 => 'campo_bl',
    19 => 'scroll',
    20 => 'filas',
    21 => 'filas_agregar',
    22 => 'filas_agregar_online',
    23 => 'filas_undo',
    24 => 'filas_ordenar',
    25 => 'columna_orden',
    26 => 'filas_numerar',
    27 => 'ev_seleccion',
    28 => 'alto',
    29 => 'analisis_cambios',
  ),
);
	}

	static function apex_objeto_ut_formulario_ef()
	{
		return array (
  'archivo' => 'pgsql_a50_clase_ut_formulario.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_ut_formulario_proyecto',
  'dump_clave_componente' => 'objeto_ut_formulario',
  'dump_order_by' => 'objeto_ut_formulario, identificador',
  'dump_where' => '( objeto_ut_formulario_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_ut_formulario_proyecto',
    1 => 'objeto_ut_formulario',
    2 => 'identificador',
    3 => 'columnas',
    4 => 'clave_primaria',
    5 => 'obligatorio',
    6 => 'elemento_formulario',
    7 => 'inicializacion',
    8 => 'orden',
    9 => 'etiqueta',
    10 => 'descripcion',
    11 => 'colapsado',
    12 => 'desactivado',
    13 => 'no_sql',
    14 => 'total',
    15 => 'clave_primaria_padre',
    16 => 'listar',
    17 => 'lista_cabecera',
    18 => 'lista_orden',
    19 => 'lista_columna_estilo',
    20 => 'lista_valor_sql',
    21 => 'lista_valor_sql_formato',
    22 => 'lista_valor_sql_esp',
    23 => 'lista_ancho',
  ),
);
	}

	static function apex_objeto_ei_formulario_ef()
	{
		return array (
  'archivo' => 'pgsql_a50_clase_ut_formulario.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_ei_formulario_proyecto',
  'dump_clave_componente' => 'objeto_ei_formulario',
  'dump_order_by' => 'objeto_ei_formulario, objeto_ei_formulario_fila',
  'dump_where' => '( objeto_ei_formulario_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_ei_formulario_proyecto',
    1 => 'objeto_ei_formulario',
    2 => 'objeto_ei_formulario_fila',
    3 => 'identificador',
    4 => 'elemento_formulario',
    5 => 'columnas',
    6 => 'obligatorio',
    7 => 'inicializacion',
    8 => 'orden',
    9 => 'etiqueta',
    10 => 'etiqueta_estilo',
    11 => 'descripcion',
    12 => 'colapsado',
    13 => 'desactivado',
    14 => 'estilo',
    15 => 'total',
  ),
);
	}

	static function apex_objeto_mt_me()
	{
		return array (
  'archivo' => 'pgsql_a52_clase_mt_me.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_mt_me_proyecto',
  'dump_clave_componente' => 'objeto_mt_me',
  'dump_order_by' => 'objeto_mt_me',
  'dump_where' => '(	objeto_mt_me_proyecto =	\\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_mt_me_proyecto',
    1 => 'objeto_mt_me',
    2 => 'ev_procesar_etiq',
    3 => 'ev_cancelar_etiq',
    4 => 'ancho',
    5 => 'alto',
    6 => 'posicion_botonera',
    7 => 'tipo_navegacion',
    8 => 'con_toc',
    9 => 'incremental',
    10 => 'debug_eventos',
    11 => 'activacion_procesar',
    12 => 'activacion_cancelar',
    13 => 'ev_procesar',
    14 => 'ev_cancelar',
    15 => 'objetos',
    16 => 'post_procesar',
    17 => 'metodo_despachador',
    18 => 'metodo_opciones',
  ),
);
	}

	static function apex_objeto_mt_me_etapa()
	{
		return array (
  'archivo' => 'pgsql_a52_clase_mt_me.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_mt_me_proyecto',
  'dump_clave_componente' => 'objeto_mt_me',
  'dump_order_by' => 'objeto_mt_me,	posicion',
  'dump_where' => '(	objeto_mt_me_proyecto =	\\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_mt_me_proyecto',
    1 => 'objeto_mt_me',
    2 => 'posicion',
    3 => 'orden',
    4 => 'etiqueta',
    5 => 'descripcion',
    6 => 'tip',
    7 => 'imagen_recurso_origen',
    8 => 'imagen',
    9 => 'objetos',
    10 => 'objetos_adhoc',
    11 => 'pre_condicion',
    12 => 'post_condicion',
    13 => 'gen_interface_pre',
    14 => 'gen_interface_post',
    15 => 'ev_procesar',
    16 => 'ev_cancelar',
  ),
);
	}

	static function apex_objeto_ci_pantalla()
	{
		return array (
  'archivo' => 'pgsql_a52_clase_mt_me.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_ci_proyecto',
  'dump_clave_componente' => 'objeto_ci',
  'dump_order_by' => 'objeto_ci_proyecto, objeto_ci, pantalla',
  'dump_where' => '(	objeto_ci_proyecto =	\\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_ci_proyecto',
    1 => 'objeto_ci',
    2 => 'pantalla',
    3 => 'identificador',
    4 => 'orden',
    5 => 'etiqueta',
    6 => 'descripcion',
    7 => 'tip',
    8 => 'imagen_recurso_origen',
    9 => 'imagen',
    10 => 'objetos',
    11 => 'eventos',
  ),
);
	}

	static function apex_objeto_negocio()
	{
		return array (
  'archivo' => 'pgsql_a53_clase_negocio.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_negocio_proyecto',
  'dump_clave_componente' => 'objeto_negocio',
  'dump_order_by' => 'objeto_negocio',
  'dump_where' => '( objeto_negocio_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_negocio_proyecto',
    1 => 'objeto_negocio',
    2 => 'descripcion',
  ),
);
	}

	static function apex_objeto_negocio_regla()
	{
		return array (
  'archivo' => 'pgsql_a53_clase_negocio.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_negocio_proyecto',
  'dump_clave_componente' => 'objeto_negocio',
  'dump_order_by' => 'objeto_negocio, nombre',
  'dump_where' => '( objeto_negocio_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_negocio_proyecto',
    1 => 'objeto_negocio',
    2 => 'nombre',
    3 => 'descripcion',
    4 => 'activada',
    5 => 'mensaje_a',
    6 => 'mensaje_b',
  ),
);
	}

	static function apex_objeto_esquema()
	{
		return array (
  'archivo' => 'pgsql_a66_clase_esquema.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_esquema_proyecto',
  'dump_clave_componente' => 'objeto_esquema',
  'dump_order_by' => 'objeto_esquema',
  'dump_where' => '( objeto_esquema_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_esquema_proyecto',
    1 => 'objeto_esquema',
    2 => 'parser',
    3 => 'descripcion',
    4 => 'dot',
    5 => 'debug',
    6 => 'formato',
    7 => 'modelo_ejecucion',
    8 => 'modelo_ejecucion_cache',
    9 => 'tipo_incrustacion',
    10 => 'ancho',
    11 => 'alto',
    12 => 'dirigido',
    13 => 'sql',
  ),
);
	}

	static function apex_objeto_html()
	{
		return array (
  'archivo' => 'pgsql_a67_clase_html.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_html_proyecto',
  'dump_clave_componente' => 'objeto_html',
  'dump_order_by' => 'objeto_html',
  'dump_where' => '( objeto_html_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_html_proyecto',
    1 => 'objeto_html',
    2 => 'html',
  ),
);
	}

}
?>