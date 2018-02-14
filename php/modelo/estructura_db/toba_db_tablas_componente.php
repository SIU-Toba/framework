<?php

class toba_db_tablas_componente
{
	static function get_lista()
	{
		return array (
  0 => 'apex_item',
  1 => 'apex_item_info',
  2 => 'apex_item_permisos_tablas',
  3 => 'apex_objeto',
  4 => 'apex_objeto_info',
  5 => 'apex_objeto_dependencias',
  6 => 'apex_objeto_dep_consumo',
  7 => 'apex_objeto_eventos',
  8 => 'apex_ptos_control_x_evento',
  9 => 'apex_item_objeto',
  10 => 'apex_objeto_mt_me',
  11 => 'apex_objeto_ci_pantalla',
  12 => 'apex_objetos_pantalla',
  13 => 'apex_eventos_pantalla',
  14 => 'apex_objeto_cuadro',
  15 => 'apex_objeto_cuadro_cc',
  16 => 'apex_objeto_ei_cuadro_columna',
  17 => 'apex_objeto_cuadro_col_cc',
  18 => 'apex_objeto_ut_formulario',
  19 => 'apex_objeto_ei_formulario_ef',
  20 => 'apex_objeto_esquema',
  21 => 'apex_objeto_ei_filtro',
  22 => 'apex_objeto_ei_filtro_col',
  23 => 'apex_objeto_mapa',
  24 => 'apex_objeto_grafico',
  25 => 'apex_objeto_codigo',
  26 => 'apex_objeto_ei_firma',
  27 => 'apex_objeto_db_registros',
  28 => 'apex_objeto_db_registros_col',
  29 => 'apex_objeto_db_columna_fks',
  30 => 'apex_objeto_db_registros_ext',
  31 => 'apex_objeto_db_registros_ext_col',
  32 => 'apex_objeto_db_registros_uniq',
  33 => 'apex_objeto_datos_rel',
  34 => 'apex_objeto_datos_rel_asoc',
  35 => 'apex_objeto_rel_columnas_asoc',
  36 => 'apex_molde_operacion',
  37 => 'apex_molde_operacion_log',
  38 => 'apex_molde_operacion_log_elementos',
  39 => 'apex_molde_operacion_abms',
  40 => 'apex_molde_operacion_abms_fila',
  41 => 'apex_molde_operacion_importacion',
);
	}

