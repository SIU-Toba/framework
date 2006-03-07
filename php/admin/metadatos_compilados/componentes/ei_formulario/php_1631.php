<?

class php_1631
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1631',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor FORM - EF (importar)',
    'titulo' => 'Importar DEFINICION',
    'colapsable' => '1',
    'descripcion' => 'Importar la definicion del formulario de un OBJETO_DATOS_TABLA',
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
    'creacion' => '2005-10-04 15:39:03',
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
      'identificador' => 'importar',
      'etiqueta' => 'Importar',
      'maneja_datos' => NULL,
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => NULL,
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
      'identificador' => 'datos_tabla',
      'columnas' => 'datos_tabla',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_lista_objetos_dt;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: objeto;
valor: descripcion;
no_seteado: --- SELECCIONAR ---;',
      'etiqueta' => 'Datos Tabla',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Seleccionar el objeto_datos_tabla que se desea utilizar.',
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'pk',
      'columnas' => 'pk',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;',
      'etiqueta' => 'Incluir PKs',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Indica si se incluyen las PKs en el formulario',
      'orden' => '2',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>