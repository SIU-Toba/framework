<?php

class toba_mc_comp__10000024
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 10000024,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Puntos de Control',
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
    'creacion' => '2006-12-18 14:25:55',
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
    'objeto' => 10000024,
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
      'objeto' => 10000024,
      'asoc_id' => 10000002,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 10000022,
      'padre_id' => 'apex_ptos_control',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 10000028,
      'hijo_id' => 'apex_ptos_ctrl_param',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 10000024,
      'asoc_id' => 10000004,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 10000022,
      'padre_id' => 'apex_ptos_control',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 10000035,
      'hijo_id' => 'ptos_control_ctrl',
      'cascada' => NULL,
      'orden' => '2',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'ptos_control_ctrl',
      'proyecto' => 'toba_editor',
      'objeto' => 10000035,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'apex_ptos_ctrl_param',
      'proyecto' => 'toba_editor',
      'objeto' => 10000028,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '',
      'parametros_b' => '',
    ),
    2 => 
    array (
      'identificador' => 'apex_ptos_control',
      'proyecto' => 'toba_editor',
      'objeto' => 10000022,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '1',
      'parametros_b' => '1',
    ),
  ),
  '_info_columnas_asoc_rel' => 
  array (
    0 => 
    array (
      'asoc_id' => 10000002,
      'proyecto' => 'toba_editor',
      'objeto' => 10000024,
      'hijo_clave' => 10000007,
      'hijo_objeto' => 10000028,
      'col_hija' => 'proyecto',
      'padre_objeto' => 10000022,
      'padre_clave' => 10000001,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 10000002,
      'proyecto' => 'toba_editor',
      'objeto' => 10000024,
      'hijo_clave' => 10000008,
      'hijo_objeto' => 10000028,
      'col_hija' => 'pto_control',
      'padre_objeto' => 10000022,
      'padre_clave' => 10000002,
      'col_padre' => 'pto_control',
    ),
    2 => 
    array (
      'asoc_id' => 10000004,
      'proyecto' => 'toba_editor',
      'objeto' => 10000024,
      'hijo_clave' => 10000013,
      'hijo_objeto' => 10000035,
      'col_hija' => 'proyecto',
      'padre_objeto' => 10000022,
      'padre_clave' => 10000001,
      'col_padre' => 'proyecto',
    ),
    3 => 
    array (
      'asoc_id' => 10000004,
      'proyecto' => 'toba_editor',
      'objeto' => 10000024,
      'hijo_clave' => 10000014,
      'hijo_objeto' => 10000035,
      'col_hija' => 'pto_control',
      'padre_objeto' => 10000022,
      'padre_clave' => 10000002,
      'col_padre' => 'pto_control',
    ),
  ),
);
	}

}

?>