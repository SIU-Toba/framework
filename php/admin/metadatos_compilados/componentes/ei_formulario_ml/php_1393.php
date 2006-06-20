<?

class php_1393
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1393',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor FORM - EF (ini)',
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
    'creacion' => '2005-07-26 01:25:28',
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
  ),
  'info_formulario' => 
  array (
    'auto_reset' => NULL,
    'scroll' => NULL,
    'ancho' => NULL,
    'alto' => NULL,
    'filas' => NULL,
    'filas_agregar' => NULL,
    'filas_agregar_online' => '1',
    'filas_ordenar' => NULL,
    'filas_numerar' => NULL,
    'columna_orden' => NULL,
    'analisis_cambios' => 'NO',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'clave',
      'columnas' => 'clave',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_oculto',
      'inicializacion' => NULL,
      'etiqueta' => 'Parametro',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'total' => NULL,
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'etiqueta',
      'columnas' => 'etiqueta',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_fijo',
      'inicializacion' => NULL,
      'etiqueta' => 'Parametro',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'total' => NULL,
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'ayuda',
      'columnas' => 'ayuda',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_fijo',
      'inicializacion' => 'sin_datos: 1;',
      'etiqueta' => '?',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'total' => NULL,
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'valor',
      'columnas' => 'valor',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 2;
columnas: 60;
ajustable: 1;',
      'etiqueta' => 'Valor',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'total' => '0',
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>