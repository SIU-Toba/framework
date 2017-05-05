<?php

class toba_mc_comp__1752
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1752,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Comp. ei_esquema',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => 'toba_editor',
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
    'creacion' => '2005-11-28 16:17:57',
    'punto_montaje' => 12,
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
    'ap_clase' => 'ap_relacion_objeto',
    'ap_archivo' => 'db/ap_relacion_objeto.php',
    'ap_punto_montaje' => 12,
    'cant_dependencias' => 4,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1752,
    'debug' => 0,
    'ap' => 3,
    'punto_montaje' => 12,
    'ap_clase' => 'ap_relacion_objeto',
    'ap_archivo' => 'db/ap_relacion_objeto.php',
    'sinc_susp_constraints' => 0,
    'sinc_orden_automatico' => 1,
    'sinc_lock_optimista' => 1,
  ),
  '_info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'asoc_id' => 1,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1501,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1753,
      'hijo_id' => 'prop_basicas',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'asoc_id' => 4,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1501,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1505,
      'hijo_id' => 'eventos',
      'cascada' => NULL,
      'orden' => '2',
    ),
    2 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'asoc_id' => 10000005,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1505,
      'padre_id' => 'eventos',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 10000033,
      'hijo_id' => 'puntos_control',
      'cascada' => NULL,
      'orden' => '3',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'base',
      'proyecto' => 'toba_editor',
      'objeto' => 1501,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '1',
      'parametros_b' => '1',
    ),
    1 => 
    array (
      'identificador' => 'prop_basicas',
      'proyecto' => 'toba_editor',
      'objeto' => 1753,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '1',
      'parametros_b' => '1',
    ),
    2 => 
    array (
      'identificador' => 'eventos',
      'proyecto' => 'toba_editor',
      'objeto' => 1505,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => 'odt_eventos',
      'subclase_archivo' => 'db/odt_eventos.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'puntos_control',
      'proyecto' => 'toba_editor',
      'objeto' => 10000033,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
  '_info_columnas_asoc_rel' => 
  array (
    0 => 
    array (
      'asoc_id' => 1,
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'hijo_clave' => 377,
      'hijo_objeto' => 1753,
      'col_hija' => 'objeto_esquema_proyecto',
      'padre_objeto' => 1501,
      'padre_clave' => 21,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 1,
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'hijo_clave' => 378,
      'hijo_objeto' => 1753,
      'col_hija' => 'objeto_esquema',
      'padre_objeto' => 1501,
      'padre_clave' => 22,
      'col_padre' => 'objeto',
    ),
    2 => 
    array (
      'asoc_id' => 4,
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'hijo_clave' => 87,
      'hijo_objeto' => 1505,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1501,
      'padre_clave' => 21,
      'col_padre' => 'proyecto',
    ),
    3 => 
    array (
      'asoc_id' => 4,
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'hijo_clave' => 88,
      'hijo_objeto' => 1505,
      'col_hija' => 'objeto',
      'padre_objeto' => 1501,
      'padre_clave' => 22,
      'col_padre' => 'objeto',
    ),
    4 => 
    array (
      'asoc_id' => 10000005,
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'hijo_clave' => 10000010,
      'hijo_objeto' => 10000033,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1505,
      'padre_clave' => 87,
      'col_padre' => 'proyecto',
    ),
    5 => 
    array (
      'asoc_id' => 10000005,
      'proyecto' => 'toba_editor',
      'objeto' => 1752,
      'hijo_clave' => 10000012,
      'hijo_objeto' => 10000033,
      'col_hija' => 'evento_id',
      'padre_objeto' => 1505,
      'padre_clave' => 335,
      'col_padre' => 'evento_id',
    ),
  ),
);
	}

}

?>