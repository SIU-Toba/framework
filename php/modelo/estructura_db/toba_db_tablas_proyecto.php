<?php

class toba_db_tablas_proyecto
{
	static function get_lista()
	{
		return array (
  0 => 'apex_proyecto',
  1 => 'apex_estilo',
  2 => 'apex_fuente_datos',
  3 => 'apex_elemento_formulario',
  4 => 'apex_solicitud_obs_tipo',
  5 => 'apex_pagina_tipo',
  6 => 'apex_ptos_control',
  7 => 'apex_ptos_control_param',
  8 => 'apex_ptos_control_ctrl',
  9 => 'apex_consulta_php',
  10 => 'apex_item_zona',
  11 => 'apex_clase',
  12 => 'apex_clase_relacion',
  13 => 'apex_conversion',
  14 => 'apex_msg',
  15 => 'apex_item_msg',
  16 => 'apex_objeto_msg',
  17 => 'apex_nota',
  18 => 'apex_item_nota',
  19 => 'apex_objeto_nota',
  20 => 'apex_relacion_tablas',
  21 => 'apex_dimension',
  22 => 'apex_dimension_gatillo',
  23 => 'apex_objeto_ei_filtro_tipo_col',
  24 => 'apex_molde_opciones_generacion',
  25 => 'apex_permiso',
  26 => 'apex_restriccion_funcional',
  27 => 'apex_restriccion_funcional_ef',
  28 => 'apex_restriccion_funcional_pantalla',
  29 => 'apex_restriccion_funcional_evt',
  30 => 'apex_restriccion_funcional_ei',
  31 => 'apex_restriccion_funcional_cols',
  32 => 'apex_restriccion_funcional_filtro_cols',
);
	}

	static function get_lista_permisos()
	{
		return array (
  0 => 'apex_usuario_perfil_datos',
  1 => 'apex_usuario_perfil_datos_dims',
  2 => 'apex_usuario_grupo_acc',
  3 => 'apex_usuario_grupo_acc_item',
  4 => 'apex_permiso_grupo_acc',
  5 => 'apex_grupo_acc_restriccion_funcional',
);
	}

