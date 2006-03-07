<?

class php_281
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '281',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_filtro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'ESTAD. - Instanciacion por clase',
    'titulo' => NULL,
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
    'creacion' => '2004-04-23 16:19:57',
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
      'dimension' => 'proyecto',
      'fuente' => 'instancia',
      'nombre' => 'Proyecto',
      'descripcion' => 'Elegir un proyecto',
      'tipo' => 'combo_db',
      'inicializacion' => 'sql: SELECT proyecto, descripcion_corta FROM apex_proyecto;
no_seteado: No Filtrar;',
      'etiqueta' => NULL,
      'tabla' => NULL,
      'columna' => 'proyecto',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
  ),
);
	}

}
?>