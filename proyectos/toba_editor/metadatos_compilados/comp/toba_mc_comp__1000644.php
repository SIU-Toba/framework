<?php

class toba_mc_comp__1000644
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1000644,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Comp. ei_codigo',
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
    'creacion' => '2010-08-18 19:08:47',
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
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'ap_punto_montaje' => 12,
    'cant_dependencias' => 3,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1000644,
    'debug' => 0,
    'ap' => 2,
    'punto_montaje' => 12,
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
      'proyecto' => 'toba_editor',
      'objeto' => 1000644,
      'asoc_id' => 1000021,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1501,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1000645,
      'hijo_id' => 'prop_basicas',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1000644,
      'asoc_id' => 12000002,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1501,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1505,
      'hijo_id' => 'eventos',
      'cascada' => NULL,
      'orden' => '2',
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
      'objeto' => 1000645,
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
      'parametros_a' => '0',
      'parametros_b' => '0',
    ),
  ),
  '_info_columnas_asoc_rel' => 
  array (
    0 => 
    array (
      'asoc_id' => 1000021,
      'proyecto' => 'toba_editor',
      'objeto' => 1000644,
      'hijo_clave' => 1000429,
      'hijo_objeto' => 1000645,
      'col_hija' => 'objeto_codigo_proyecto',
      'padre_objeto' => 1501,
      'padre_clave' => 21,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 1000021,
      'proyecto' => 'toba_editor',
      'objeto' => 1000644,
      'hijo_clave' => 1000430,
      'hijo_objeto' => 1000645,
      'col_hija' => 'objeto_codigo',
      'padre_objeto' => 1501,
      'padre_clave' => 22,
      'col_padre' => 'objeto',
    ),
    2 => 
    array (
      'asoc_id' => 12000002,
      'proyecto' => 'toba_editor',
      'objeto' => 1000644,
      'hijo_clave' => 87,
      'hijo_objeto' => 1505,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1501,
      'padre_clave' => 21,
      'col_padre' => 'proyecto',
    ),
    3 => 
    array (
      'asoc_id' => 12000002,
      'proyecto' => 'toba_editor',
      'objeto' => 1000644,
      'hijo_clave' => 88,
      'hijo_objeto' => 1505,
      'col_hija' => 'objeto',
      'padre_objeto' => 1501,
      'padre_clave' => 22,
      'col_padre' => 'objeto',
    ),
  ),
);
	}

}

?>