	static function apex_item()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
    11 => 'actividad_buffer_proyecto',
    12 => 'actividad_buffer',
    13 => 'actividad_patron_proyecto',
    14 => 'actividad_patron',
    15 => 'nombre',
    16 => 'descripcion',
    17 => 'punto_montaje',
    18 => 'actividad_accion',
    19 => 'menu',
    20 => 'orden',
    21 => 'solicitud_registrar',
    22 => 'solicitud_obs_tipo_proyecto',
    23 => 'solicitud_obs_tipo',
    24 => 'solicitud_observacion',
    25 => 'solicitud_registrar_cron',
    26 => 'prueba_directorios',
    27 => 'zona_proyecto',
    28 => 'zona',
    29 => 'zona_orden',
    30 => 'zona_listar',
    31 => 'imagen_recurso_origen',
    32 => 'imagen',
    33 => 'parametro_a',
    34 => 'parametro_b',
    35 => 'parametro_c',
    36 => 'publico',
    37 => 'redirecciona',
    38 => 'usuario',
    39 => 'exportable',
    40 => 'creacion',
    41 => 'retrasar_headers',
  ),
);
	}

	static function apex_item_info()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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

	static function apex_item_permisos_tablas()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'item',
  'clave_elemento' => 'proyecto, item, fuente_datos',
  'dump_order_by' => 'item',
  'dump_where' => '(	proyecto = \\\'%%\\\'	)',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'item',
    2 => 'fuente_datos',
    3 => 'esquema',
    4 => 'tabla',
    5 => 'permisos',
  ),
);
	}

	static function apex_objeto()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
    3 => 'identificador',
    4 => 'reflexivo',
    5 => 'clase_proyecto',
    6 => 'clase',
    7 => 'punto_montaje',
    8 => 'subclase',
    9 => 'subclase_archivo',
    10 => 'objeto_categoria_proyecto',
    11 => 'objeto_categoria',
    12 => 'nombre',
    13 => 'titulo',
    14 => 'colapsable',
    15 => 'descripcion',
    16 => 'fuente_datos_proyecto',
    17 => 'fuente_datos',
    18 => 'solicitud_registrar',
    19 => 'solicitud_obj_obs_tipo',
    20 => 'solicitud_obj_observacion',
    21 => 'parametro_a',
    22 => 'parametro_b',
    23 => 'parametro_c',
    24 => 'parametro_d',
    25 => 'parametro_e',
    26 => 'parametro_f',
    27 => 'usuario',
    28 => 'creacion',
    29 => 'posicion_botonera',
  ),
);
	}

	static function apex_objeto_info()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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

	static function apex_objeto_dep_consumo()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
    1 => 'consumo_id',
    2 => 'objeto_consumidor',
    3 => 'objeto_proveedor',
    4 => 'identificador',
    5 => 'parametros_a',
    6 => 'parametros_b',
    7 => 'parametros_c',
    8 => 'inicializar',
  ),
);
	}

	static function apex_objeto_eventos()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
    16 => 'defecto',
    17 => 'display_datos_cargados',
    18 => 'grupo',
    19 => 'accion',
    20 => 'accion_imphtml_debug',
    21 => 'accion_vinculo_carpeta',
    22 => 'accion_vinculo_item',
    23 => 'accion_vinculo_objeto',
    24 => 'accion_vinculo_popup',
    25 => 'accion_vinculo_popup_param',
    26 => 'accion_vinculo_target',
    27 => 'accion_vinculo_celda',
    28 => 'accion_vinculo_servicio',
    29 => 'es_seleccion_multiple',
    30 => 'es_autovinculo',
  ),
);
	}

	static function apex_ptos_control_x_evento()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto',
  'clave_elemento' => 'proyecto, pto_control, evento_id',
  'dump_order_by' => 'objeto, evento_id',
  'zona' => 'nucleo',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'pto_control',
    2 => 'evento_id',
    3 => 'objeto',
  ),
);
	}

	static function apex_item_objeto()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'item',
  'clave_elemento' => 'proyecto, item, objeto',
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

	static function apex_objeto_mt_me()
	{
		return array (
  'archivo' => 'pgsql_a10_componente_ci.sql',
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
    8 => 'botonera_barra_item',
    9 => 'con_toc',
    10 => 'incremental',
    11 => 'debug_eventos',
    12 => 'activacion_procesar',
    13 => 'activacion_cancelar',
    14 => 'ev_procesar',
    15 => 'ev_cancelar',
    16 => 'objetos',
    17 => 'post_procesar',
    18 => 'metodo_despachador',
    19 => 'metodo_opciones',
  ),
);
	}

	static function apex_objeto_ci_pantalla()
	{
		return array (
  'archivo' => 'pgsql_a10_componente_ci.sql',
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
    12 => 'subclase',
    13 => 'subclase_archivo',
    14 => 'template',
    15 => 'template_impresion',
    16 => 'punto_montaje',
  ),
);
	}

	static function apex_objetos_pantalla()
	{
		return array (
  'archivo' => 'pgsql_a10_componente_ci.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto_ci',
  'clave_elemento' => 'proyecto, objeto_ci, pantalla, dep_id',
  'dump_order_by' => 'proyecto, objeto_ci, pantalla, dep_id',
  'dump_where' => '(	proyecto =	\\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'pantalla',
    2 => 'objeto_ci',
    3 => 'orden',
    4 => 'dep_id',
  ),
);
	}

	static function apex_eventos_pantalla()
	{
		return array (
  'archivo' => 'pgsql_a10_componente_ci.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto_ci',
  'clave_elemento' => 'proyecto, objeto_ci, pantalla, evento_id',
  'dump_order_by' => 'proyecto, objeto_ci, pantalla, evento_id',
  'dump_where' => '(	proyecto =	\\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'pantalla',
    1 => 'objeto_ci',
    2 => 'evento_id',
    3 => 'proyecto',
  ),
);
	}

	static function apex_objeto_cuadro()
	{
		return array (
  'archivo' => 'pgsql_a11_componente_ei_cuadro.sql',
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
    6 => 'columna_descripcion',
    7 => 'clave_dbr',
    8 => 'archivos_callbacks',
    9 => 'ancho',
    10 => 'ordenar',
    11 => 'paginar',
    12 => 'tamano_pagina',
    13 => 'tipo_paginado',
    14 => 'mostrar_total_registros',
    15 => 'eof_invisible',
    16 => 'eof_customizado',
    17 => 'siempre_con_titulo',
    18 => 'exportar_paginado',
    19 => 'exportar',
    20 => 'exportar_rtf',
    21 => 'pdf_propiedades',
    22 => 'pdf_respetar_paginacion',
    23 => 'asociacion_columnas',
    24 => 'ev_seleccion',
    25 => 'ev_eliminar',
    26 => 'dao_nucleo_proyecto',
    27 => 'dao_nucleo',
    28 => 'dao_metodo',
    29 => 'dao_parametros',
    30 => 'desplegable',
    31 => 'desplegable_activo',
    32 => 'scroll',
    33 => 'scroll_alto',
    34 => 'cc_modo',
    35 => 'cc_modo_anidado_colap',
    36 => 'cc_modo_anidado_totcol',
    37 => 'cc_modo_anidado_totcua',
  ),
);
	}

	static function apex_objeto_cuadro_cc()
	{
		return array (
  'archivo' => 'pgsql_a11_componente_ei_cuadro.sql',
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
    12 => 'modo_inicio_colapsado',
  ),
);
	}

	static function apex_objeto_ei_cuadro_columna()
	{
		return array (
  'archivo' => 'pgsql_a11_componente_ei_cuadro.sql',
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
    18 => 'usar_vinculo',
    19 => 'vinculo_carpeta',
    20 => 'vinculo_item',
    21 => 'vinculo_popup',
    22 => 'vinculo_popup_param',
    23 => 'vinculo_target',
    24 => 'vinculo_celda',
    25 => 'vinculo_servicio',
    26 => 'permitir_html',
    27 => 'grupo',
    28 => 'evento_asociado',
  ),
);
	}

	static function apex_objeto_cuadro_col_cc()
	{
		return array (
  'archivo' => 'pgsql_a11_componente_ei_cuadro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_cuadro_proyecto',
  'dump_clave_componente' => 'objeto_cuadro',
  'clave_elemento' => 'objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col',
  'dump_order_by' => 'objeto_cuadro, objeto_cuadro_col, objeto_cuadro_cc',
  'dump_where' => '( objeto_cuadro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_cuadro_cc',
    1 => 'objeto_cuadro_proyecto',
    2 => 'objeto_cuadro',
    3 => 'objeto_cuadro_col',
    4 => 'total',
  ),
);
	}

	static function apex_objeto_ut_formulario()
	{
		return array (
  'archivo' => 'pgsql_a12_componente_ei_formulario.sql',
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
    18 => 'expandir_descripcion',
    19 => 'campo_bl',
    20 => 'scroll',
    21 => 'filas',
    22 => 'filas_agregar',
    23 => 'filas_agregar_online',
    24 => 'filas_agregar_abajo',
    25 => 'filas_agregar_texto',
    26 => 'filas_borrar_en_linea',
    27 => 'filas_undo',
    28 => 'filas_ordenar',
    29 => 'filas_ordenar_en_linea',
    30 => 'columna_orden',
    31 => 'filas_numerar',
    32 => 'ev_seleccion',
    33 => 'alto',
    34 => 'analisis_cambios',
    35 => 'no_imprimir_efs_sin_estado',
    36 => 'resaltar_efs_con_estado',
    37 => 'template',
    38 => 'template_impresion',
  ),
);
	}

	static function apex_objeto_ei_formulario_ef()
	{
		return array (
  'archivo' => 'pgsql_a12_componente_ei_formulario.sql',
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
    0 => 'objeto_ei_formulario_fila',
    1 => 'objeto_ei_formulario',
    2 => 'objeto_ei_formulario_proyecto',
    3 => 'identificador',
    4 => 'elemento_formulario',
    5 => 'columnas',
    6 => 'obligatorio',
    7 => 'oculto_relaja_obligatorio',
    8 => 'orden',
    9 => 'etiqueta',
    10 => 'etiqueta_estilo',
    11 => 'descripcion',
    12 => 'colapsado',
    13 => 'desactivado',
    14 => 'estilo',
    15 => 'total',
    16 => 'inicializacion',
    17 => 'permitir_html',
    18 => 'deshabilitar_rest_func',
    19 => 'estado_defecto',
    20 => 'solo_lectura',
    21 => 'solo_lectura_modificacion',
    22 => 'carga_metodo',
    23 => 'carga_clase',
    24 => 'carga_include',
    25 => 'carga_dt',
    26 => 'carga_consulta_php',
    27 => 'carga_sql',
    28 => 'carga_fuente',
    29 => 'carga_lista',
    30 => 'carga_col_clave',
    31 => 'carga_col_desc',
    32 => 'carga_maestros',
    33 => 'carga_cascada_relaj',
    34 => 'cascada_mantiene_estado',
    35 => 'carga_permite_no_seteado',
    36 => 'carga_no_seteado',
    37 => 'carga_no_seteado_ocultar',
    38 => 'edit_tamano',
    39 => 'edit_maximo',
    40 => 'edit_mascara',
    41 => 'edit_unidad',
    42 => 'edit_rango',
    43 => 'edit_filas',
    44 => 'edit_columnas',
    45 => 'edit_wrap',
    46 => 'edit_resaltar',
    47 => 'edit_ajustable',
    48 => 'edit_confirmar_clave',
    49 => 'edit_expreg',
    50 => 'popup_item',
    51 => 'popup_proyecto',
    52 => 'popup_editable',
    53 => 'popup_ventana',
    54 => 'popup_carga_desc_metodo',
    55 => 'popup_carga_desc_clase',
    56 => 'popup_carga_desc_include',
    57 => 'popup_puede_borrar_estado',
    58 => 'fieldset_fin',
    59 => 'check_valor_si',
    60 => 'check_valor_no',
    61 => 'check_desc_si',
    62 => 'check_desc_no',
    63 => 'check_ml_toggle',
    64 => 'fijo_sin_estado',
    65 => 'editor_ancho',
    66 => 'editor_alto',
    67 => 'editor_botonera',
    68 => 'selec_cant_minima',
    69 => 'selec_cant_maxima',
    70 => 'selec_utilidades',
    71 => 'selec_tamano',
    72 => 'selec_ancho',
    73 => 'selec_serializar',
    74 => 'selec_cant_columnas',
    75 => 'upload_extensiones',
    76 => 'punto_montaje',
    77 => 'placeholder',
  ),
);
	}

	static function apex_objeto_esquema()
	{
		return array (
  'archivo' => 'pgsql_a13_componente_ei_esquema.sql',
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

	static function apex_objeto_ei_filtro()
	{
		return array (
  'archivo' => 'pgsql_a14_componente_ei_filtro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_ei_filtro_proyecto',
  'dump_clave_componente' => 'objeto_ei_filtro',
  'dump_order_by' => 'objeto_ei_filtro',
  'dump_where' => '( objeto_ei_filtro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_ei_filtro_proyecto',
    1 => 'objeto_ei_filtro',
    2 => 'ancho',
  ),
);
	}

	static function apex_objeto_ei_filtro_col()
	{
		return array (
  'archivo' => 'pgsql_a14_componente_ei_filtro.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_ei_filtro_proyecto',
  'dump_clave_componente' => 'objeto_ei_filtro',
  'dump_order_by' => 'objeto_ei_filtro_col',
  'dump_where' => '( objeto_ei_filtro_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_ei_filtro_col',
    1 => 'objeto_ei_filtro',
    2 => 'objeto_ei_filtro_proyecto',
    3 => 'tipo',
    4 => 'nombre',
    5 => 'expresion',
    6 => 'etiqueta',
    7 => 'descripcion',
    8 => 'obligatorio',
    9 => 'inicial',
    10 => 'orden',
    11 => 'estado_defecto',
    12 => 'opciones_es_multiple',
    13 => 'opciones_ef',
    14 => 'carga_metodo',
    15 => 'carga_clase',
    16 => 'carga_include',
    17 => 'carga_dt',
    18 => 'carga_consulta_php',
    19 => 'carga_sql',
    20 => 'carga_fuente',
    21 => 'carga_lista',
    22 => 'carga_col_clave',
    23 => 'carga_col_desc',
    24 => 'carga_permite_no_seteado',
    25 => 'carga_no_seteado',
    26 => 'carga_no_seteado_ocultar',
    27 => 'carga_maestros',
    28 => 'edit_tamano',
    29 => 'edit_maximo',
    30 => 'edit_mascara',
    31 => 'edit_unidad',
    32 => 'edit_rango',
    33 => 'edit_expreg',
    34 => 'estilo',
    35 => 'popup_item',
    36 => 'popup_proyecto',
    37 => 'popup_editable',
    38 => 'popup_ventana',
    39 => 'popup_carga_desc_metodo',
    40 => 'popup_carga_desc_clase',
    41 => 'popup_carga_desc_include',
    42 => 'popup_puede_borrar_estado',
    43 => 'punto_montaje',
    44 => 'check_valor_si',
    45 => 'check_valor_no',
    46 => 'check_desc_si',
    47 => 'check_desc_no',
    48 => 'selec_cant_minima',
    49 => 'selec_cant_maxima',
    50 => 'selec_utilidades',
    51 => 'selec_tamano',
    52 => 'selec_ancho',
    53 => 'selec_serializar',
    54 => 'selec_cant_columnas',
    55 => 'placeholder',
  ),
);
	}

	static function apex_objeto_mapa()
	{
		return array (
  'archivo' => 'pgsql_a15_componente_ei_mapa.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_mapa_proyecto',
  'dump_clave_componente' => 'objeto_mapa',
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
    2 => 'mapfile_path',
  ),
);
	}

	static function apex_objeto_grafico()
	{
		return array (
  'archivo' => 'pgsql_a16_componente_ei_grafico.sql',
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
    2 => 'descripcion',
    3 => 'grafico',
    4 => 'ancho',
    5 => 'alto',
  ),
);
	}

	static function apex_objeto_codigo()
	{
		return array (
  'archivo' => 'pgsql_a17_componente_ei_codigo.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_codigo_proyecto',
  'dump_clave_componente' => 'objeto_codigo',
  'dump_order_by' => 'objeto_codigo',
  'dump_where' => '( objeto_codigo_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_codigo_proyecto',
    1 => 'objeto_codigo',
    2 => 'descripcion',
    3 => 'ancho',
    4 => 'alto',
  ),
);
	}

	static function apex_objeto_ei_firma()
	{
		return array (
  'archivo' => 'pgsql_a18_componente_ei_firma.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_ei_firma_proyecto',
  'dump_clave_componente' => 'objeto_ei_firma',
  'dump_order_by' => 'objeto_ei_firma',
  'dump_where' => '( objeto_ei_firma_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_ei_firma_proyecto',
    1 => 'objeto_ei_firma',
    2 => 'ancho',
    3 => 'alto',
  ),
);
	}

	static function apex_objeto_db_registros()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
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
    4 => 'punto_montaje',
    5 => 'ap',
    6 => 'ap_clase',
    7 => 'ap_archivo',
    8 => 'tabla',
    9 => 'tabla_ext',
    10 => 'alias',
    11 => 'modificar_claves',
    12 => 'fuente_datos_proyecto',
    13 => 'fuente_datos',
    14 => 'permite_actualizacion_automatica',
    15 => 'esquema',
    16 => 'esquema_ext',
  ),
);
	}

	static function apex_objeto_db_registros_col()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
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
    11 => 'tabla',
  ),
);
	}

	static function apex_objeto_db_columna_fks()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto, id',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'id',
    1 => 'objeto_proyecto',
    2 => 'objeto',
    3 => 'tabla',
    4 => 'columna',
    5 => 'tabla_ext',
    6 => 'columna_ext',
  ),
);
	}

	static function apex_objeto_db_registros_ext()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto, externa_id',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'externa_id',
    3 => 'tipo',
    4 => 'sincro_continua',
    5 => 'metodo',
    6 => 'clase',
    7 => 'include',
    8 => 'punto_montaje',
    9 => 'sql',
    10 => 'dato_estricto',
    11 => 'carga_dt',
    12 => 'carga_consulta_php',
    13 => 'permite_carga_masiva',
    14 => 'metodo_masivo',
  ),
);
	}

	static function apex_objeto_db_registros_ext_col()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_proyecto',
  'dump_clave_componente' => 'objeto',
  'clave_elemento' => 'objeto, externa_id, col_id, objeto_proyecto',
  'dump_order_by' => 'objeto, externa_id, col_id',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => 'Asocia una carga externa con una columna, ya sea como resultado o como parametro',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'externa_id',
    3 => 'col_id',
    4 => 'es_resultado',
  ),
);
	}

	static function apex_objeto_db_registros_uniq()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'objeto_proyecto',
  'dump_clave_componente' => 'objeto',
  'dump_order_by' => 'objeto, uniq_id',
  'dump_where' => '( objeto_proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'objeto_proyecto',
    1 => 'objeto',
    2 => 'uniq_id',
    3 => 'columnas',
  ),
);
	}

	static function apex_objeto_datos_rel()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
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
    5 => 'punto_montaje',
    6 => 'ap_clase',
    7 => 'ap_archivo',
    8 => 'sinc_susp_constraints',
    9 => 'sinc_orden_automatico',
    10 => 'sinc_lock_optimista',
  ),
);
	}

	static function apex_objeto_datos_rel_asoc()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
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

	static function apex_objeto_rel_columnas_asoc()
	{
		return array (
  'archivo' => 'pgsql_a40_componente_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'objeto',
  'clave_elemento' => 'asoc_id, objeto, proyecto, padre_objeto, hijo_objeto, padre_clave, hijo_clave',
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
    3 => 'padre_objeto',
    4 => 'padre_clave',
    5 => 'hijo_objeto',
    6 => 'hijo_clave',
  ),
);
	}

	static function apex_molde_operacion()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'molde',
  'dump_order_by' => 'molde',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'molde',
    2 => 'operacion_tipo',
    3 => 'nombre',
    4 => 'item',
    5 => 'carpeta_archivos',
    6 => 'prefijo_clases',
    7 => 'fuente',
    8 => 'punto_montaje',
  ),
);
	}

	static function apex_molde_operacion_log()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'molde',
  'dump_order_by' => 'generacion',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'molde',
    2 => 'generacion',
    3 => 'momento',
  ),
);
	}

	static function apex_molde_operacion_log_elementos()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'molde',
  'dump_order_by' => 'generacion',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'generacion',
    1 => 'molde',
    2 => 'id',
    3 => 'tipo',
    4 => 'proyecto',
    5 => 'clave',
  ),
);
	}

	static function apex_molde_operacion_abms()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'molde',
  'dump_order_by' => 'molde',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'molde',
    2 => 'tabla',
    3 => 'gen_usa_filtro',
    4 => 'gen_separar_pantallas',
    5 => 'filtro_comprobar_parametros',
    6 => 'cuadro_eof',
    7 => 'cuadro_eliminar_filas',
    8 => 'cuadro_id',
    9 => 'cuadro_forzar_filtro',
    10 => 'cuadro_carga_origen',
    11 => 'cuadro_carga_sql',
    12 => 'cuadro_carga_php_include',
    13 => 'cuadro_carga_php_clase',
    14 => 'cuadro_carga_php_metodo',
    15 => 'datos_tabla_validacion',
    16 => 'apdb_pre',
    17 => 'punto_montaje',
  ),
);
	}

	static function apex_molde_operacion_abms_fila()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'molde',
  'dump_order_by' => 'molde, fila',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'molde',
    2 => 'fila',
    3 => 'orden',
    4 => 'columna',
    5 => 'asistente_tipo_dato',
    6 => 'etiqueta',
    7 => 'en_cuadro',
    8 => 'en_form',
    9 => 'en_filtro',
    10 => 'filtro_operador',
    11 => 'cuadro_estilo',
    12 => 'cuadro_formato',
    13 => 'dt_tipo_dato',
    14 => 'dt_largo',
    15 => 'dt_secuencia',
    16 => 'dt_pk',
    17 => 'elemento_formulario',
    18 => 'ef_obligatorio',
    19 => 'ef_desactivar_modificacion',
    20 => 'ef_procesar_javascript',
    21 => 'ef_carga_origen',
    22 => 'ef_carga_sql',
    23 => 'ef_carga_php_include',
    24 => 'ef_carga_php_clase',
    25 => 'ef_carga_php_metodo',
    26 => 'ef_carga_tabla',
    27 => 'ef_carga_col_clave',
    28 => 'ef_carga_col_desc',
    29 => 'punto_montaje',
  ),
);
	}

	static function apex_molde_operacion_importacion()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'componente',
  'dump_clave_proyecto' => 'proyecto',
  'dump_clave_componente' => 'molde',
  'dump_order_by' => 'molde',
  'dump_where' => '( proyecto = \\\'%%\\\' )',
  'zona' => 'objeto',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'molde',
    2 => 'origen_item',
    3 => 'origen_proyecto',
  ),
);
	}

}

?>