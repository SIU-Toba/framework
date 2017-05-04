<?php

class toba_mc_comp__33000009
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 33000009,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Comp. ei_cuadro - columna_total_cc',
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
    'creacion' => '2009-03-18 11:45:58',
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
    'tabla' => 'apex_objeto_cuadro_col_cc',
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
      'objeto' => 33000009,
      'col_id' => 33000004,
      'columna' => 'objeto_cuadro_cc',
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
      'objeto' => 33000009,
      'col_id' => 33000005,
      'columna' => 'objeto_cuadro_proyecto',
      'tipo' => 'C',
      'pk' => 1,
      'secuencia' => '',
      'largo' => 15,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000009,
      'col_id' => 33000006,
      'columna' => 'objeto_cuadro',
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
      'objeto' => 33000009,
      'col_id' => 33000007,
      'columna' => 'objeto_cuadro_col',
      'tipo' => 'E',
      'pk' => 1,
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
      'objeto' => 33000009,
      'col_id' => 33000008,
      'columna' => 'total',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    5 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000009,
      'col_id' => 33000009,
      'columna' => 'identificador',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => 30,
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
      'objeto' => 33000009,
      'externa_id' => 33000001,
      'tipo' => 'sql',
      'sincro_continua' => 1,
      'metodo' => NULL,
      'clase' => NULL,
      'include' => NULL,
      'sql' => 'select identificador 
from apex_objeto_cuadro_cc
where 
objeto_cuadro_proyecto =\'%objeto_cuadro_proyecto%\' AND 
objeto_cuadro = \'%objeto_cuadro%\' AND
objeto_cuadro_cc = \'%objeto_cuadro_cc%\'',
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
      'objeto' => 33000009,
      'externa_id' => 33000001,
      'es_resultado' => 0,
      'columna' => 'objeto_cuadro_cc',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000009,
      'externa_id' => 33000001,
      'es_resultado' => 0,
      'columna' => 'objeto_cuadro_proyecto',
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000009,
      'externa_id' => 33000001,
      'es_resultado' => 0,
      'columna' => 'objeto_cuadro',
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 33000009,
      'externa_id' => 33000001,
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