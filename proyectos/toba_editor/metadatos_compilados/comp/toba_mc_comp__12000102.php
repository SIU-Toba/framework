<?php

class toba_mc_comp__12000102
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 12000102,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_puntos_montaje',
    'subclase_archivo' => 'configuracion/puntos_montaje/ci_puntos_montaje.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Proyecto - Puntos de Montaje',
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
    'creacion' => '2010-06-28 16:40:52',
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
    'cant_dependencias' => 5,
    'posicion_botonera' => 'abajo',
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
    'ancho' => '500px',
    'alto' => NULL,
    'posicion_botonera' => NULL,
    'tipo_navegacion' => 'wizard',
    'con_toc' => 0,
    'botonera_barra_item' => 0,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 12000085,
      'identificador' => 'tipo_montaje',
      'etiqueta' => 'Agregar punto de montaje',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 1,
      'punto_montaje' => 12,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
    1 => 
    array (
      'pantalla' => 12000086,
      'identificador' => 'proyecto_toba',
      'etiqueta' => 'Punto de montaje proyecto Toba',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 2,
      'punto_montaje' => 12,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
    2 => 
    array (
      'pantalla' => 12000087,
      'identificador' => 'otro',
      'etiqueta' => 'Punto de montaje externo',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 3,
      'punto_montaje' => 12,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
  ),
  '_info_obj_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 12000085,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 12000102,
      'dep_id' => 12000093,
      'orden' => 0,
      'identificador_pantalla' => 'tipo_montaje',
      'identificador_dep' => 'cuadro_puntos_montaje',
    ),
    1 => 
    array (
      'pantalla' => 12000085,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 12000102,
      'dep_id' => 12000090,
      'orden' => 1,
      'identificador_pantalla' => 'tipo_montaje',
      'identificador_dep' => 'form_eleccion_tipo',
    ),
    2 => 
    array (
      'pantalla' => 12000087,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 12000102,
      'dep_id' => 12000096,
      'orden' => NULL,
      'identificador_pantalla' => 'otro',
      'identificador_dep' => 'form_otro',
    ),
    3 => 
    array (
      'pantalla' => 12000086,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 12000102,
      'dep_id' => 12000094,
      'orden' => NULL,
      'identificador_pantalla' => 'proyecto_toba',
      'identificador_dep' => 'form_proyecto',
    ),
  ),
  '_info_evt_pantalla' => 
  array (
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'cuadro_puntos_montaje',
      'proyecto' => 'toba_editor',
      'objeto' => 12000106,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'dt_puntos_montaje',
      'proyecto' => 'toba_editor',
      'objeto' => 12000104,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'form_eleccion_tipo',
      'proyecto' => 'toba_editor',
      'objeto' => 12000103,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'form_otro',
      'proyecto' => 'toba_editor',
      'objeto' => 12000109,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'form_proyecto',
      'proyecto' => 'toba_editor',
      'objeto' => 12000107,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => 'form_proyecto',
      'subclase_archivo' => 'configuracion/puntos_montaje/form_proyecto.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}

?>