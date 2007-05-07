<?php

class toba_mc_comp__1732
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1732,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'ABM Personas',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => 'toba_referencia',
    'fuente' => 'toba_referencia',
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
    'creacion' => '2005-11-15 03:03:56',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/db_tablas',
    'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_relacion.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/db_tablas',
    'clase_icono' => 'objetos/datos_relacion.gif',
    'clase_descripcion_corta' => 'datos_relacion',
    'clase_instanciador_proyecto' => NULL,
    'clase_instanciador_item' => NULL,
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '3',
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1732,
    'debug' => 0,
    'ap' => 2,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
  ),
  '_info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_referencia',
      'objeto' => 1732,
      'asoc_id' => 1,
      'padre_proyecto' => 'toba_referencia',
      'padre_objeto' => 1733,
      'padre_id' => 'persona',
      'padre_clave' => 'id',
      'hijo_proyecto' => 'toba_referencia',
      'hijo_objeto' => 1734,
      'hijo_id' => 'juegos',
      'hijo_clave' => 'persona',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_referencia',
      'objeto' => 1732,
      'asoc_id' => 2,
      'padre_proyecto' => 'toba_referencia',
      'padre_objeto' => 1733,
      'padre_id' => 'persona',
      'padre_clave' => 'id',
      'hijo_proyecto' => 'toba_referencia',
      'hijo_objeto' => 1735,
      'hijo_id' => 'deportes',
      'hijo_clave' => 'persona',
      'cascada' => NULL,
      'orden' => '2',
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'deportes',
      'proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_referencia',
      'parametros_a' => '0',
      'parametros_b' => '0',
    ),
    1 => 
    array (
      'identificador' => 'juegos',
      'proyecto' => 'toba_referencia',
      'objeto' => 1734,
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_referencia',
      'parametros_a' => '1',
      'parametros_b' => '2',
    ),
    2 => 
    array (
      'identificador' => 'persona',
      'proyecto' => 'toba_referencia',
      'objeto' => 1733,
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_referencia',
      'parametros_a' => '1',
      'parametros_b' => '1',
    ),
  ),
);
	}

}

?>