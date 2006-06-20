<?

class php_1613
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1613',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - ei_cuadro - corte',
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
    'creacion' => '2005-09-20 14:32:30',
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
    'filas_agregar' => '1',
    'filas_agregar_online' => '1',
    'filas_ordenar' => '1',
    'filas_numerar' => '1',
    'columna_orden' => 'orden',
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
      'inicializacion' => 'tamano: 15;
maximo: 15;',
      'etiqueta' => 'Identificador',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'descripcion',
      'columnas' => 'descripcion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;
maximo: 30;',
      'etiqueta' => 'Descripcion',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'columnas_id',
      'columnas' => 'columnas_id',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;
maximo: 100;',
      'etiqueta' => 'Columnas CORTE',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indicar la lista de columnas a utilizar separadas por comas.',
      'orden' => '3',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'columnas_descripcion',
      'columnas' => 'columnas_descripcion',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;
maximo: 100;',
      'etiqueta' => 'Columnas DESC.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indicar la lista de columnas a utilizar separadas por comas.',
      'orden' => '4',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'pie_contar_filas',
      'columnas' => 'pie_contar_filas',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;',
      'etiqueta' => 'Contar filas',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Agrega una cuenta de filas al final del grupo.',
      'orden' => '5',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'pie_mostrar_titulos',
      'columnas' => 'pie_mostrar_titulos',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;',
      'etiqueta' => 'Tit. Col.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Cenera una cabecera del grupo para el PIE.',
      'orden' => '6',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    6 => 
    array (
      'identificador' => 'pie_mostrar_titular',
      'columnas' => 'pie_mostrar_titular',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'Cab. Pie',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Agrega los titulos de las columnas a los totales.',
      'orden' => '7',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>