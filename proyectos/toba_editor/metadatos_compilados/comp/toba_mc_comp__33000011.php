<?php

class toba_mc_comp__33000011
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 33000011,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Comp. ci - eventos_pantalla',
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
    'creacion' => '2009-03-26 11:54:11',
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
    'tabla' => 'apex_eventos_pantalla',
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
      'objeto' => 33000011,
      'col_id' => 33000016,
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
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000011,
      'col_id' => 33000017,
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
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000011,
      'col_id' => 33000018,
      'columna' => 'evento_id',
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
      'objeto' => 33000011,
      'col_id' => 33000019,
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
    4 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000011,
      'col_id' => 33000020,
      'columna' => 'identificador',
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
      'objeto' => 33000011,
      'externa_id' => 33000003,
      'tipo' => 'sql',
      'sincro_continua' => 1,
      'metodo' => NULL,
      'clase' => NULL,
      'include' => NULL,
      'sql' => 'SELECT identificador 
FROM  apex_objeto_eventos
WHERE proyecto = \'%proyecto%\' AND
objeto = \'%objeto_ci%\' AND
evento_id = \'%evento_id%\'',
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
      'objeto' => 33000011,
      'externa_id' => 33000003,
      'es_resultado' => 0,
      'columna' => 'objeto_ci',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000011,
      'externa_id' => 33000003,
      'es_resultado' => 0,
      'columna' => 'evento_id',
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000011,
      'externa_id' => 33000003,
      'es_resultado' => 0,
      'columna' => 'proyecto',
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000011,
      'externa_id' => 33000003,
      'es_resultado' => 1,
      'columna' => 'identificador',
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