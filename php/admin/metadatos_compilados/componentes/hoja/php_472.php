<?

class php_472
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '472',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_hoja',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'PROYECTO - Tareas concluidas',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => NULL,
    'fuente_proyecto' => 'toba',
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
    'creacion' => '2004-07-22 04:16:30',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos/editores/hoja',
    'clase_archivo' => 'nucleo/browser/clases/objeto_hoja.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos/editores/hoja',
    'clase_icono' => 'objetos/hoja.gif',
    'clase_descripcion_corta' => 'HOJA de DATOS',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '/admin/objetos/instanciadores/hoja',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_hoja' => 
  array (
    'sql' => 'SELECT v.version as version,
v.version as version,
t.tarea as tarea,
t.tarea as tarea,
tm.descripcion as tema_desc,
tt.descripcion as tarea_tipo,
t.descripcion as descripcion
FROM apex_ap_tarea t,
apex_ap_tarea_tema tm,
apex_ap_version v,
apex_ap_tarea_tipo tt
WHERE t.version = v.version
AND t.version_proyecto = v.proyecto
AND t.tarea_tema = tm.tarea_tema
AND t.tarea_tipo = tt.tarea_tipo
AND t.tarea_estado = 3
GROUP BY 1, 2,3,4,5,6,7
ORDER BY 1 DESC , 2,5,6,7;',
    'total_y' => NULL,
    'total_x' => NULL,
    'total_x_formato' => 'mayusculas',
    'ordenable' => NULL,
    'columna_entrada' => NULL,
    'ancho' => '600',
    'grafico' => NULL,
    'graf_columnas' => NULL,
    'graf_filas' => NULL,
    'graf_gen_invertir' => NULL,
    'graf_gen_invertible' => NULL,
    'graf_gen_ancho' => NULL,
    'graf_gen_alto' => NULL,
  ),
  'info_hoja_dir' => 
  array (
    0 => 
    array (
      'tipo' => '1',
      'nombre' => NULL,
      'formato' => 'NULO',
      'estilo' => 'col-num-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    1 => 
    array (
      'tipo' => '2',
      'nombre' => 'Version',
      'formato' => 'NULO',
      'estilo' => 'col-num-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    2 => 
    array (
      'tipo' => '5',
      'nombre' => NULL,
      'formato' => 'NULO',
      'estilo' => 'col-num-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    3 => 
    array (
      'tipo' => '6',
      'nombre' => NULL,
      'formato' => 'NULO',
      'estilo' => 'col-num-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    4 => 
    array (
      'tipo' => '7',
      'nombre' => 'Tema',
      'formato' => 'NULO',
      'estilo' => 'col-tex-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    5 => 
    array (
      'tipo' => '7',
      'nombre' => 'Tipo',
      'formato' => 'NULO',
      'estilo' => 'col-tex-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    6 => 
    array (
      'tipo' => '7',
      'nombre' => 'Descripcion',
      'formato' => 'html_br',
      'estilo' => 'col-tex-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
  ),
);
	}

}
?>