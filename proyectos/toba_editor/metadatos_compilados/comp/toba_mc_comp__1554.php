<?php

class toba_mc_comp__1554
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1554,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Objeto - El item',
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
    'creacion' => '2005-09-06 16:33:27',
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
    'ap_clase' => 'ap_relacion_item',
    'ap_archivo' => 'db/ap_relacion_item.php',
    'cant_dependencias' => '3',
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1554,
    'debug' => 0,
    'ap' => 3,
    'ap_clase' => 'ap_relacion_item',
    'ap_archivo' => 'db/ap_relacion_item.php',
  ),
  '_info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'asoc_id' => 1,
      'padre_proyecto' => 'toba',
      'padre_objeto' => 1553,
      'padre_id' => 'base',
      'padre_clave' => 'item,proyecto',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => 1558,
      'hijo_id' => 'objetos',
      'hijo_clave' => 'item,proyecto',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'asoc_id' => 2,
      'padre_proyecto' => 'toba',
      'padre_objeto' => 1553,
      'padre_id' => 'base',
      'padre_clave' => 'item,proyecto',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => 1555,
      'hijo_id' => 'permisos',
      'hijo_clave' => 'item,proyecto',
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
      'objeto' => 1553,
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
      'identificador' => 'objetos',
      'proyecto' => 'toba_editor',
      'objeto' => 1558,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '0',
      'parametros_b' => '0',
    ),
    2 => 
    array (
      'identificador' => 'permisos',
      'proyecto' => 'toba_editor',
      'objeto' => 1555,
      'clase' => 'toba_datos_tabla',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '0',
      'parametros_b' => '0',
    ),
  ),
);
	}

}

?>