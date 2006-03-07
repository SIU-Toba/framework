<?

class php_405
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '405',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_lista',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor CUADRO 2 - Col',
    'titulo' => 'COLUMNAS definidas',
    'colapsable' => NULL,
    'descripcion' => 'Lista del editor del Cuadro',
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
    'creacion' => NULL,
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos/editores/lista',
    'clase_archivo' => 'nucleo/browser/clases/objeto_lista.php',
    'clase_vinculos' => '1',
    'clase_editor' => '/admin/objetos/editores/lista',
    'clase_icono' => 'objetos/lista.gif',
    'clase_descripcion_corta' => 'LISTA',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '/admin/objetos/instanciadores/lista',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_lista' => 
  array (
    'titulo' => 'COLUMNAS definidas',
    'subtitulo' => NULL,
    'sql' => 'SELECT c.objeto_cuadro_proyecto,
c.objeto_cuadro,
c.orden,
c.titulo,
c.valor_sql,
c.valor_fijo,
c.valor_proceso,
c.vinculo_indice,
e.descripcion
FROM %f% apex_objeto_cuadro_columna c,
apex_columna_estilo e
WHERE c.columna_estilo =  e.columna_estilo
%w%
ORDER BY 3;',
    'col_ver' => '2=>"n",3=>"t",4=>"t",5=>"t",6=>"t",7=>"t",8=>"t"',
    'col_formato' => NULL,
    'col_titulos' => 'Orden, Titulo, Valor SQL, Valor fijo, Valor proceso, Vinc., Estilo',
    'ancho' => '600',
    'ordenar' => NULL,
    'exportar' => NULL,
    'vinculo_clave' => '0,1,2',
    'vinculo_indice' => 'abm',
  ),
);
	}

}
?>