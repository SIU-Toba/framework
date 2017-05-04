<?php

class toba_mc_comp__2260
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_usuarios',
    'objeto' => 2260,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_datos_tabla',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Usuario - editar - editor - datos - proyecto_pd',
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
    'creacion' => '2008-05-20 16:47:52',
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
    'tabla' => 'apex_usuario_proyecto_perfil_datos',
    'alias' => NULL,
    'min_registros' => NULL,
    'max_registros' => NULL,
    'ap' => 1,
    'punto_montaje' => 12000004,
    'ap_sub_clase' => NULL,
    'ap_sub_clase_archivo' => NULL,
    'ap_modificar_claves' => 1,
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
      'objeto' => 2260,
      'col_id' => 785,
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
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'col_id' => 786,
      'columna' => 'usuario_perfil_datos',
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
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'col_id' => 787,
      'columna' => 'usuario',
      'tipo' => 'C',
      'pk' => 1,
      'secuencia' => '',
      'largo' => 60,
      'no_nulo' => NULL,
      'no_nulo_db' => 1,
      'externa' => 0,
      'tabla' => NULL,
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'col_id' => 788,
      'columna' => 'perfil_datos_descripcion',
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
      'objeto' => 2260,
      'col_id' => 789,
      'columna' => 'perfil_datos_nombre',
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
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'externa_id' => 7,
      'tipo' => 'dao',
      'sincro_continua' => 1,
      'metodo' => 'get_descripcion_perfil_datos',
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
      'objeto' => 2260,
      'externa_id' => 7,
      'es_resultado' => 0,
      'columna' => 'proyecto',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'externa_id' => 7,
      'es_resultado' => 0,
      'columna' => 'usuario_perfil_datos',
    ),
    2 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'externa_id' => 7,
      'es_resultado' => 1,
      'columna' => 'perfil_datos_descripcion',
    ),
    3 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2260,
      'externa_id' => 7,
      'es_resultado' => 1,
      'columna' => 'perfil_datos_nombre',
    ),
  ),
  '_info_valores_unicos' => 
  array (
    0 => 
    array (
      'columnas' => 'proyecto,usuario,usuario_perfil_datos',
    ),
  ),
  '_info_fks' => 
  array (
  ),
);
	}

}

?>