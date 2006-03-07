<?

class php_275
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '275',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_filtro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'AUDITORIA - Solicitudes',
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
    'creacion' => '2004-04-14 16:17:11',
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
      'dimension' => 'solicitud_tipo',
      'fuente' => 'instancia',
      'nombre' => 'Tipo de Solicitud',
      'descripcion' => 'Tipos de solicitud del sistema',
      'tipo' => 'combo_db',
      'inicializacion' => 'sql: SELECT usuario
FROM apex_log_sistema
WHERE usuario = \'fantasma\';
',
      'etiqueta' => 'Tipo de Solicitud',
      'tabla' => NULL,
      'columna' => 'solicitud_tipo',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
  ),
);
	}

}
?>