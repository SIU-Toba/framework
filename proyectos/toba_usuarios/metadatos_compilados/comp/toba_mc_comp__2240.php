<?php

class toba_mc_comp__2240
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2240,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_sesiones',
    'subclase_archivo' => 'auditoria/sesiones/ci_sesiones.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Log de sesiones',
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
    'creacion' => '2008-04-23 14:28:48',
    'punto_montaje' => 12000004,
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
    'posicion_botonera' => 'abajo',
  ),
  '_info_eventos' => 
  array (
    0 => 
    array (
      'evento_id' => 1024,
      'identificador' => 'volver',
      'etiqueta' => 'Volver',
      'maneja_datos' => 0,
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'deshacer.gif',
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
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => '600px',
    'alto' => '400px',
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => NULL,
    'con_toc' => 0,
    'botonera_barra_item' => NULL,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1154,
      'identificador' => 'listar_sesiones',
      'etiqueta' => 'Sesiones',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 1,
      'punto_montaje' => 12000004,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
    1 => 
    array (
      'pantalla' => 1155,
      'identificador' => 'listar_solicitudes',
      'etiqueta' => 'Solicitudes',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 2,
      'punto_montaje' => 12000004,
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
      'pantalla' => 1154,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2240,
      'dep_id' => 1138,
      'orden' => 1,
      'identificador_pantalla' => 'listar_sesiones',
      'identificador_dep' => 'filtro',
    ),
    1 => 
    array (
      'pantalla' => 1155,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2240,
      'dep_id' => 1140,
      'orden' => 1,
      'identificador_pantalla' => 'listar_solicitudes',
      'identificador_dep' => 'solicitudes',
    ),
    2 => 
    array (
      'pantalla' => 1154,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2240,
      'dep_id' => 1139,
      'orden' => 2,
      'identificador_pantalla' => 'listar_sesiones',
      'identificador_dep' => 'sesiones',
    ),
  ),
  '_info_evt_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1155,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2240,
      'evento_id' => 1024,
      'identificador_pantalla' => 'listar_solicitudes',
      'identificador_evento' => 'volver',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'filtro',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2237,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'sesiones',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2238,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => 'cuadro_sesiones',
      'subclase_archivo' => 'auditoria/sesiones/cuadro_sesiones.php',
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'solicitudes',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2239,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => 'cuadro_solicitudes',
      'subclase_archivo' => 'auditoria/sesiones/cuadro_solicitudes.php',
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}

?>