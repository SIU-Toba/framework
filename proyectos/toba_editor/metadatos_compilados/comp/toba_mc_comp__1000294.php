<?php

class toba_mc_comp__1000294
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1000294,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Asistente abms',
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
    'creacion' => '2007-07-19 16:20:34',
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
    'objeto' => 1000294,
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
      'objeto' => 1000294,
      'asoc_id' => 1000015,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1000298,
      'padre_id' => 'molde',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1000295,
      'hijo_id' => 'base',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1000294,
      'asoc_id' => 1000016,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1000295,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1000296,
      'hijo_id' => 'filas',
      'cascada' => NULL,
      'orden' => '2',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'filas',
      'proyecto' => 'toba_editor',
      'objeto' => 1000296,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '',
      'parametros_b' => '',
    ),
    1 => 
    array (
      'identificador' => 'molde',
      'proyecto' => 'toba_editor',
      'objeto' => 1000298,
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
      'identificador' => 'base',
      'proyecto' => 'toba_editor',
      'objeto' => 1000295,
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
      'asoc_id' => 1000015,
      'proyecto' => 'toba_editor',
      'objeto' => 1000294,
      'hijo_clave' => 1000168,
      'hijo_objeto' => 1000295,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1000298,
      'padre_clave' => 1000205,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 1000015,
      'proyecto' => 'toba_editor',
      'objeto' => 1000294,
      'hijo_clave' => 1000169,
      'hijo_objeto' => 1000295,
      'col_hija' => 'molde',
      'padre_objeto' => 1000298,
      'padre_clave' => 1000206,
      'col_padre' => 'molde',
    ),
    2 => 
    array (
      'asoc_id' => 1000016,
      'proyecto' => 'toba_editor',
      'objeto' => 1000294,
      'hijo_clave' => 1000183,
      'hijo_objeto' => 1000296,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1000295,
      'padre_clave' => 1000168,
      'col_padre' => 'proyecto',
    ),
    3 => 
    array (
      'asoc_id' => 1000016,
      'proyecto' => 'toba_editor',
      'objeto' => 1000294,
      'hijo_clave' => 1000184,
      'hijo_objeto' => 1000296,
      'col_hija' => 'molde',
      'padre_objeto' => 1000295,
      'padre_clave' => 1000169,
      'col_padre' => 'molde',
    ),
  ),
);
	}

}

?>