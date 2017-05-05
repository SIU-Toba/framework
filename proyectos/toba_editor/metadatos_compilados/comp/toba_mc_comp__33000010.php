<?php

class toba_mc_comp__33000010
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 33000010,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'DT - objetos_pantalla',
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
    'creacion' => '2009-03-23 16:21:31',
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
    'tabla' => 'apex_objetos_pantalla',
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
      'objeto' => 33000010,
      'col_id' => 33000010,
      'columna' => 'proyecto',
      'tipo' => 'C',
      'pk' => 1,
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
      'objeto' => 33000010,
      'col_id' => 33000011,
      'columna' => 'pantalla',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => '',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000010,
      'col_id' => 33000012,
      'columna' => 'objeto_ci',
      'tipo' => 'E',
      'pk' => 1,
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
      'objeto' => 33000010,
      'col_id' => 33000013,
      'columna' => 'orden',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => '',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000010,
      'col_id' => 33000014,
      'columna' => 'dep_id',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => '',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    5 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000010,
      'col_id' => 33000015,
      'columna' => 'dependencia',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => 40,
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
      'objeto' => 33000010,
      'externa_id' => 33000002,
      'tipo' => 'sql',
      'sincro_continua' => 1,
      'metodo' => NULL,
      'clase' => NULL,
      'include' => NULL,
      'sql' => 'SELECT identificador as dependencia
FROM  apex_objeto_dependencias
WHERE
proyecto = \'%proyecto%\' AND objeto_consumidor = \'%objeto_ci%\' AND dep_id = \'%dep_id%\'',
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
      'objeto' => 33000010,
      'externa_id' => 33000002,
      'es_resultado' => 0,
      'columna' => 'proyecto',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000010,
      'externa_id' => 33000002,
      'es_resultado' => 0,
      'columna' => 'objeto_ci',
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000010,
      'externa_id' => 33000002,
      'es_resultado' => 0,
      'columna' => 'dep_id',
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000010,
      'externa_id' => 33000002,
      'es_resultado' => 1,
      'columna' => 'dependencia',
    ),
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