<?php

class toba_mc_comp__1723
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1723,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'ABM Juegos',
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
    'creacion' => '2005-11-14 22:07:44',
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
    'tabla' => 'ref_juegos',
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
      'objeto' => 1723,
      'col_id' => 336,
      'columna' => 'id',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => 'ref_juegos_id_seq',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1723,
      'col_id' => 337,
      'columna' => 'nombre',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 30,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_referencia',
      'objeto' => 1723,
      'col_id' => 338,
      'columna' => 'descripcion',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 255,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
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
);
	}

}

?>