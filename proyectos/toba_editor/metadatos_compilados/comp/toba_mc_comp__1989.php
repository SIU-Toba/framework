<?php

class toba_mc_comp__1989
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1989,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_catalogo',
    'subclase_archivo' => 'datos/ci_catalogo.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Catalogo',
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
    'creacion' => '2007-07-19 18:05:08',
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
    'cant_dependencias' => 6,
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
      'pantalla' => 1060,
      'identificador' => 'pant_inicial',
      'etiqueta' => 'Pantalla Inicial',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 1,
      'punto_montaje' => 12,
      'subclase' => 'pantalla_catalogo',
      'subclase_archivo' => 'datos/ci_catalogo.php',
      'template' => NULL,
      'template_impresion' => NULL,
    ),
  ),
  '_info_obj_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1060,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1989,
      'dep_id' => 926,
      'orden' => 1,
      'identificador_pantalla' => 'pant_inicial',
      'identificador_dep' => 'fuentes',
    ),
    1 => 
    array (
      'pantalla' => 1060,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1989,
      'dep_id' => 1116,
      'orden' => 2,
      'identificador_pantalla' => 'pant_inicial',
      'identificador_dep' => 'dimensiones',
    ),
    2 => 
    array (
      'pantalla' => 1060,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1989,
      'dep_id' => 927,
      'orden' => 3,
      'identificador_pantalla' => 'pant_inicial',
      'identificador_dep' => 'consultas',
    ),
    3 => 
    array (
      'pantalla' => 1060,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1989,
      'dep_id' => 928,
      'orden' => 4,
      'identificador_pantalla' => 'pant_inicial',
      'identificador_dep' => 'tablas',
    ),
    4 => 
    array (
      'pantalla' => 1060,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1989,
      'dep_id' => 959,
      'orden' => 5,
      'identificador_pantalla' => 'pant_inicial',
      'identificador_dep' => 'arbol_relaciones',
    ),
    5 => 
    array (
      'pantalla' => 1060,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 1989,
      'dep_id' => 30000067,
      'orden' => 6,
      'identificador_pantalla' => 'pant_inicial',
      'identificador_dep' => 'servicios_web',
    ),
  ),
  '_info_evt_pantalla' => 
  array (
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'arbol_relaciones',
      'proyecto' => 'toba_editor',
      'objeto' => 2013,
      'clase' => 'toba_ei_arbol',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_arbol.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'consultas',
      'proyecto' => 'toba_editor',
      'objeto' => 1991,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'dimensiones',
      'proyecto' => 'toba_editor',
      'objeto' => 2204,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'fuentes',
      'proyecto' => 'toba_editor',
      'objeto' => 1990,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'servicios_web',
      'proyecto' => 'toba_editor',
      'objeto' => 30000122,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'tablas',
      'proyecto' => 'toba_editor',
      'objeto' => 1992,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
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