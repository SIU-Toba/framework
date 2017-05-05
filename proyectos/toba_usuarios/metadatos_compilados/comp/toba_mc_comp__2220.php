<?php

class toba_mc_comp__2220
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2220,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Perfiles de Datos - datos',
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
    'creacion' => '2008-04-10 18:23:01',
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
    'objeto' => 2220,
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
      'objeto' => 2220,
      'asoc_id' => 40,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 2218,
      'padre_id' => 'perfil',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 2222,
      'hijo_id' => 'dims',
      'cascada' => NULL,
      'orden' => '1',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'perfil',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2218,
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
      'identificador' => 'dims',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2222,
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
      'asoc_id' => 40,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2220,
      'hijo_clave' => 763,
      'hijo_objeto' => 2222,
      'col_hija' => 'proyecto',
      'padre_objeto' => 2218,
      'padre_clave' => 755,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 40,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2220,
      'hijo_clave' => 764,
      'hijo_objeto' => 2222,
      'col_hija' => 'usuario_perfil_datos',
      'padre_objeto' => 2218,
      'padre_clave' => 756,
      'col_padre' => 'usuario_perfil_datos',
    ),
  ),
);
	}

}

?>