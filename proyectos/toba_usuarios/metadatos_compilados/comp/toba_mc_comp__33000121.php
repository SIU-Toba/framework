<?php

class toba_mc_comp__33000121
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 33000121,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Generador de Menus - datos',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => 'toba_usuarios',
    'fuente' => 'toba_usuarios',
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
    'creacion' => '2013-11-12 16:37:05',
    'punto_montaje' => 12000004,
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '1000251',
    'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_relacion.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '1000251',
    'clase_icono' => 'objetos/datos_relacion.gif',
    'clase_descripcion_corta' => 'datos_relacion',
    'clase_instanciador_proyecto' => NULL,
    'clase_instanciador_item' => NULL,
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'ap_punto_montaje' => 12000004,
    'cant_dependencias' => 2,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 33000121,
    'debug' => 0,
    'ap' => 2,
    'punto_montaje' => 12000004,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'sinc_susp_constraints' => 0,
    'sinc_orden_automatico' => 1,
    'sinc_lock_optimista' => 1,
  ),
  '_info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_usuarios',
      'objeto' => 33000121,
      'asoc_id' => 33000013,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 33000119,
      'padre_id' => 'menu',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 33000120,
      'hijo_id' => 'operaciones',
      'cascada' => NULL,
      'orden' => '1',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'menu',
      'proyecto' => 'toba_usuarios',
      'objeto' => 33000119,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => '1',
      'parametros_b' => '1',
    ),
    1 => 
    array (
      'identificador' => 'operaciones',
      'proyecto' => 'toba_usuarios',
      'objeto' => 33000120,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => '1',
      'parametros_b' => NULL,
    ),
  ),
  '_info_columnas_asoc_rel' => 
  array (
    0 => 
    array (
      'asoc_id' => 33000013,
      'proyecto' => 'toba_usuarios',
      'objeto' => 33000121,
      'hijo_clave' => 33000185,
      'hijo_objeto' => 33000120,
      'col_hija' => 'proyecto',
      'padre_objeto' => 33000119,
      'padre_clave' => 33000181,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 33000013,
      'proyecto' => 'toba_usuarios',
      'objeto' => 33000121,
      'hijo_clave' => 33000186,
      'hijo_objeto' => 33000120,
      'col_hija' => 'menu_id',
      'padre_objeto' => 33000119,
      'padre_clave' => 33000182,
      'col_padre' => 'menu_id',
    ),
  ),
);
	}

}

?>