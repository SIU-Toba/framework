<?

class php_1398
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1398',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - DBR - Columnas',
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
    'creacion' => '2005-07-26 23:58:37',
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
      'sobre_fila' => '0',
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
      'identificador' => 'leer_db',
      'etiqueta' => 'Leer Metadatos DB',
      'maneja_datos' => '1',
      'sobre_fila' => '0',
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'fuente.gif',
      'en_botonera' => '1',
      'ayuda' => 'Construye automáticamente los nombres y propiedades de las columnas en base a los metadatos disponibles de la tabla en la base de datos.',
      'ci_predep' => NULL,
      'implicito' => '0',
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
    'filas_agregar' => '1',
    'filas_agregar_online' => '1',
    'filas_ordenar' => NULL,
    'filas_numerar' => NULL,
    'columna_orden' => NULL,
    'analisis_cambios' => 'LINEA',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'columna',
      'columnas' => 'columna',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 25;
maximo: 120;',
      'etiqueta' => 'Columna',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'total' => '0',
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'tipo',
      'columnas' => 'tipo',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'sql: SELECT tipo, descripcion
FROM apex_tipo_datos;
no_seteado: Indefinido;',
      'etiqueta' => 'Tipo',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'total' => NULL,
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'largo',
      'columnas' => 'largo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras:10;',
      'etiqueta' => 'Max.',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'total' => NULL,
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'pk',
      'columnas' => 'pk',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'PK',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'total' => NULL,
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'secuencia',
      'columnas' => 'secuencia',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 15;
maximo: 120;',
      'etiqueta' => 'Secuencia',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5',
      'total' => '0',
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'no_nulo_db',
      'columnas' => 'no_nulo_db',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'NOT NULL',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '7',
      'total' => NULL,
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    6 => 
    array (
      'identificador' => 'externa',
      'columnas' => 'externa',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'Ext.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Una columna externa es una que participa de la tabla por razones cosméticas, generalmente la tabla ya tiene un ID (casi siempre una FK a otra tabla) pero también necesita el campo legible que representa ese ID, que por normalización no está incluído en la tabla.',
      'orden' => '8',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>