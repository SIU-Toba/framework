<?

class php_142
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '142',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_lista',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'ITEM - Objetos asociados',
    'titulo' => 'Objetos Asociados',
    'colapsable' => NULL,
    'descripcion' => '',
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
    'creacion' => '2003-09-19 08:50:40',
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
    'titulo' => 'Objetos Asociados',
    'subtitulo' => NULL,
    'sql' => 'SELECT i.item, 
o.clase, 
i.orden, 
o.nombre, 
i.objeto,
i.proyecto
FROM apex_item_objeto i,
apex_objeto o %f%
WHERE i.objeto = o.objeto 
AND i.proyecto = o.proyecto
%w%
ORDER BY 2,3;',
    'col_ver' => '1=>"t",2=>"n",3=>"t",4=>"n"',
    'col_formato' => NULL,
    'col_titulos' => 'Clase, Orden, Nombre, Objeto',
    'ancho' => '500',
    'ordenar' => NULL,
    'exportar' => NULL,
    'vinculo_clave' => '0,5,4',
    'vinculo_indice' => '0',
  ),
);
	}

}
?>