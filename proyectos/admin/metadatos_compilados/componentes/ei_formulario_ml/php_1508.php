<?

class php_1508
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1508',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - DR - Asociar DT',
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
    'creacion' => '2005-08-20 19:38:48',
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
    'ancho' => '400',
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
      'inicializacion' => 'tamano: 20;
maximo: 20;',
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
      'identificador' => 'dependencia',
      'columnas' => 'proyecto, objeto_proveedor',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_lista_objetos_dt;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: proyecto, objeto;
valor: descripcion;',
      'etiqueta' => 'Dependencia',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'min_filas',
      'columnas' => 'parametros_a',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => NULL,
      'etiqueta' => 'Min. Filas',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'max_filas',
      'columnas' => 'parametros_b',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => NULL,
      'etiqueta' => 'Max. Filas',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>