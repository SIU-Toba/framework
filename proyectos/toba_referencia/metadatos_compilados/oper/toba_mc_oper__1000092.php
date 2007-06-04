<?php

class toba_mc_item__1000092
{
	static function get_metadatos()
	{
		return array (
  'basica' => 
  array (
    'item_proyecto' => 'toba_referencia',
    'item' => '1000092',
    'item_nombre' => 'Formularios',
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
    'orden' => '6',
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
      'objeto' => 1000225,
      'objeto_nombre' => 'Formularios',
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

class toba_mc_comp__1000225
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1000225,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Formularios',
    'titulo' => 'Formularios',
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
    'creacion' => '2006-12-07 10:55:11',
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
    'alto' => NULL,
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => 'wizard',
    'con_toc' => 0,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1000105,
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
      'pantalla' => 1000106,
      'identificador' => 'introduccion',
      'etiqueta' => 'Introducción',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 2,
      'subclase' => 'pant_introduccion',
      'subclase_archivo' => 'tutorial/pant_formularios.php',
    ),
    2 => 
    array (
      'pantalla' => 1000107,
      'identificador' => 'tipos',
      'etiqueta' => 'Tipos de Efs',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 3,
      'subclase' => 'pant_tipos',
      'subclase_archivo' => 'tutorial/pant_formularios.php',
    ),
    3 => 
    array (
      'pantalla' => 1000109,
      'identificador' => 'opciones',
      'etiqueta' => 'Carga de opciones',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 4,
      'subclase' => 'pant_opciones',
      'subclase_archivo' => 'tutorial/pant_formularios.php',
    ),
    4 => 
    array (
      'pantalla' => 1000114,
      'identificador' => 'ml',
      'etiqueta' => 'Formulario ML',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 5,
      'subclase' => 'pant_ml',
      'subclase_archivo' => 'tutorial/pant_formularios.php',
    ),
    5 => 
    array (
      'pantalla' => 1000111,
      'identificador' => 'mas_info',
      'etiqueta' => 'Más Info',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 6,
      'subclase' => 'pant_masinfo',
      'subclase_archivo' => 'tutorial/pant_formularios.php',
    ),
  ),
  '_info_dependencias' => 
  array (
  ),
);
	}

}

?>