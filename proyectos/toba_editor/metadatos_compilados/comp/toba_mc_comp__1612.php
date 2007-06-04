<?php

class toba_mc_comp__1612
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 1612,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - EI cuadro corte',
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
    'creacion' => '2005-09-20 14:01:41',
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
    'tabla' => 'apex_objeto_cuadro_cc',
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
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 285,
      'columna' => 'objeto_cuadro_proyecto',
      'tipo' => 'C',
      'pk' => 1,
      'secuencia' => NULL,
      'largo' => 15,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 286,
      'columna' => 'objeto_cuadro',
      'tipo' => 'E',
      'pk' => 1,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 287,
      'columna' => 'orden',
      'tipo' => NULL,
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 288,
      'columna' => 'columnas_id',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 200,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 289,
      'columna' => 'columnas_descripcion',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 200,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => NULL,
    ),
    5 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 290,
      'columna' => 'identificador',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 30,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
    ),
    6 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 291,
      'columna' => 'pie_contar_filas',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 10,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
    ),
    7 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 292,
      'columna' => 'pie_mostrar_titulos',
      'tipo' => 'E',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
    ),
    8 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 293,
      'columna' => 'imp_paginar',
      'tipo' => 'E',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
    ),
    9 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 294,
      'columna' => 'objeto_cuadro_cc',
      'tipo' => 'N',
      'pk' => 1,
      'secuencia' => 'apex_obj_ei_cuadro_cc_seq',
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
    ),
    10 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 300,
      'columna' => 'descripcion',
      'tipo' => 'C',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => 30,
      'no_nulo' => NULL,
      'no_nulo_db' => NULL,
      'externa' => NULL,
    ),
    11 => 
    array (
      'objeto_proyecto' => 'toba_editor',
      'objeto' => 1612,
      'col_id' => 334,
      'columna' => 'pie_mostrar_titular',
      'tipo' => 'E',
      'pk' => NULL,
      'secuencia' => NULL,
      'largo' => NULL,
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