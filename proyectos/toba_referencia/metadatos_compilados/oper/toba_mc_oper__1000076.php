<?php

class toba_mc_item__1000076
{
	static function get_metadatos()
	{
		return array (
  'basica' => 
  array (
    'item_proyecto' => 'toba_referencia',
    'item' => '1000076',
    'item_nombre' => 'Componentes',
    'item_descripcion' => NULL,
    'item_act_buffer_proyecto' => NULL,
    'item_act_buffer' => NULL,
    'item_act_patron_proyecto' => NULL,
    'item_act_patron' => NULL,
    'item_act_accion_script' => NULL,
    'item_solic_tipo' => 'web',
    'item_solic_registrar' => 0,
    'item_solic_obs_tipo_proyecto' => NULL,
    'item_solic_obs_tipo' => NULL,
    'item_solic_observacion' => NULL,
    'item_solic_cronometrar' => NULL,
    'item_parametro_a' => NULL,
    'item_parametro_b' => NULL,
    'item_parametro_c' => NULL,
    'item_imagen_recurso_origen' => NULL,
    'item_imagen' => NULL,
    'tipo_pagina_clase' => 'tp_tutorial',
    'tipo_pagina_archivo' => 'tutorial/tp_tutorial.php',
    'item_include_arriba' => NULL,
    'item_include_abajo' => NULL,
    'item_zona_proyecto' => 'toba_referencia',
    'item_zona' => 'zona_tutorial',
    'item_zona_archivo' => 'tutorial/zona_tutorial.php',
    'zona_cons_archivo' => NULL,
    'zona_cons_clase' => NULL,
    'zona_cons_metodo' => NULL,
    'item_publico' => 0,
    'item_existe_ayuda' => NULL,
    'carpeta' => 0,
    'menu' => 1,
    'orden' => '3',
    'publico' => 0,
    'redirecciona' => 0,
    'crono' => NULL,
    'solicitud_tipo' => 'web',
    'item_padre' => '3292',
    'cant_dependencias' => '1',
    'cant_items_hijos' => '0',
  ),
  'objetos' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1000213,
      'objeto_nombre' => 'Tutorial - Componentes',
      'objeto_subclase' => NULL,
      'objeto_subclase_archivo' => NULL,
      'orden' => 0,
      'clase_proyecto' => 'toba',
      'clase' => 'toba_ci',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
      'fuente_proyecto' => NULL,
      'fuente' => NULL,
      'fuente_motor' => NULL,
      'fuente_host' => NULL,
      'fuente_usuario' => NULL,
      'fuente_clave' => NULL,
      'fuente_base' => NULL,
    ),
  ),
);
	}

}

class toba_mc_comp__1000213
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1000213,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Tutorial - Componentes',
    'titulo' => 'Componentes',
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
    'creacion' => '2006-11-21 14:00:39',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/ci',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ci',
    'clase_icono' => 'objetos/multi_etapa.gif',
    'clase_descripcion_corta' => 'ci',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1642',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '0',
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
    'ancho' => '80%',
    'alto' => '80%',
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => 'wizard',
    'con_toc' => 0,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1000064,
      'identificador' => 'agenda',
      'etiqueta' => 'Agenda',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 1,
      'subclase' => 'pant_agenda',
      'subclase_archivo' => 'tutorial/pant_tutorial.php',
    ),
    1 => 
    array (
      'pantalla' => 1000065,
      'identificador' => 'concepto',
      'etiqueta' => '¿Qué es un componente?',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 2,
      'subclase' => 'pant_concepto',
      'subclase_archivo' => 'tutorial/pant_componentes.php',
    ),
    2 => 
    array (
      'pantalla' => 1000078,
      'identificador' => 'tipos',
      'etiqueta' => 'Tipos de Componentes',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 3,
      'subclase' => 'pant_tipos',
      'subclase_archivo' => 'tutorial/pant_componentes.php',
    ),
    3 => 
    array (
      'pantalla' => 1000066,
      'identificador' => 'creacion',
      'etiqueta' => '[Video] Creación de un Componente',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 4,
      'subclase' => 'pant_creacion',
      'subclase_archivo' => 'tutorial/pant_componentes.php',
    ),
    4 => 
    array (
      'pantalla' => 1000079,
      'identificador' => 'extension',
      'etiqueta' => 'Extensión de Componentes',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 5,
      'subclase' => 'pant_extension',
      'subclase_archivo' => 'tutorial/pant_componentes.php',
    ),
    5 => 
    array (
      'pantalla' => 1000080,
      'identificador' => 'video_extension',
      'etiqueta' => '[Video] Definición de una Extensión',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 6,
      'subclase' => 'pant_video_extension',
      'subclase_archivo' => 'tutorial/pant_componentes.php',
    ),
  ),
  '_info_dependencias' => 
  array (
  ),
);
	}

}

?>