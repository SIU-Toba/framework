<?php

class toba_mc_comp__3
{
	static function get_metadatos()
	{
		return array (
  'molde' => 
  array (
    'proyecto' => 'toba_editor',
    'molde' => 3,
    'operacion_tipo' => 10,
    'nombre' => 'Skins',
    'item' => '3419',
    'carpeta_archivos' => 'configuracion/skins',
    'prefijo_clases' => 'skins_',
    'fuente' => 'instancia',
    'clase' => 'toba_asistente_abms',
  ),
  'molde_abms' => 
  array (
    'proyecto' => 'toba_editor',
    'molde' => 3,
    'tabla' => 'apex_estilo',
    'gen_usa_filtro' => 0,
    'gen_separar_pantallas' => 1,
    'cuadro_eof' => '<center>El proyecto no tiene definidos skins propios</center>',
    'cuadro_id' => 'estilo',
    'filtro_comprobar_parametros' => NULL,
    'cuadro_forzar_filtro' => NULL,
    'cuadro_eliminar_filas' => 0,
    'cuadro_carga_origen' => 'datos_tabla',
    'cuadro_carga_sql' => 'SELECT
	ae.estilo,
	ae.descripcion,
	ap.descripcion_corta as proyecto_nombre
FROM
	apex_estilo as ae,
	apex_proyecto as ap
WHERE
		ae.proyecto = ap.proyecto
ORDER BY descripcion',
    'cuadro_carga_php_include' => NULL,
    'cuadro_carga_php_clase' => NULL,
    'cuadro_carga_php_metodo' => NULL,
    'datos_tabla_validacion' => NULL,
    'apdb_pre' => NULL,
  ),
  'molde_abms_fila' => 
  array (
    0 => 
    array (
      'proyecto' => 'toba_editor',
      'molde' => 3,
      'fila' => 31,
      'orden' => '1',
      'columna' => 'estilo',
      'etiqueta' => 'Identificador',
      'en_cuadro' => 1,
      'en_form' => 1,
      'en_filtro' => 0,
      'filtro_operador' => 'ILIKE',
      'cuadro_estilo' => 4,
      'cuadro_formato' => 1,
      'dt_tipo_dato' => 'C',
      'dt_largo' => NULL,
      'dt_secuencia' => '',
      'dt_pk' => 1,
      'elemento_formulario' => 'ef_editable',
      'ef_desactivar_modificacion' => NULL,
      'ef_procesar_javascript' => NULL,
      'ef_obligatorio' => 1,
      'ef_carga_origen' => NULL,
      'ef_carga_sql' => NULL,
      'ef_carga_tabla' => NULL,
      'ef_carga_php_include' => NULL,
      'ef_carga_php_clase' => NULL,
      'ef_carga_php_metodo' => NULL,
      'ef_carga_col_clave' => NULL,
      'ef_carga_col_desc' => NULL,
    ),
    1 => 
    array (
      'proyecto' => 'toba_editor',
      'molde' => 3,
      'fila' => 32,
      'orden' => '2',
      'columna' => 'descripcion',
      'etiqueta' => 'Descripción',
      'en_cuadro' => 1,
      'en_form' => 1,
      'en_filtro' => 0,
      'filtro_operador' => 'ILIKE',
      'cuadro_estilo' => 4,
      'cuadro_formato' => 1,
      'dt_tipo_dato' => 'C',
      'dt_largo' => NULL,
      'dt_secuencia' => '',
      'dt_pk' => 0,
      'elemento_formulario' => 'ef_editable',
      'ef_desactivar_modificacion' => NULL,
      'ef_procesar_javascript' => NULL,
      'ef_obligatorio' => 1,
      'ef_carga_origen' => NULL,
      'ef_carga_sql' => NULL,
      'ef_carga_tabla' => NULL,
      'ef_carga_php_include' => NULL,
      'ef_carga_php_clase' => NULL,
      'ef_carga_php_metodo' => NULL,
      'ef_carga_col_clave' => NULL,
      'ef_carga_col_desc' => NULL,
    ),
    2 => 
    array (
      'proyecto' => 'toba_editor',
      'molde' => 3,
      'fila' => 33,
      'orden' => '3',
      'columna' => 'proyecto',
      'etiqueta' => 'Proyecto',
      'en_cuadro' => 0,
      'en_form' => 0,
      'en_filtro' => 0,
      'filtro_operador' => '=',
      'cuadro_estilo' => 4,
      'cuadro_formato' => 1,
      'dt_tipo_dato' => 'C',
      'dt_largo' => NULL,
      'dt_secuencia' => '',
      'dt_pk' => 0,
      'elemento_formulario' => 'ef_combo',
      'ef_desactivar_modificacion' => NULL,
      'ef_procesar_javascript' => NULL,
      'ef_obligatorio' => 1,
      'ef_carga_origen' => 'datos_tabla',
      'ef_carga_sql' => 'SELECT proyecto, descripcion_corta FROM apex_proyecto ORDER BY descripcion_corta',
      'ef_carga_tabla' => 'apex_proyecto',
      'ef_carga_php_include' => NULL,
      'ef_carga_php_clase' => NULL,
      'ef_carga_php_metodo' => NULL,
      'ef_carga_col_clave' => 'proyecto',
      'ef_carga_col_desc' => 'descripcion_corta',
    ),
  ),
);
	}

}

?>