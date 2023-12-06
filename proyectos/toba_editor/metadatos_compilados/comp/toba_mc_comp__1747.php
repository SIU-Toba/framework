<?php

class toba_mc_comp__1747
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1747,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_eventos',
    'subclase_archivo' => 'objetos_toba/ci_eventos.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Editor OBJETO - (eventos FORM)',
    'titulo' => NULL,
    'colapsable' => 0,
    'descripcion' => NULL,
    'fuente_proyecto' => NULL,
    'fuente' => NULL,
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
    'creacion' => '2005-11-22 10:29:48',
    'punto_montaje' => 12,
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '1000249',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '1000249',
    'clase_icono' => 'objetos/multi_etapa.gif',
    'clase_descripcion_corta' => 'ci',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1642',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'ap_punto_montaje' => NULL,
    'cant_dependencias' => 3,
    'posicion_botonera' => 'arriba',
  ),
  '_info_eventos' => 
  array (
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => NULL,
    'alto' => NULL,
    'posicion_botonera' => 'arriba',
    'tipo_navegacion' => NULL,
    'con_toc' => 0,
    'botonera_barra_item' => NULL,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 956,
      'identificador' => '1',
      'etiqueta' => 'Mostrar',
      'descripcion' => 'Definir los [wiki:Referencia/Eventos eventos] disponibles en el componente.',
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 1,
      'punto_montaje' => 12,
      'subclase' => 'pant_eventos',
      'subclase_archivo' => 'objetos_toba/pant_eventos.php',
      'template' => NULL,
      'template_impresion' => NULL,
    ),
  ),
  '_info_obj_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 956,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1747,
      'dep_id' => 718,
      'orden' => 1,
      'identificador_pantalla' => '1',
      'identificador_dep' => 'eventos_lista',
    ),
    1 => 
    array (
      'pantalla' => 956,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1747,
      'dep_id' => 712,
      'orden' => 2,
      'identificador_pantalla' => '1',
      'identificador_dep' => 'eventos',
    ),
    2 => 
    array (
      'pantalla' => 956,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1747,
      'dep_id' => 713,
      'orden' => 3,
      'identificador_pantalla' => '1',
      'identificador_dep' => 'generador',
    ),
  ),
  '_info_evt_pantalla' => 
  array (
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'eventos',
      'proyecto' => 'toba_editor',
      'objeto' => 1366,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => 'eiform_eventos',
      'subclase_archivo' => 'objetos_toba/eiform_eventos.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'eventos_lista',
      'proyecto' => 'toba_editor',
      'objeto' => 1744,
      'clase' => 'toba_ei_formulario_ml',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario_ml.php',
      'subclase' => 'eiform_abm_detalle',
      'subclase_archivo' => 'objetos_toba/eiform_abm_detalle.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'generador',
      'proyecto' => 'toba_editor',
      'objeto' => 1719,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
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