<?

class php_146
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '146',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_lista',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor HOJA - Directivas',
    'titulo' => 'Directivas',
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
    'creacion' => '2003-09-21 01:07:36',
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
    'titulo' => 'Directivas',
    'subtitulo' => NULL,
    'sql' => 'SELECT d.objeto_hoja_proyecto,
d.objeto_hoja,
d.columna,
t.nombre,
d.nombre,
cf.descripcion,
ce.descripcion,
d.par_dimension
FROM %f% 
apex_objeto_hoja_directiva_ti t,
apex_objeto_hoja_directiva d
LEFT OUTER JOIN apex_columna_estilo ce 
ON d.columna_estilo = ce.columna_estilo
LEFT OUTER JOIN apex_columna_formato cf
ON d.columna_formato = cf.columna_formato
WHERE d.objeto_hoja_directiva_tipo = t.objeto_hoja_directiva_tipo
%w%
ORDER BY 1,2,3;',
    'col_ver' => '2=>"n",3=>"t",4=>"t",5=>"t",6=>"t"',
    'col_formato' => NULL,
    'col_titulos' => 'Columna,Tipo, Nombre,Formato,Estilo',
    'ancho' => '500',
    'ordenar' => NULL,
    'exportar' => NULL,
    'vinculo_clave' => '0,1,2',
    'vinculo_indice' => 'abm',
  ),
);
	}

}
?>