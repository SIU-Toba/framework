<?php

class toba_mc_comp__2202
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2202,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Mantenimiento de Perfiles Funcionales - datos',
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
    'creacion' => '2008-03-17 18:06:43',
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
    'ap_clase' => 'datos_relacion_perfiles',
    'ap_archivo' => 'perfiles/perfil_funcional/datos_relacion_perfiles.php',
    'ap_punto_montaje' => 12000004,
    'cant_dependencias' => 4,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2202,
    'debug' => 0,
    'ap' => 3,
    'punto_montaje' => 12000004,
    'ap_clase' => 'datos_relacion_perfiles',
    'ap_archivo' => 'perfiles/perfil_funcional/datos_relacion_perfiles.php',
    'sinc_susp_constraints' => 0,
    'sinc_orden_automatico' => 1,
    'sinc_lock_optimista' => 1,
  ),
  '_info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'asoc_id' => 38,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 2206,
      'padre_id' => 'accesos',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 2205,
      'hijo_id' => 'permisos',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'asoc_id' => 39,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 2206,
      'padre_id' => 'accesos',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 2204,
      'hijo_id' => 'restricciones',
      'cascada' => NULL,
      'orden' => '2',
    ),
    2 => 
    array (
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'asoc_id' => 30000002,
      'padre_proyecto' => 'toba_usuarios',
      'padre_objeto' => 2206,
      'padre_id' => 'accesos',
      'hijo_proyecto' => 'toba_usuarios',
      'hijo_objeto' => 30000107,
      'hijo_id' => 'membresia',
      'cascada' => NULL,
      'orden' => '3',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'accesos',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2206,
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
      'identificador' => 'permisos',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2205,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => '',
      'parametros_b' => '',
    ),
    2 => 
    array (
      'identificador' => 'restricciones',
      'proyecto' => 'toba_usuarios',
      'objeto' => 2204,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => '',
      'parametros_b' => '',
    ),
    3 => 
    array (
      'identificador' => 'membresia',
      'proyecto' => 'toba_usuarios',
      'objeto' => 30000107,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_usuarios',
      'parametros_a' => '',
      'parametros_b' => '',
    ),
  ),
  '_info_columnas_asoc_rel' => 
  array (
    0 => 
    array (
      'asoc_id' => 38,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'hijo_clave' => 736,
      'hijo_objeto' => 2205,
      'col_hija' => 'proyecto',
      'padre_objeto' => 2206,
      'padre_clave' => 742,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 38,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'hijo_clave' => 737,
      'hijo_objeto' => 2205,
      'col_hija' => 'usuario_grupo_acc',
      'padre_objeto' => 2206,
      'padre_clave' => 743,
      'col_padre' => 'usuario_grupo_acc',
    ),
    2 => 
    array (
      'asoc_id' => 39,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'hijo_clave' => 739,
      'hijo_objeto' => 2204,
      'col_hija' => 'proyecto',
      'padre_objeto' => 2206,
      'padre_clave' => 742,
      'col_padre' => 'proyecto',
    ),
    3 => 
    array (
      'asoc_id' => 39,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'hijo_clave' => 740,
      'hijo_objeto' => 2204,
      'col_hija' => 'usuario_grupo_acc',
      'padre_objeto' => 2206,
      'padre_clave' => 743,
      'col_padre' => 'usuario_grupo_acc',
    ),
    4 => 
    array (
      'asoc_id' => 30000002,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'hijo_clave' => 30000018,
      'hijo_objeto' => 30000107,
      'col_hija' => 'proyecto',
      'padre_objeto' => 2206,
      'padre_clave' => 742,
      'col_padre' => 'proyecto',
    ),
    5 => 
    array (
      'asoc_id' => 30000002,
      'proyecto' => 'toba_usuarios',
      'objeto' => 2202,
      'hijo_clave' => 30000019,
      'hijo_objeto' => 30000107,
      'col_hija' => 'usuario_grupo_acc',
      'padre_objeto' => 2206,
      'padre_clave' => 743,
      'col_padre' => 'usuario_grupo_acc',
    ),
  ),
);
	}

}

?>