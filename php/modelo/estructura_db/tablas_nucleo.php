<?
//Generador: nucleo_parser_ddl.php

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
  17 => 'apex_usuario_tipodoc',
  18 => 'apex_clase_tipo',
  19 => 'apex_vinculo_tipo',
  20 => 'apex_nucleo_tipo',
  21 => 'apex_dimension_tipo_perfil',
  22 => 'apex_comparacion',
  23 => 'apex_nota_tipo',
  24 => 'apex_msg_tipo',
  25 => 'apex_mod_datos_dump',
  26 => 'apex_ap_tarea_tipo',
  27 => 'apex_ap_tarea_estado',
  28 => 'apex_ap_tarea_prioridad',
  29 => 'apex_ap_tarea_tema',
  30 => 'apex_tp_tarea_tipo',
  31 => 'apex_objeto_hoja_directiva_ti',
  32 => 'apex_admin_persistencia',
  33 => 'apex_tipo_datos',
  34 => 'apex_objeto_mt_me_tipo_nav',
  35 => 'apex_test_paises',
);
	}

	static function apex_elemento_infra()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
  'dump_order_by' => 'elemento_infra, tabla',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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

	static function apex_log_sistema_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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

	static function apex_grafico()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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

	static function apex_columna_estilo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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

	static function apex_usuario_tipodoc()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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

	static function apex_vinculo_tipo()
	{
		return array (
  'archivo' => 'pgsql_a01_nucleo.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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

	static function apex_comparacion()
	{
		return array (
  'archivo' => 'pgsql_a02_dimensiones.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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

	static function apex_mod_datos_dump()
	{
		return array (
  'archivo' => 'pgsql_a06_mod_datos.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
  'dump_order_by' => 'dump',
  'zona' => 'modelo_datos',
  'desc' => 'Modalidades de dumpeo',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'dump',
    1 => 'descripcion',
  ),
);
	}

	static function apex_ap_tarea_tipo()
	{
		return array (
  'archivo' => 'pgsql_a07_admin_proy.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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
  'dump' => 'proyecto',
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

	static function apex_test_paises()
	{
		return array (
  'archivo' => 'pgsql_a98_test.sql',
  'proyecto' => 'toba',
  'dump' => 'proyecto',
  'dump_order_by' => 'pais',
  'zona' => 'test',
  'desc' => '',
  'historica' => '0',
  'version' => '1.0',
  'columnas' => 
  array (
    0 => 'pais',
    1 => 'nombre',
  ),
);
	}

}
?>