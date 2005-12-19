<?
//Generador: compilador_proyecto.php

class php_1507
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1507',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - EI ci',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => 'toba',
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
    'creacion' => '2005-08-19 17:28:22',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/db_tablas',
    'clase_archivo' => 'nucleo/persistencia/objeto_datos_relacion.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/db_tablas',
    'clase_icono' => 'objetos/datos_relacion.gif',
    'clase_descripcion_corta' => 'Objeto DATOS - RELACION',
    'clase_instanciador_proyecto' => NULL,
    'clase_instanciador_item' => NULL,
    'objeto_existe_ayuda' => NULL,
  ),
  'info_estructura' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1507',
    'debug' => '0',
    'ap' => '2',
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
  ),
  'info_relaciones' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba',
      'objeto' => '1507',
      'asoc_id' => '1',
      'padre_proyecto' => 'toba',
      'padre_objeto' => '1501',
      'padre_id' => 'base',
      'padre_clave' => 'proyecto,objeto',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => '1502',
      'hijo_id' => 'dependencias',
      'hijo_clave' => 'proyecto,objeto_consumidor',
      'cascada' => '0',
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba',
      'objeto' => '1507',
      'asoc_id' => '2',
      'padre_proyecto' => 'toba',
      'padre_objeto' => '1501',
      'padre_id' => 'base',
      'padre_clave' => 'proyecto,objeto',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => '1505',
      'hijo_id' => 'eventos',
      'hijo_clave' => 'proyecto,objeto',
      'cascada' => '0',
      'orden' => '2',
    ),
    2 => 
    array (
      'proyecto' => 'toba',
      'objeto' => '1507',
      'asoc_id' => '3',
      'padre_proyecto' => 'toba',
      'padre_objeto' => '1501',
      'padre_id' => 'base',
      'padre_clave' => 'proyecto,objeto',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => '1503',
      'hijo_id' => 'prop_basicas',
      'hijo_clave' => 'objeto_mt_me_proyecto,objeto_mt_me',
      'cascada' => '0',
      'orden' => '3',
    ),
    3 => 
    array (
      'proyecto' => 'toba',
      'objeto' => '1507',
      'asoc_id' => '4',
      'padre_proyecto' => 'toba',
      'padre_objeto' => '1503',
      'padre_id' => 'prop_basicas',
      'padre_clave' => 'objeto_mt_me_proyecto,objeto_mt_me',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => '1504',
      'hijo_id' => 'pantallas',
      'hijo_clave' => 'objeto_ci_proyecto,objeto_ci',
      'cascada' => '0',
      'orden' => '4',
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'base',
      'proyecto' => 'toba',
      'objeto' => '1501',
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '1',
      'parametros_b' => '1',
    ),
    1 => 
    array (
      'identificador' => 'dependencias',
      'proyecto' => 'toba',
      'objeto' => '1502',
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '0',
      'parametros_b' => '0',
    ),
    2 => 
    array (
      'identificador' => 'eventos',
      'proyecto' => 'toba',
      'objeto' => '1505',
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_tabla.php',
      'subclase' => 'odt_eventos',
      'subclase_archivo' => 'admin/db/odt_eventos.php',
      'fuente' => 'instancia',
      'parametros_a' => '0',
      'parametros_b' => '0',
    ),
    3 => 
    array (
      'identificador' => 'pantallas',
      'proyecto' => 'toba',
      'objeto' => '1504',
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_tabla.php',
      'subclase' => 'odt_pantallas',
      'subclase_archivo' => 'admin/db/odt_pantallas.php',
      'fuente' => 'instancia',
      'parametros_a' => '1',
      'parametros_b' => '0',
    ),
    4 => 
    array (
      'identificador' => 'prop_basicas',
      'proyecto' => 'toba',
      'objeto' => '1503',
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_tabla.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => '1',
      'parametros_b' => '1',
    ),
  ),
);
	}

}
?>