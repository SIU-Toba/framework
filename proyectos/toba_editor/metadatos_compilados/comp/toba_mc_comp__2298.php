<?php

class toba_mc_comp__2298
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 2298,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'DT - apex_objeto_dep_consumo',
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
    'creacion' => '2009-07-14 04:50:36',
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
    'tabla' => 'apex_objeto_dep_consumo',
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
      'objeto' => 2298,
      'col_id' => 799,
      'columna' => 'proyecto',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => '',
      'largo' => 15,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 800,
      'columna' => 'consumo_id',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => '"apex_objeto_dep_consumo_seq"',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 801,
      'columna' => 'objeto_consumidor',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => '',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 802,
      'columna' => 'objeto_proveedor',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => '',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 803,
      'columna' => 'identificador',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => '',
      'largo' => 40,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    5 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 804,
      'columna' => 'parametros_a',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => '',
      'largo' => 255,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    6 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 805,
      'columna' => 'parametros_b',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => '',
      'largo' => 255,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    7 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 806,
      'columna' => 'parametros_c',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => '',
      'largo' => 255,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    8 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 807,
      'columna' => 'inicializar',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => '',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    9 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 808,
      'columna' => 'clase',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
      'tabla' => NULL,
    ),
    10 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 809,
      'columna' => 'nombre_objeto',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
      'tabla' => NULL,
    ),
    11 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'col_id' => 810,
      'columna' => 'descripcion',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
      'tabla' => NULL,
    ),
  ),
  '_info_externas' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'externa_id' => 8,
      'tipo' => 'dao',
      'sincro_continua' => 1,
      'metodo' => 'get_info_dependencia',
      'clase' => 'toba_info_editores',
      'include' => 'modelo/info/toba_info_editores.php',
      'sql' => NULL,
      'dato_estricto' => 0,
      'carga_dt' => NULL,
      'carga_consulta_php' => NULL,
      'permite_carga_masiva' => 0,
      'metodo_masivo' => NULL,
    ),
  ),
  '_info_externas_col' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'externa_id' => 8,
      'es_resultado' => 0,
      'columna' => 'proyecto',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'externa_id' => 8,
      'es_resultado' => 0,
      'columna' => 'objeto_proveedor',
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'externa_id' => 8,
      'es_resultado' => 1,
      'columna' => 'clase',
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'externa_id' => 8,
      'es_resultado' => 1,
      'columna' => 'nombre_objeto',
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 2298,
      'externa_id' => 8,
      'es_resultado' => 1,
      'columna' => 'descripcion',
    ),
  ),
  '_info_valores_unicos' => 
  array (
    0 => 
    array (
      'columnas' => 'identificador',
    ),
    1 => 
    array (
      'columnas' => 'proyecto,objeto_proveedor',
    ),
  ),
  '_info_fks' => 
  array (
  ),
);
	}

}

?>