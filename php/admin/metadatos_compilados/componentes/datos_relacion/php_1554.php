<?
//Generador: compilador_proyecto.php

class php_1554
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1554',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_datos_relacion',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Objeto - El item',
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
    'creacion' => '2005-09-06 16:33:27',
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
    'objeto' => '1554',
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
      'objeto' => '1554',
      'asoc_id' => '1',
      'padre_proyecto' => 'toba',
      'padre_objeto' => '1553',
      'padre_id' => 'base',
      'padre_clave' => 'item,proyecto',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => '1558',
      'hijo_id' => 'objetos',
      'hijo_clave' => 'item,proyecto',
      'cascada' => NULL,
      'orden' => '1',
    ),
    1 => 
    array (
      'proyecto' => 'toba',
      'objeto' => '1554',
      'asoc_id' => '2',
      'padre_proyecto' => 'toba',
      'padre_objeto' => '1553',
      'padre_id' => 'base',
      'padre_clave' => 'item,proyecto',
      'hijo_proyecto' => 'toba',
      'hijo_objeto' => '1555',
      'hijo_id' => 'permisos',
      'hijo_clave' => 'item,proyecto',
      'cascada' => NULL,
      'orden' => '2',
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'base',
      'proyecto' => 'toba',
      'objeto' => '1553',
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
      'identificador' => 'objetos',
      'proyecto' => 'toba',
      'objeto' => '1558',
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
      'identificador' => 'permisos',
      'proyecto' => 'toba',
      'objeto' => '1555',
      'clase' => 'objeto_datos_tabla',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_tabla.php',
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