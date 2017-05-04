<?php

class toba_mc_comp__2191
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2191,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Usuario - editar - editor - datos',
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
    'creacion' => '2008-02-26 15:55:03',
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
    'cant_dependencias' => 4,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2191,
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
      'objeto' => 2191,
      'asoc_id' => 37,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 2182,
      'padre_id' => 'basica',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 2183,
      'hijo_id' => 'proyecto',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_usuarios',
      'objeto' => 2191,
      'asoc_id' => 42,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 2182,
      'padre_id' => 'basica',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 2260,
      'hijo_id' => 'proyecto_pd',
      'cascada' => NULL,
      'orden' => '2',
    ),
    2 => 
    array (
      'proyecto' => 'toba_usuarios',
      'objeto' => 2191,
      'asoc_id' => 33000011,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 2182,
      'padre_id' => 'basica',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 33000064,
      'hijo_id' => 'pregunta_secreta',
      'cascada' => NULL,
      'orden' => '3',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'basica',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2182,
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
      'identificador' => 'proyecto',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'proyecto_pd',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'pregunta_secreta',
      'proyecto' => 'toba_usuarios',
      'objeto' => 33000064,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => '0',
      'parametros_b' => '0',
    ),
  ),
  '_info_columnas_asoc_rel' => 
  array (
    0 => 
    array (
      'asoc_id' => 37,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2191,
      'hijo_clave' => 727,
      'hijo_objeto' => 2183,
      'col_hija' => 'usuario',
      'padre_objeto' => 2182,
      'padre_clave' => 721,
      'col_padre' => 'usuario',
    ),
    1 => 
    array (
      'asoc_id' => 42,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2191,
      'hijo_clave' => 787,
      'hijo_objeto' => 2260,
      'col_hija' => 'usuario',
      'padre_objeto' => 2182,
      'padre_clave' => 721,
      'col_padre' => 'usuario',
    ),
    2 => 
    array (
      'asoc_id' => 33000011,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2191,
      'hijo_clave' => 33000059,
      'hijo_objeto' => 33000064,
      'col_hija' => 'usuario',
      'padre_objeto' => 2182,
      'padre_clave' => 721,
      'col_padre' => 'usuario',
    ),
  ),
);
	}

}

?>