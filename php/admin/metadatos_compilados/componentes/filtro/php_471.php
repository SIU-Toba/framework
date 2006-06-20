<?

class php_471
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '471',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_filtro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'PROYECTO - Tarea',
    'titulo' => 'Filtrar tarea',
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => 'toba',
    'fuente' => 'instancia',
    'solicitud_registrar' => NULL,
    'solicitud_obj_obs_tipo' => NULL,
    'solicitud_obj_observacion' => NULL,
    'parametro_a' => NULL,
    'parametro_b' => NULL,
    'parametro_c' => NULL,
    'parametro_d' => NULL,
    'parametro_e' => NULL,
    'parametro_f' => NULL,
    'usuario' => NULL,
    'creacion' => '2004-07-22 03:43:45',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos/editores/filtro',
    'clase_archivo' => 'nucleo/browser/clases/objeto_filtro.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos/editores/filtro',
    'clase_icono' => 'objetos/filtro.gif',
    'clase_descripcion_corta' => 'FILTRO',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '/admin/objetos/instanciadores/filtro',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_dimensiones' => 
  array (
    0 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'version',
      'fuente' => 'instancia',
      'nombre' => 'Version',
      'descripcion' => 'Version del proyecto',
      'tipo' => 'combo_db_proyecto',
      'inicializacion' => 'sql: SELECT proyecto, version, version
FROM apex_ap_version %w%;
columna_proyecto: proyecto;
no_seteado: Todas;
',
      'etiqueta' => NULL,
      'tabla' => NULL,
      'columna' => 't.version_proyecto %-% t.version',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
    1 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'tarea_estado',
      'fuente' => 'instancia',
      'nombre' => 'Estado',
      'descripcion' => 'Estado de la tarea',
      'tipo' => 'combo_db',
      'inicializacion' => 'sql: SELECT tarea_estado, descripcion
FROM apex_ap_tarea_estado;
no_seteado: Todas;',
      'etiqueta' => NULL,
      'tabla' => NULL,
      'columna' => 't.tarea_estado',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
    2 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'tarea_prioridad',
      'fuente' => 'instancia',
      'nombre' => 'Prioridad',
      'descripcion' => 'Prioridad de la tarea',
      'tipo' => 'combo_db',
      'inicializacion' => 'sql: SELECT tarea_prioridad, descripcion
FROM apex_ap_tarea_prioridad;
no_seteado: Todas;',
      'etiqueta' => NULL,
      'tabla' => NULL,
      'columna' => 't.tarea_prioridad',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
    3 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'tarea_tema',
      'fuente' => 'instancia',
      'nombre' => 'Tema',
      'descripcion' => 'Tema',
      'tipo' => 'combo_db',
      'inicializacion' => 'sql: SELECT tarea_tema, descripcion
FROM apex_ap_tarea_tema;
no_seteado: Todos;',
      'etiqueta' => NULL,
      'tabla' => NULL,
      'columna' => 't.tarea_tema',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
    4 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'tarea_tipo',
      'fuente' => 'instancia',
      'nombre' => 'Tipo',
      'descripcion' => 'Tipo de tarea',
      'tipo' => 'combo_db',
      'inicializacion' => 'sql: SELECT tarea_tipo, descripcion
FROM apex_ap_tarea_tipo;
no_seteado: Todos;',
      'etiqueta' => NULL,
      'tabla' => NULL,
      'columna' => 't.tarea_tipo',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
  ),
);
	}

}
?>