<?php

class toba_mc_comp__1381
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1381,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_catalogo_items',
    'subclase_archivo' => 'catalogos/ci_catalogo_items.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Catalogo Items',
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
    'creacion' => '2005-07-18 10:27:44',
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
    0 => 
    array (
      'evento_id' => 57,
      'identificador' => 'refrescar',
      'etiqueta' => NULL,
      'maneja_datos' => 1,
      'sobre_fila' => 0,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'refrescar.png',
      'en_botonera' => 1,
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => 0,
      'defecto' => 0,
      'grupo' => NULL,
      'accion' => NULL,
      'accion_imphtml_debug' => 0,
      'accion_vinculo_carpeta' => NULL,
      'accion_vinculo_item' => NULL,
      'accion_vinculo_objeto' => NULL,
      'accion_vinculo_popup' => 0,
      'accion_vinculo_popup_param' => NULL,
      'accion_vinculo_celda' => NULL,
      'accion_vinculo_target' => NULL,
      'accion_vinculo_servicio' => NULL,
      'es_seleccion_multiple' => 0,
      'es_autovinculo' => 0,
    ),
    1 => 
    array (
      'evento_id' => 1000389,
      'identificador' => 'asistente',
      'etiqueta' => 'Asistente',
      'maneja_datos' => 1,
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'wizard.png',
      'en_botonera' => 1,
      'ayuda' => 'Ejecuta un asistente que lo guiará en la creación de una nueva operación a partir de un catálogo de tipos de operación.',
      'ci_predep' => NULL,
      'implicito' => 0,
      'defecto' => 0,
      'grupo' => NULL,
      'accion' => 'V',
      'accion_imphtml_debug' => 0,
      'accion_vinculo_carpeta' => '3392',
      'accion_vinculo_item' => '1000110',
      'accion_vinculo_objeto' => NULL,
      'accion_vinculo_popup' => 0,
      'accion_vinculo_popup_param' => NULL,
      'accion_vinculo_celda' => 'central',
      'accion_vinculo_target' => 'frame_centro',
      'accion_vinculo_servicio' => NULL,
      'es_seleccion_multiple' => 0,
      'es_autovinculo' => 0,
    ),
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => '100%',
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
      'pantalla' => 365,
      'identificador' => '1',
      'etiqueta' => 'Items',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 1,
      'punto_montaje' => 12,
      'subclase' => 'pant_catalogo_items',
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
  ),
  '_info_obj_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 365,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1381,
      'dep_id' => 43,
      'orden' => 1,
      'identificador_pantalla' => '1',
      'identificador_dep' => 'fotos',
    ),
    1 => 
    array (
      'pantalla' => 365,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1381,
      'dep_id' => 42,
      'orden' => 2,
      'identificador_pantalla' => '1',
      'identificador_dep' => 'filtro',
    ),
    2 => 
    array (
      'pantalla' => 365,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1381,
      'dep_id' => 44,
      'orden' => 3,
      'identificador_pantalla' => '1',
      'identificador_dep' => 'items',
    ),
  ),
  '_info_evt_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 365,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1381,
      'evento_id' => 57,
      'identificador_pantalla' => '1',
      'identificador_evento' => 'refrescar',
    ),
    1 => 
    array (
      'pantalla' => 365,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1381,
      'evento_id' => 1000389,
      'identificador_pantalla' => '1',
      'identificador_evento' => 'asistente',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'filtro',
      'proyecto' => 'toba_editor',
      'objeto' => 1369,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => 'filtro_catalogo_items',
      'subclase_archivo' => 'catalogos/filtro_catalogo_items.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'fotos',
      'proyecto' => 'toba_editor',
      'objeto' => 1383,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => 'cuadro_fotos',
      'subclase_archivo' => 'catalogos/cuadro_fotos.php',
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'items',
      'proyecto' => 'toba_editor',
      'objeto' => 1368,
      'clase' => 'toba_ei_arbol',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_arbol.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}

?>