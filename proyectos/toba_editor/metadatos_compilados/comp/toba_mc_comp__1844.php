<?php

class toba_mc_comp__1844
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1844,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'MENSAJES - datos',
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
    'creacion' => '2006-07-24 20:18:12',
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
    'tabla' => 'apex_msg',
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
      'objeto' => 1844,
      'col_id' => 480,
      'columna' => 'msg',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => '"apex_msg_seq"',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 481,
      'columna' => 'indice',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => 20,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 482,
      'columna' => 'proyecto',
      'tipo' => 'C',
      'pk' => 1,
      'secuencia' => NULL,
      'largo' => 15,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 483,
      'columna' => 'msg_tipo',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => 20,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 484,
      'columna' => 'descripcion_corta',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => 50,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    5 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 485,
      'columna' => 'mensaje_a',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => -1,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    6 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 486,
      'columna' => 'mensaje_b',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => -1,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    7 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 487,
      'columna' => 'mensaje_c',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => -1,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
      'tabla' => NULL,
    ),
    8 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1844,
      'col_id' => 488,
      'columna' => 'mensaje_customizable',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => -1,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 0,
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