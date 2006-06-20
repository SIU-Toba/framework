<?

class php_1617
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1617',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ci',
    'subclase' => 'ci_comparador',
    'subclase_archivo' => 'admin/utilerias_graficas/comparador_css/ci_comparador.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Comparador de css',
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
    'creacion' => '2005-09-22 11:10:58',
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
      'identificador' => 'refrescar',
      'etiqueta' => '&Refrescar',
      'maneja_datos' => NULL,
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'refrescar.gif',
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => NULL,
    ),
  ),
  'info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => NULL,
    'alto' => NULL,
    'posicion_botonera' => 'ambos',
    'tipo_navegacion' => 'wizard',
    'con_toc' => '1',
  ),
  'info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => '475',
      'identificador' => 'seleccion',
      'etiqueta' => 'Selección',
      'descripcion' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'archivos',
      'eventos' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'pantalla' => '476',
      'identificador' => 'resultado',
      'etiqueta' => 'Resultado',
      'descripcion' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'faltantes,sobrantes',
      'eventos' => 'refrescar',
      'orden' => '2',
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'archivos',
      'proyecto' => 'toba',
      'objeto' => '1618',
      'clase' => 'objeto_ei_formulario',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'faltantes',
      'proyecto' => 'toba',
      'objeto' => '1619',
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'sobrantes',
      'proyecto' => 'toba',
      'objeto' => '1620',
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