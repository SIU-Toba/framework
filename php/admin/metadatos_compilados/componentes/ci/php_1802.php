<?

class php_1802
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1802',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ci',
    'subclase' => 'ci_navegacion',
    'subclase_archivo' => 'admin/editores/editor_permisos/ci_navegacion.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Permisos personalizados',
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
    'creacion' => '2006-02-01 10:32:11',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/ci',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ci.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ci',
    'clase_icono' => 'objetos/multi_etapa.gif',
    'clase_descripcion_corta' => 'Controlador de Interface',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '1642',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'eliminar',
      'etiqueta' => '&Eliminar',
      'maneja_datos' => '0',
      'sobre_fila' => NULL,
      'confirmacion' => '¿Desea eliminar el permiso?',
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'borrar.gif',
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => '0',
      'grupo' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'guardar',
      'etiqueta' => '&Guardar',
      'maneja_datos' => '1',
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'guardar.gif',
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => '0',
      'grupo' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'cancelar',
      'etiqueta' => '&Cancelar',
      'maneja_datos' => '0',
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
    3 => 
    array (
      'identificador' => 'agregar',
      'etiqueta' => '&Agregar Permiso',
      'maneja_datos' => '0',
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
  ),
  'info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => '550px',
    'alto' => '400px',
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => NULL,
    'con_toc' => NULL,
  ),
  'info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => '976',
      'identificador' => 'seleccion',
      'etiqueta' => 'Selección',
      'descripcion' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'filtro,listado',
      'eventos' => 'agregar',
      'orden' => '1',
    ),
    1 => 
    array (
      'pantalla' => '977',
      'identificador' => 'edicion',
      'etiqueta' => 'Edición',
      'descripcion' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'editor',
      'eventos' => 'cancelar,guardar,eliminar',
      'orden' => '2',
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'datos',
      'proyecto' => 'toba',
      'objeto' => '1803',
      'clase' => 'objeto_datos_relacion',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_relacion.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'editor',
      'proyecto' => 'toba',
      'objeto' => '1806',
      'clase' => 'objeto_ci',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ci.php',
      'subclase' => 'ci_edicion',
      'subclase_archivo' => 'admin/editores/editor_permisos/ci_edicion.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'filtro',
      'proyecto' => 'toba',
      'objeto' => '1809',
      'clase' => 'objeto_ei_filtro',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_filtro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'listado',
      'proyecto' => 'toba',
      'objeto' => '1805',
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}
?>