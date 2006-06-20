<?

class php_245
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '245',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_hoja',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'ESTAD. - Listado O x  P',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'Listado de objetos generados por proyecto',
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
    'creacion' => '2004-03-08 15:30:20',
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
    'sql' => 'SELECT	o.clase,
o.clase,
p.proyecto,
p.descripcion,
count(*)
FROM apex_proyecto p,
apex_objeto o
WHERE o.proyecto = p.proyecto
AND o.clase <> \'objeto\'
GROUP BY 1,2,3,4
ORDER BY 1,2,3,4;',
    'total_y' => '1',
    'total_x' => '1',
    'total_x_formato' => 'NULO',
    'ordenable' => NULL,
    'columna_entrada' => NULL,
    'ancho' => NULL,
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
      'tipo' => '3',
      'nombre' => NULL,
      'formato' => 'NULO',
      'estilo' => 'col-tex-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    1 => 
    array (
      'tipo' => '4',
      'nombre' => 'Clases',
      'formato' => 'NULO',
      'estilo' => 'col-tex-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    2 => 
    array (
      'tipo' => '5',
      'nombre' => NULL,
      'formato' => 'NULO',
      'estilo' => 'col-tex-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    3 => 
    array (
      'tipo' => '6',
      'nombre' => 'Proyectos',
      'formato' => 'indivisible',
      'estilo' => 'col-tex-p3',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
    4 => 
    array (
      'tipo' => '7',
      'nombre' => 'Objetos',
      'formato' => 'NULO',
      'estilo' => 'col-num-p1',
      'dimension' => NULL,
      'dimension_tabla' => NULL,
      'dimension_columna' => NULL,
    ),
  ),
);
	}

}
?>