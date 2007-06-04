<?php

class toba_mc_comp__1735
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1735,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'ABM Personas - Deportes',
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
    'creacion' => '2005-11-15 03:05:50',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/db_registros',
    'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_tabla.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/db_registros',
    'clase_icono' => 'objetos/datos_tabla.gif',
    'clase_descripcion_corta' => 'datos_tabla',
    'clase_instanciador_proyecto' => NULL,
    'clase_instanciador_item' => NULL,
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '0',
  ),
  '_info_estructura' => 
  array (
    'tabla' => 'ref_persona_deportes',
    'alias' => NULL,
    'min_registros' => NULL,
    'max_registros' => NULL,
    'ap' => 1,
    'ap_sub_clase' => NULL,
    'ap_sub_clase_archivo' => NULL,
    'ap_modificar_claves' => 0,
    'ap_clase' => 'ap_tabla_db_s',
    'ap_clase_archivo' => 'nucleo/componentes/persistencia/toba_ap_tabla_db_s.php',
  ),
  '_info_columnas' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 352,
      'columna' => 'id',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => 'ref_persona_deportes_id_seq',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 353,
      'columna' => 'persona',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 354,
      'columna' => 'deporte',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 355,
      'columna' => 'dia_semana',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 356,
      'columna' => 'hora_inicio',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
    ),
    5 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 357,
      'columna' => 'hora_fin',
      'tipo' => 'E',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
    ),
    6 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 358,
      'columna' => 'desc_deporte',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
    ),
    7 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'col_id' => 359,
      'columna' => 'desc_dia_semana',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
    ),
  ),
  '_info_externas' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'externa_id' => 0,
      'tipo' => 'sql',
      'sincro_continua' => 1,
      'metodo' => NULL,
      'clase' => NULL,
      'include' => NULL,
      'sql' => 'SELECT nombre as desc_deporte FROM ref_deportes WHERE id = \'%deporte%\';',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'externa_id' => 1,
      'tipo' => 'dao',
      'sincro_continua' => 1,
      'metodo' => 'get_dia_semana',
      'clase' => 'consultas',
      'include' => 'operaciones_simples/consultas.php',
      'sql' => NULL,
    ),
  ),
  '_info_externas_col' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'externa_id' => 0,
      'es_resultado' => 0,
      'columna' => 'deporte',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'externa_id' => 0,
      'es_resultado' => 1,
      'columna' => 'desc_deporte',
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'externa_id' => 1,
      'es_resultado' => 0,
      'columna' => 'dia_semana',
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1735,
      'externa_id' => 1,
      'es_resultado' => 1,
      'columna' => 'desc_dia_semana',
    ),
  ),
  '_info_valores_unicos' => 
  array (
  ),
);
	}

}

?>