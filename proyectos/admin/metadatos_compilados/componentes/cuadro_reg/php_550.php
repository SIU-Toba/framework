<?

class php_550
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '550',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_cuadro_reg',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'ITEM - Ayuda',
    'titulo' => 'Ayuda',
    'colapsable' => NULL,
    'descripcion' => 'Muestra la ayuda asociada a un item',
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
    'creacion' => '2004-08-04 12:10:15',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos/editores/cuadro_reg',
    'clase_archivo' => 'nucleo/browser/clases/objeto_cuadro_reg.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos/editores/cuadro_reg',
    'clase_icono' => 'objetos/cuadro2.gif',
    'clase_descripcion_corta' => 'objeto_cuadro_reg',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '/admin/objetos/instanciadores/cuadro_reg',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_cuadro' => 
  array (
    'titulo' => NULL,
    'subtitulo' => NULL,
    'sql' => 'SELECT 
item_proyecto,
item,
descripcion_breve,
descripcion_larga
FROM apex_item_info %w%',
    'columnas_clave' => 'item_proyecto,item',
    'archivos_callbacks' => NULL,
    'ancho' => '90%',
    'ordenar' => NULL,
    'exportar_xls' => NULL,
    'exportar_pdf' => NULL,
    'paginar' => NULL,
    'tamano_pagina' => NULL,
    'eof_invisible' => NULL,
    'eof_customizado' => 'No hay ayuda disponible.',
    'pdf_respetar_paginacion' => NULL,
    'pdf_propiedades' => NULL,
    'asociacion_columnas' => NULL,
  ),
  'info_cuadro_columna' => 
  array (
    0 => 
    array (
      'orden' => '1',
      'titulo' => 'Descripción',
      'estilo' => 'col-tex-p1',
      'ancho' => NULL,
      'valor_sql' => 'descripcion_breve',
      'valor_sql_formato' => NULL,
      'valor_fijo' => NULL,
      'valor_proceso' => NULL,
      'valor_proceso_parametros' => NULL,
      'vinculo_indice' => NULL,
      'par_dimension_proyecto' => NULL,
      'par_dimension' => NULL,
      'par_tabla' => NULL,
      'par_columna' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
    ),
    1 => 
    array (
      'orden' => '2',
      'titulo' => 'Explicación',
      'estilo' => 'col-tex-p1',
      'ancho' => NULL,
      'valor_sql' => 'descripcion_larga',
      'valor_sql_formato' => NULL,
      'valor_fijo' => NULL,
      'valor_proceso' => NULL,
      'valor_proceso_parametros' => NULL,
      'vinculo_indice' => NULL,
      'par_dimension_proyecto' => NULL,
      'par_dimension' => NULL,
      'par_tabla' => NULL,
      'par_columna' => NULL,
      'no_ordenar' => NULL,
      'mostrar_xls' => NULL,
      'mostrar_pdf' => NULL,
      'pdf_propiedades' => NULL,
      'total' => NULL,
    ),
  ),
);
	}

}
?>