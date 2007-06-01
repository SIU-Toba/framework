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
  9 => 'apex_usuario_perfil_datos',
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
  20 => 'apex_permiso',
);
	}

	static function get_lista_permisos()
	{
		return array (
  0 => 'apex_usuario_grupo_acc',
  1 => 'apex_usuario_grupo_acc_item',
  2 => 'apex_permiso_grupo_acc',
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
    23 => 'validacion_debug',
    24 => 'sesion_tiempo_no_interac_min',
    25 => 'sesion_tiempo_maximo_min',
    26 => 'sesion_subclase',
    27 => 'sesion_subclase_archivo',
    28 => 'contexto_ejecucion_subclase',
    29 => 'contexto_ejecucion_subclase_archivo',
    30 => 'usuario_subclase',
    31 => 'usuario_subclase_archivo',
    32 => 'encriptar_qs',
    33 => 'registrar_solicitud',
    34 => 'registrar_cronometro',
    35 => 'item_inicio_sesion',
    36 => 'item_pre_sesion',
    37 => 'item_set_sesion',
    38 => 'log_archivo',
    39 => 'log_archivo_nivel',
    40 => 'fuente_datos',
    41 => 'version',
    42 => 'version_fecha',
    43 => 'version_detalle',
    44 => 'version_link',
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

	static function apex_usuario_perfil_datos()
	{
		return array (
  'archivo' => 'pgsql_a02_tablas_usuario.sql',
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
  'archivo' => 'pgsql_a02_tablas_usuario.sql',
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
  'archivo' => 'pgsql_a02_tablas_usuario.sql',
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
  'archivo' => 'pgsql_a02_tablas_usuario.sql',
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

}

?>