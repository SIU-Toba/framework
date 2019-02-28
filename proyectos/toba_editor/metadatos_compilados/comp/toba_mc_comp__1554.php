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
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Item',
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
    'ap_clase' => 'ap_relacion_item',
    'ap_archivo' => 'db/ap_relacion_item.php',
    'ap_punto_montaje' => 12,
    'cant_dependencias' => 4,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1554,
    'debug' => 0,
    'ap' => 3,
    'punto_montaje' => 12,
    'ap_clase' => 'ap_relacion_item',
    'ap_archivo' => 'db/ap_relacion_item.php',
    'sinc_susp_constraints' => 0,
    'sinc_orden_automatico' => 1,
    'sinc_lock_optimista' => 1,
  ),
  '_info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'asoc_id' => 1,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1553,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1558,
      'hijo_id' => 'objetos',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'asoc_id' => 2,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1553,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 1555,
      'hijo_id' => 'permisos',
      'cascada' => NULL,
      'orden' => '2',
    ),
    2 => 
    array (
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'asoc_id' => 30000001,
      'padre_proyecto' => 'toba_editor',
      'padre_objeto' => 1553,
      'padre_id' => 'base',
      'hijo_proyecto' => 'toba_editor',
      'hijo_objeto' => 30000103,
      'hijo_id' => 'permisos_tablas',
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
    3 => 
    array (
      'identificador' => 'permisos_tablas',
      'proyecto' => 'toba_editor',
      'objeto' => 30000103,
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
      'asoc_id' => 1,
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'hijo_clave' => 269,
      'hijo_objeto' => 1558,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1553,
      'padre_clave' => 229,
      'col_padre' => 'proyecto',
    ),
    1 => 
    array (
      'asoc_id' => 1,
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'hijo_clave' => 270,
      'hijo_objeto' => 1558,
      'col_hija' => 'item',
      'padre_objeto' => 1553,
      'padre_clave' => 230,
      'col_padre' => 'item',
    ),
    2 => 
    array (
      'asoc_id' => 2,
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'hijo_clave' => 266,
      'hijo_objeto' => 1555,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1553,
      'padre_clave' => 229,
      'col_padre' => 'proyecto',
    ),
    3 => 
    array (
      'asoc_id' => 2,
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'hijo_clave' => 268,
      'hijo_objeto' => 1555,
      'col_hija' => 'item',
      'padre_objeto' => 1553,
      'padre_clave' => 230,
      'col_padre' => 'item',
    ),
    4 => 
    array (
      'asoc_id' => 30000001,
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'hijo_clave' => 30000012,
      'hijo_objeto' => 30000103,
      'col_hija' => 'proyecto',
      'padre_objeto' => 1553,
      'padre_clave' => 229,
      'col_padre' => 'proyecto',
    ),
    5 => 
    array (
      'asoc_id' => 30000001,
      'proyecto' => 'toba_editor',
      'objeto' => 1554,
      'hijo_clave' => 30000013,
      'hijo_objeto' => 30000103,
      'col_hija' => 'item',
      'padre_objeto' => 1553,
      'padre_clave' => 230,
      'col_padre' => 'item',
    ),
  ),
);
	}

}

?>