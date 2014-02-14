<?php

class toba_db_tablas_nucleo
{
	static function get_lista_nucleo_multiproyecto()
	{
		return array (
  0 => 'apex_proyecto',
  1 => 'apex_estilo',
  2 => 'apex_puntos_montaje',
  3 => 'apex_fuente_datos',
  4 => 'apex_fuente_datos_schemas',
  5 => 'apex_elemento_formulario',
  6 => 'apex_solicitud_obs_tipo',
  7 => 'apex_pagina_tipo',
  8 => 'apex_perfil_datos_set_prueba',
  9 => 'apex_clase',
  10 => 'apex_clase_relacion',
  11 => 'apex_msg',
  12 => 'apex_objeto_ei_filtro_tipo_col',
);
	}

	static function get_lista()
	{
		return array (
  0 => 'apex_menu_tipos',
  1 => 'apex_log_sistema_tipo',
  2 => 'apex_fuente_datos_motor',
  3 => 'apex_recurso_origen',
  4 => 'apex_nivel_acceso',
  5 => 'apex_solicitud_tipo',
  6 => 'apex_columna_estilo',
  7 => 'apex_columna_formato',
  8 => 'apex_usuario_tipodoc',
  9 => 'apex_clase_tipo',
  10 => 'apex_msg_tipo',
  11 => 'apex_nota_tipo',
  12 => 'apex_objeto_mt_me_tipo_nav',
  13 => 'apex_grafico',
  14 => 'apex_admin_persistencia',
  15 => 'apex_tipo_datos',
  16 => 'apex_molde_operacion_tipo',
  17 => 'apex_molde_operacion_tipo_dato',
);
	}