	static function apex_proyecto()
	{
		return array (
  'archivo' => 'pgsql_a00_tablas_instancia.sql',
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
    19 => 'usuario_anonimo_desc',
    20 => 'usuario_anonimo_grupos_acc',
    21 => 'validacion_intentos',
    22 => 'validacion_intentos_min',
    23 => 'validacion_bloquear_usuario',
    24 => 'validacion_debug',
    25 => 'sesion_tiempo_no_interac_min',
    26 => 'sesion_tiempo_maximo_min',
    27 => 'sesion_subclase',
    28 => 'sesion_subclase_archivo',
    29 => 'contexto_ejecucion_subclase',
    30 => 'contexto_ejecucion_subclase_archivo',
    31 => 'usuario_subclase',
    32 => 'usuario_subclase_archivo',
    33 => 'encriptar_qs',
    34 => 'registrar_solicitud',
    35 => 'registrar_cronometro',
    36 => 'item_inicio_sesion',
    37 => 'item_pre_sesion',
    38 => 'item_pre_sesion_popup',
    39 => 'item_set_sesion',
    40 => 'log_archivo',
    41 => 'log_archivo_nivel',
    42 => 'fuente_datos',
    43 => 'pagina_tipo',
    44 => 'version',
    45 => 'version_fecha',
    46 => 'version_detalle',
    47 => 'version_link',
    48 => 'tiempo_espera_ms',
    49 => 'navegacion_ajax',
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
  'zona' => 'general',
  'desc' => 'Skins',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'estilo',
    1 => 'descripcion',
    2 => 'proyecto',
    3 => 'paleta',
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
    6 => 'subclase_archivo',
    7 => 'subclase_nombre',
    8 => 'orden',
    9 => 'schema',
    10 => 'instancia_id',
    11 => 'administrador',
    12 => 'link_instancia',
    13 => 'usuario',
    14 => 'clave',
    15 => 'base',
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

	static function apex_ptos_control()
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
    1 => 'pto_control',
    2 => 'descripcion',
  ),
);
	}

	static function apex_ptos_control_param()
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
    1 => 'pto_control',
    2 => 'parametro',
  ),
);
	}

	static function apex_ptos_control_ctrl()
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
    1 => 'pto_control',
    2 => 'clase',
    3 => 'archivo',
    4 => 'actua_como',
  ),
);
	}

	static function apex_consulta_php()
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
    1 => 'consulta_php',
    2 => 'clase',
    3 => 'archivo',
    4 => 'descripcion',
  ),
);
	}

	static function apex_item_zona()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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

	static function apex_conversion()
	{
		return array (
  'archivo' => 'pgsql_a03_tablas_componentes.sql',
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

	static function apex_nota()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_notas.sql',
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

	static function apex_item_nota()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_notas.sql',
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

	static function apex_objeto_nota()
	{
		return array (
  'archivo' => 'pgsql_a05_tablas_notas.sql',
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

	static function apex_relacion_tablas()
	{
		return array (
  'archivo' => 'pgsql_a06_tablas_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'relacion_tablas',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'fuente_datos_proyecto',
    1 => 'fuente_datos',
    2 => 'proyecto',
    3 => 'relacion_tablas',
    4 => 'tabla_1',
    5 => 'tabla_1_cols',
    6 => 'tabla_2',
    7 => 'tabla_2_cols',
  ),
);
	}

	static function apex_dimension()
	{
		return array (
  'archivo' => 'pgsql_a06_tablas_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'dimension',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'dimension',
    2 => 'nombre',
    3 => 'descripcion',
    4 => 'schema',
    5 => 'tabla',
    6 => 'col_id',
    7 => 'col_desc',
    8 => 'col_desc_separador',
    9 => 'multitabla_col_tabla',
    10 => 'multitabla_id_tabla',
    11 => 'fuente_datos_proyecto',
    12 => 'fuente_datos',
  ),
);
	}

	static function apex_dimension_gatillo()
	{
		return array (
  'archivo' => 'pgsql_a06_tablas_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'gatillo',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'dimension',
    2 => 'gatillo',
    3 => 'tipo',
    4 => 'orden',
    5 => 'tabla_rel_dim',
    6 => 'columnas_rel_dim',
    7 => 'tabla_gatillo',
    8 => 'ruta_tabla_rel_dim',
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

	static function apex_molde_opciones_generacion()
	{
		return array (
  'archivo' => 'pgsql_a50_asistentes.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'proyecto',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'uso_autoload',
    2 => 'origen_datos_cuadro',
    3 => 'carga_php_include',
    4 => 'carga_php_clase',
  ),
);
	}

	static function apex_permiso()
	{
		return array (
  'archivo' => 'pgsql_a59_tablas_permisos.sql',
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

	static function apex_usuario_perfil_datos()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'permisos',
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

	static function apex_usuario_perfil_datos_dims()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'permisos',
  'dump_order_by' => 'elemento',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario_perfil_datos',
    2 => 'dimension',
    3 => 'elemento',
    4 => 'clave',
  ),
);
	}

	static function apex_usuario_grupo_acc()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'permisos',
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

	static function apex_usuario_grupo_acc_item()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'permisos',
  'dump_order_by' => 'usuario_grupo_acc, item',
  'zona' => 'usuario, item',
  'desc' => '',
  'columna_grupo_desarrollo' => 'item',
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

	static function apex_permiso_grupo_acc()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'permisos',
  'dump_order_by' => 'permiso, usuario_grupo_acc',
  'zona' => 'usuario',
  'desc' => '',
  'columna_grupo_desarrollo' => 'permiso',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario_grupo_acc',
    2 => 'permiso',
  ),
);
	}

	static function apex_restriccion_funcional()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'restriccion_funcional',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'restriccion_funcional',
    2 => 'descripcion',
  ),
);
	}

	static function apex_grupo_acc_restriccion_funcional()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'permisos',
  'dump_order_by' => 'usuario_grupo_acc, restriccion_funcional',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'usuario_grupo_acc',
    2 => 'restriccion_funcional',
  ),
);
	}

	static function apex_restriccion_funcional_ef()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'restriccion_funcional, objeto_ei_formulario_fila',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'restriccion_funcional',
    2 => 'item',
    3 => 'objeto_ei_formulario_fila',
    4 => 'objeto_ei_formulario',
    5 => 'no_visible',
    6 => 'no_editable',
  ),
);
	}

	static function apex_restriccion_funcional_pantalla()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'restriccion_funcional, pantalla',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'restriccion_funcional',
    2 => 'item',
    3 => 'pantalla',
    4 => 'objeto_ci',
    5 => 'no_visible',
  ),
);
	}

	static function apex_restriccion_funcional_evt()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'restriccion_funcional, evento_id',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'restriccion_funcional',
    2 => 'item',
    3 => 'evento_id',
    4 => 'no_visible',
  ),
);
	}

	static function apex_restriccion_funcional_ei()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'restriccion_funcional, objeto',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'restriccion_funcional',
    2 => 'item',
    3 => 'objeto',
    4 => 'no_visible',
  ),
);
	}

	static function apex_restriccion_funcional_cols()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'restriccion_funcional, objeto_cuadro_col',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'restriccion_funcional',
    2 => 'item',
    3 => 'objeto_cuadro',
    4 => 'objeto_cuadro_col',
    5 => 'no_visible',
  ),
);
	}

	static function apex_restriccion_funcional_filtro_cols()
	{
		return array (
  'archivo' => 'pgsql_a60_tablas_perfil_funcional.sql',
  'proyecto' => 'toba',
  'dump' => 'multiproyecto',
  'dump_order_by' => 'restriccion_funcional, objeto_ei_filtro_col',
  'zona' => 'usuario',
  'desc' => '',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'proyecto',
    1 => 'restriccion_funcional',
    2 => 'item',
    3 => 'objeto_ei_filtro_col',
    4 => 'objeto_ei_filtro',
    5 => 'no_visible',
  ),
);
	}

}

?>