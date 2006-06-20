<?

class php_478
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '478',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'PLAN - Propiedades basicas',
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
    'creacion' => '2004-07-27 04:55:39',
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
    'ancho' => NULL,
    'ancho_etiqueta' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'objeto_plan_proyecto',
      'columnas' => 'objeto_plan_proyecto',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_oculto_proyecto',
      'inicializacion' => '',
      'etiqueta' => NULL,
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '0',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'objeto_plan',
      'columnas' => 'objeto_plan',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_oculto',
      'inicializacion' => '',
      'etiqueta' => NULL,
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '0.5',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'descripcion',
      'columnas' => 'descripcion',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 7;
columnas: 60;',
      'etiqueta' => 'Descripcion',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>