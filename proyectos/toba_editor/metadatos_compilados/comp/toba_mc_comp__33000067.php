<?php

class toba_mc_comp__33000067
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 33000067,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Fuente de Datos - datos',
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
    'creacion' => '2011-11-23 10:55:56',
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
    'cant_dependencias' => 2,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 33000067,
    'debug' => 0,
    'ap' => 2,
    'punto_montaje' => 12,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'sinc_susp_constraints' => 1,
    'sinc_orden_automatico' => 1,
    'sinc_lock_optimista' => 1,
  ),
  '_info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 33000067,
      'asoc_id' => 33000012,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1833,
      'padre_id' => 'fuente',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 33000068,
      'hijo_id' => 'esquemas',
      'cascada' => NULL,
      'orden' => '1',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'fuente',
      'proyecto' => 'toba_editor',
      'objeto' => 1833,
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
      'identificador' => 'esquemas',
      'proyecto' => 'toba_editor',
      'objeto' => 33000068,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '',
      'parametros_b' => '',
    ),
  ),
  '_info_columnas_asoc_rel' => 
  array (
    0 => 
    array (
      'asoc_id' => 33000012,
      'proyecto' => 'toba_editor',
      'objeto' => 33000067,
      'hijo_clave' => 33000067,
      'hijo_objeto' => 33000068,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1833,
      'padre_clave' => 461,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 33000012,
      'proyecto' => 'toba_editor',
      'objeto' => 33000067,
      'hijo_clave' => 33000068,
      'hijo_objeto' => 33000068,
      'col_hija' => 'fuente_datos',
      'padre_objeto' => 1833,
      'padre_clave' => 462,
      'col_padre' => 'fuente_datos',
    ),
  ),
);
	}

}

?>