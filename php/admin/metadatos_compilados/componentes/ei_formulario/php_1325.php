<?

class php_1325
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1325',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Editor PHP - Creación de subclase',
    'titulo' => 'Subclase',
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
    'creacion' => '2005-07-05 16:20:36',
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
      'identificador' => 'alta',
      'etiqueta' => 'Crear Subclase',
      'maneja_datos' => '1',
      'sobre_fila' => '0',
      'confirmacion' => '',
      'estilo' => 'abm-input',
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => '',
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => 'no_cargado',
    ),
  ),
  'info_formulario' => 
  array (
    'auto_reset' => NULL,
    'ancho' => NULL,
    'ancho_etiqueta' => '150px',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'basicos',
      'columnas' => 'basicos',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
estado: 1;',
      'etiqueta' => 'Métodos básicos',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Incluye los encabezados redefiniendo los métodos más utilizados.',
      'orden' => '2',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'nivel_comentarios',
      'columnas' => 'nivel_comentarios',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_lista_c',
      'inicializacion' => 'lista:0,No/1,Recomendados/2,Explicativos/3,Detallistas;
predeterminado: 1;',
      'etiqueta' => 'Incluír comentarios',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Junto a cada extensión se pueden incluir comentarios aclaratorios del funcionamiento de cada método.',
      'orden' => '5',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>