<?php

class toba_mc_comp__1804
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1804,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Permisos',
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
    'creacion' => '2006-02-01 10:42:57',
    'punto_montaje' => 12,
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '1000250',
    'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '1000250',
    'clase_icono' => 'objetos/datos_tabla.gif',
    'clase_descripcion_corta' => 'datos_tabla',
    'clase_instanciador_proyecto' => NULL,
    'clase_instanciador_item' => NULL,
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'ap_punto_montaje' => 12,
    'cant_dependencias' => 0,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'tabla' => 'apex_permiso',
    'alias' => NULL,
    'min_registros' => NULL,
    'max_registros' => NULL,
    'ap' => 1,
    'punto_montaje' => 12,
    'ap_sub_clase' => NULL,
    'ap_sub_clase_archivo' => NULL,
    'ap_modificar_claves' => 0,
    'ap_clase' => 'ap_tabla_db_s',
    'ap_clase_archivo' => 'nucleo/componentes/persistencia/toba_ap_tabla_db_s.php',
    'tabla_ext' => NULL,
    'esquema' => NULL,
    'esquema_ext' => NULL,
  ),
  '_info_columnas' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1804,
      'col_id' => 392,
      'columna' => 'permiso',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => '"apex_permiso_seq"',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
      'tabla' => NULL,
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1804,
      'col_id' => 393,
      'columna' => 'proyecto',
      'tipo' => 'C',
      'pk' => 1,
      'secuencia' => NULL,
      'largo' => 15,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
      'tabla' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1804,
      'col_id' => 394,
      'columna' => 'nombre',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 100,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
      'tabla' => NULL,
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1804,
      'col_id' => 395,
      'columna' => 'descripcion',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 255,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
      'tabla' => NULL,
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1804,
      'col_id' => 396,
      'columna' => 'mensaje_particular',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
      'tabla' => NULL,
    ),
  ),
  '_info_externas' => 
  array (
  ),
  '_info_externas_col' => 
  array (
  ),
  '_info_valores_unicos' => 
  array (
  ),
  '_info_fks' => 
  array (
  ),
);
	}

}

?>