	static function apex_proyecto()
	{
		return array (
  'archivo' => 'pgsql_a00_tablas_instancia.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'proyecto',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'proyecto',
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
    7 => 'pm_impresion',
    8 => 'salida_impr_html_c',
    9 => 'salida_impr_html_a',
    10 => 'menu',
    11 => 'path_includes',
    12 => 'path_browser',
    13 => 'administrador',
    14 => 'listar_multiproyecto',
    15 => 'orden',
    16 => 'palabra_vinculo_std',
    17 => 'version_toba',
    18 => 'requiere_validacion',
    19 => 'usuario_anonimo',
    20 => 'usuario_anonimo_desc',
    21 => 'usuario_anonimo_grupos_acc',
    22 => 'validacion_intentos',
    23 => 'validacion_intentos_min',
    24 => 'validacion_bloquear_usuario',
    25 => 'validacion_debug',
    26 => 'sesion_tiempo_no_interac_min',
    27 => 'sesion_tiempo_maximo_min',
    28 => 'pm_sesion',
    29 => 'sesion_subclase',
    30 => 'sesion_subclase_archivo',
    31 => 'pm_contexto',
    32 => 'contexto_ejecucion_subclase',
    33 => 'contexto_ejecucion_subclase_archivo',
    34 => 'pm_usuario',
    35 => 'usuario_subclase',
    36 => 'usuario_subclase_archivo',
    37 => 'encriptar_qs',
    38 => 'registrar_solicitud',
    39 => 'registrar_cronometro',
    40 => 'item_inicio_sesion',
    41 => 'item_pre_sesion',
    42 => 'item_pre_sesion_popup',
    43 => 'item_set_sesion',
    44 => 'log_archivo',
    45 => 'log_archivo_nivel',
    46 => 'fuente_datos',
    47 => 'pagina_tipo',
    48 => 'version',
    49 => 'version_fecha',
    50 => 'version_detalle',
    51 => 'version_link',
    52 => 'tiempo_espera_ms',
    53 => 'navegacion_ajax',
    54 => 'codigo_ga_tracker',
    55 => 'extension_toba',
    56 => 'extension_proyecto',
  ),
);
	}

	static function apex_menu_tipos()
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

	static function apex_estilo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'proyecto, estilo',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'estilo',
  'zona' => 'general',
  'desc' => 'Skins',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'estilo',
    1 => 'descripcion',
    2 => 'proyecto',
    3 => 'es_css3',
    4 => 'paleta',
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

	static function apex_puntos_montaje()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'id',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'id',
  'zona' => 'general',
  'desc' => 'tabla de puntos de montaje',
  'version' => '1.6',
  'columnas' => 
  array (
    0 => 'id',
    1 => 'etiqueta',
    2 => 'proyecto',
    3 => 'proyecto_ref',
    4 => 'descripcion',
    5 => 'path_pm',
    6 => 'tipo',
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
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'fuente_datos',
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
    6 => 'punto_montaje',
    7 => 'subclase_archivo',
    8 => 'subclase_nombre',
    9 => 'orden',
    10 => 'schema',
    11 => 'instancia_id',
    12 => 'administrador',
    13 => 'link_instancia',
    14 => 'tiene_auditoria',
    15 => 'parsea_errores',
    16 => 'permisos_por_tabla',
    17 => 'usuario',
    18 => 'clave',
    19 => 'base',
  ),
);
	}

	static function apex_fuente_datos_schemas()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'fuente_datos, nombre',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'fuente_datos, nombre',
  'zona' => 'general',
  'desc' => 'Esquemas pertenecientes a la BD',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'fuente_datos',
    2 => 'nombre',
    3 => 'principal',
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
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'elemento_formulario',
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
    7 => 'es_seleccion',
    8 => 'es_seleccion_multiple',
  ),
);
	}

	static function apex_solicitud_obs_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'solicitud_obs_tipo',
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
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'pagina_tipo',
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
    9 => 'punto_montaje',
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
    6 => 'estilo_defecto',
  ),
);
	}

	static function apex_perfil_datos_set_prueba()
	{
		return array (
  'archivo' => 'pgsql_a01_tablas_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'fuente_datos',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'fuente_datos, proyecto',
  'zona' => 'general',
  'desc' => 'Lote de pruebas para los perfiles de datos de la fuente',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'fuente_datos',
    2 => 'lote',
    3 => 'seleccionados',
    4 => 'parametros',
  ),
);
	}

	static function apex_usuario_tipodoc()
	{
		return array (
  'archivo' => 'pgsql_a02_tablas_usuario.sql',
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
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'clase',
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
    5 => 'icono',
    6 => 'descripcion_corta',
    7 => 'editor_proyecto',
    8 => 'editor_item',
    9 => 'objeto_dr_proyecto',
    10 => 'objeto_dr',
    11 => 'utiliza_fuente_datos',
    12 => 'screenshot',
    13 => 'ancestro_proyecto',
    14 => 'ancestro',
    15 => 'instanciador_id',
    16 => 'instanciador_proyecto',
    17 => 'instanciador_item',
    18 => 'editor_id',
    19 => 'editor_ancestro_proyecto',
    20 => 'editor_ancestro',
    21 => 'plan_dump_objeto',
    22 => 'sql_info',
    23 => 'doc_clase',
    24 => 'doc_db',
    25 => 'doc_sql',
    26 => 'vinculos',
    27 => 'autodoc',
    28 => 'parametro_a',
    29 => 'parametro_b',
    30 => 'parametro_c',
    31 => 'exclusivo_toba',
    32 => 'solicitud_tipo',
  ),
);
	}

	static function apex_clase_relacion()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'clase_relacion',
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'clase_relacion',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'clase_relacion',
    2 => 'clase_contenedora',
    3 => 'clase_contenida',
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
  'clave_proyecto' => 'proyecto',
  'clave_elemento' => 'msg',
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

	static function apex_nota_tipo()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_notas.sql',
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

	static function apex_objeto_ei_filtro_tipo_col()
	{
		return array (
  'archivo' => 'pgsql_a14_componente_ei_filtro.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo_multiproyecto',
  'dump_order_by' => 'tipo_col',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tipo_col',
    1 => 'descripcion',
    2 => 'proyecto',
  ),
);
	}

	static function apex_grafico()
	{
		return array (
  'archivo' => 'pgsql_a16_componente_ei_grafico.sql',
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

	static function apex_molde_operacion_tipo()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'operacion_tipo',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'operacion_tipo',
    1 => 'descripcion_corta',
    2 => 'descripcion',
    3 => 'clase',
    4 => 'ci',
    5 => 'icono',
    6 => 'vista_previa',
    7 => 'orden',
  ),
);
	}

	static function apex_molde_operacion_tipo_dato()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'nucleo',
  'dump_order_by' => 'tipo_dato',
  'zona' => 'central',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'tipo_dato',
    1 => 'descripcion_corta',
    2 => 'descripcion',
    3 => 'dt_tipo_dato',
    4 => 'elemento_formulario',
    5 => 'cuadro_estilo',
    6 => 'cuadro_formato',
    7 => 'orden',
    8 => 'filtro_operador',
  ),
);
	}

}

?>