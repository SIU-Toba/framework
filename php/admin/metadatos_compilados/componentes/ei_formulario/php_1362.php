<?

class php_1362
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1362',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor CUADRO - Col',
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
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_formulario',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_formulario',
    'clase_icono' => 'objetos/ut_formulario.gif',
    'clase_descripcion_corta' => 'Formulario',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '1842',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'cancelar',
      'etiqueta' => '&Cancelar',
      'maneja_datos' => '0',
      'sobre_fila' => '0',
      'confirmacion' => '',
      'estilo' => 'abm-input',
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => '',
      'ci_predep' => NULL,
      'implicito' => '0',
      'grupo' => 'cargado',
    ),
    1 => 
    array (
      'identificador' => 'aceptar',
      'etiqueta' => '&Aceptar',
      'maneja_datos' => '1',
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => '0',
      'grupo' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'modificacion',
      'etiqueta' => NULL,
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
    'ancho' => NULL,
    'ancho_etiqueta' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'formateo',
      'columnas' => 'formateo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'sql: SELECT columna_formato, descripcion_corta 
FROM apex_columna_formato;
no_seteado: NO;',
      'etiqueta' => 'Formateo',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Formateo a aplicar sobre el valor retornado por el query para esta columna',
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'vinculo_indice',
      'columnas' => 'vinculo_indice',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;',
      'etiqueta' => 'ID - Vinculo',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Este indice se utiliza para recuperar un VINCULO del OBJETO (Entre el conjunto de vinculos asociados que puede tener)',
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'no_ordenar',
      'columnas' => 'no_ordenar',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;',
      'etiqueta' => 'NO ordenable',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Desactiva el ordenamiento de la columna.',
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'total',
      'columnas' => 'total',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_info: SI;',
      'etiqueta' => 'Total',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Sumarizar la columna',
      'orden' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'estilo_titulo',
      'columnas' => 'estilo_titulo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 25;
maximo: 100;
estado: lista-col-titulo;',
      'etiqueta' => 'Estilo del título',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Clase CSS a la que se asocia el título de la columna.',
      'orden' => '5',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>