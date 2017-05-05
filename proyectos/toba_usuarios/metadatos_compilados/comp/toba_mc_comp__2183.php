<?php

class toba_mc_comp__2183
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2183,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Usuario - Proyecto',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => 'toba_usuarios',
    'fuente' => 'toba_usuarios',
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
    'creacion' => '2008-02-25 17:56:35',
    'punto_montaje' => 12000004,
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
    'ap_punto_montaje' => 12000004,
    'cant_dependencias' => 0,
    'posicion_botonera' => NULL,
  ),
  '_info_estructura' => 
  array (
    'tabla' => 'apex_usuario_proyecto',
    'alias' => NULL,
    'min_registros' => NULL,
    'max_registros' => NULL,
    'ap' => 1,
    'punto_montaje' => 12000004,
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
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'col_id' => 723,
      'columna' => 'grupo_acceso',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
      'tabla' => NULL,
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'col_id' => 724,
      'columna' => 'grupo_acceso_desc',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
      'tabla' => NULL,
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'col_id' => 725,
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
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'col_id' => 726,
      'columna' => 'proyecto_nombre',
      'tipo' => 'C',
      'pk' => 0,
      'secuencia' => NULL,
      'largo' => NULL,
      'no_nulo' => NULL,
      'no_nulo_db' => 0,
      'externa' => 1,
      'tabla' => NULL,
    ),
    4 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'col_id' => 727,
      'columna' => 'usuario',
      'tipo' => 'C',
      'pk' => 1,
      'secuencia' => NULL,
      'largo' => 20,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    5 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'col_id' => 728,
      'columna' => 'usuario_grupo_acc',
      'tipo' => 'C',
      'pk' => 1,
      'secuencia' => NULL,
      'largo' => 20,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
  ),
  '_info_externas' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'externa_id' => 5,
      'tipo' => 'dao',
      'sincro_continua' => 1,
      'metodo' => 'get_descripcion_grupo_acceso',
      'clase' => 'consultas_instancia',
      'include' => 'lib/consultas_instancia.php',
      'sql' => NULL,
      'dato_estricto' => 1,
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
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'externa_id' => 5,
      'es_resultado' => 1,
      'columna' => 'grupo_acceso',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'externa_id' => 5,
      'es_resultado' => 1,
      'columna' => 'grupo_acceso_desc',
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'externa_id' => 5,
      'es_resultado' => 0,
      'columna' => 'proyecto',
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2183,
      'externa_id' => 5,
      'es_resultado' => 0,
      'columna' => 'usuario_grupo_acc',
    ),
  ),
  '_info_valores_unicos' => 
  array (
    0 => 
    array (
      'columnas' => 'proyecto,usuario_grupo_acc',
    ),
  ),
  '_info_fks' => 
  array (
  ),
);
	}

}

?>