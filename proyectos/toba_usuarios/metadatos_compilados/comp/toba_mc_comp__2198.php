<?php

class toba_mc_comp__2198
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2198,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_editor_perfiles',
    'subclase_archivo' => 'perfiles/perfil_funcional/ci_editor_perfiles.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Editor de Perfil Funcional',
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
    'creacion' => '2008-03-17 16:13:37',
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
    'cant_dependencias' => 4,
    'posicion_botonera' => 'ambos',
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
    'ancho' => '100%',
    'alto' => NULL,
    'posicion_botonera' => 'ambos',
    'tipo_navegacion' => 'tab_h',
    'con_toc' => 0,
    'botonera_barra_item' => 0,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1140,
      'identificador' => 'items_accesibles',
      'etiqueta' => 'Operaciones Accesibles',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
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
      'pantalla' => 1141,
      'identificador' => 'restricciones',
      'etiqueta' => 'Restricciones Funcionales',
      'descripcion' => 'Tildar aquellas restricciones que aplican a este perfil',
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
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
    2 => 
    array (
      'pantalla' => 1142,
      'identificador' => 'permisos',
      'etiqueta' => 'Derechos',
      'descripcion' => 'Tildar aquellos derechos que aplican a este perfil',
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 3,
      'punto_montaje' => 12000004,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
    3 => 
    array (
      'pantalla' => 30000043,
      'identificador' => 'membresia',
      'etiqueta' => 'Membresía',
      'descripcion' => 'Tildar aquellos perfiles de los que este perfil es miembro',
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 4,
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
      'pantalla' => 1140,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2198,
      'dep_id' => 1108,
      'orden' => 1,
      'identificador_pantalla' => 'items_accesibles',
      'identificador_dep' => 'arbol_perfiles',
    ),
    1 => 
    array (
      'pantalla' => 1141,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2198,
      'dep_id' => 1115,
      'orden' => 1,
      'identificador_pantalla' => 'restricciones',
      'identificador_dep' => 'form_restricciones',
    ),
    2 => 
    array (
      'pantalla' => 1142,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2198,
      'dep_id' => 1116,
      'orden' => 1,
      'identificador_pantalla' => 'permisos',
      'identificador_dep' => 'form_permisos',
    ),
    3 => 
    array (
      'pantalla' => 30000043,
      'proyecto' => 'toba_usuarios',
      'objeto_ci' => 2198,
      'dep_id' => 30000054,
      'orden' => NULL,
      'identificador_pantalla' => 'membresia',
      'identificador_dep' => 'form_membresia',
    ),
  ),
  '_info_evt_pantalla' => 
  array (
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'arbol_perfiles',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2176,
      'clase' => 'toba_ei_arbol',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_arbol.php',
      'subclase' => 'arbol_perfiles_funcionales',
      'subclase_archivo' => 'perfiles/perfil_funcional/arbol_perfiles_funcionales.php',
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'form_membresia',
      'proyecto' => 'toba_usuarios',
      'objeto' => 30000108,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'form_permisos',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2208,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'form_restricciones',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2207,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}

?>