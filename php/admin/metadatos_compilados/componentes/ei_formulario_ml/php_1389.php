<?

class php_1389
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1389',
    'anterior' => NULL,
    'reflexivo' => '1',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => 'eiform_abm_detalle',
    'subclase_archivo' => 'admin/objetos_toba/eiform_abm_detalle.php',
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor FORM - EF (lista)',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'En esta interface se editan las propiedades de los elementos de formulario.',
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
    'auto_reset' => '1',
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
      'identificador' => 'identificador',
      'columnas' => 'identificador',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;
maximo: 20;',
      'etiqueta' => 'Identificador',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'ID del EF',
      'orden' => '0.75',
      'total' => NULL,
      'columna_estilo' => '0',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'etiqueta',
      'columnas' => 'etiqueta',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 30;
maximo: 80;',
      'etiqueta' => 'Etiqueta',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Etiqueta para el formulario',
      'orden' => '1',
      'total' => NULL,
      'columna_estilo' => '0',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'obligatorio',
      'columnas' => 'obligatorio',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor:1;
valor_info:SI;',
      'etiqueta' => 'Oblig.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indicar si el campo es obligatorio',
      'orden' => '1.7',
      'total' => NULL,
      'columna_estilo' => '0',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'elemento_formulario',
      'columnas' => 'elemento_formulario',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'sql: SELECT elemento_formulario, elemento_formulario, descripcion, parametros 
FROM apex_elemento_formulario
ORDER BY 2;',
      'etiqueta' => 'Tipo de elemento',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Tipo de elemento de formulario',
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