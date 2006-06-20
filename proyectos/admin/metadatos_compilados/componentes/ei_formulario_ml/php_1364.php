<?

class php_1364
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1364',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => 'eiform_abm_detalle',
    'subclase_archivo' => 'admin/objetos_toba/eiform_abm_detalle.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor CUADRO - Col Lista',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'Edita las columnas del cuadro',
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
    'creacion' => NULL,
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_formulario_ml',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario_ml.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_formulario_ml',
    'clase_icono' => 'objetos/ut_formulario_ml.gif',
    'clase_descripcion_corta' => 'EI Formulario Multilinea',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '1842',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'modificacion',
      'etiqueta' => 'Modificacion',
      'maneja_datos' => '1',
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '0',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => '1',
      'grupo' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'seleccion',
      'etiqueta' => NULL,
      'maneja_datos' => '1',
      'sobre_fila' => '1',
      'confirmacion' => '',
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'doc.gif',
      'en_botonera' => '0',
      'ayuda' => 'Seleccionar la fila',
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => NULL,
    ),
  ),
  'info_formulario' => 
  array (
    'auto_reset' => NULL,
    'scroll' => NULL,
    'ancho' => '550',
    'alto' => NULL,
    'filas' => NULL,
    'filas_agregar' => '1',
    'filas_agregar_online' => '1',
    'filas_ordenar' => '1',
    'filas_numerar' => '1',
    'columna_orden' => NULL,
    'analisis_cambios' => 'LINEA',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'clave',
      'columnas' => 'clave',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 30;',
      'etiqueta' => 'Clave',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Codigo que identifica al campo dentro del array asociativo provisto al form para cargar datos.',
      'orden' => '1',
      'total' => NULL,
      'columna_estilo' => '0',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'titulo',
      'columnas' => 'titulo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 30;
maximo: 80;',
      'etiqueta' => 'Título',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define el título de la columna',
      'orden' => '2',
      'total' => '0',
      'columna_estilo' => '0',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'estilo',
      'columnas' => 'estilo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'sql: SELECT columna_estilo,descripcion FROM apex_columna_estilo;',
      'etiqueta' => 'Estilo',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Define el estilo para la columna',
      'orden' => '3',
      'total' => NULL,
      'columna_estilo' => '0',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'ancho',
      'columnas' => 'ancho',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 8;',
      'etiqueta' => 'Ancho',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Ancho de la columna.',
      'orden' => '4',
      'total' => NULL,
      'columna_estilo' => '0',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>