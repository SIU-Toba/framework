<?

class php_155
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '155',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_lista',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'ITEM - Vinculos',
    'titulo' => 'Vinculos',
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
    'creacion' => '2003-09-23 05:26:17',
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
    'titulo' => 'Vinculos',
    'subtitulo' => NULL,
    'sql' => 'SELECT origen_item_proyecto,
origen_item,
origen_objeto_proyecto,
origen_objeto,
destino_item_proyecto,
destino_item,
destino_objeto_proyecto,
destino_objeto,
canal,
indice, 
texto, 
imagen 
FROM apex_vinculo 
%f% %w%
ORDER BY 2,4;',
    'col_ver' => '5=>"t", 10=>"t",11=>"t"',
    'col_formato' => NULL,
    'col_titulos' => 'Destino, Texto, Imagen',
    'ancho' => '600',
    'ordenar' => NULL,
    'exportar' => NULL,
    'vinculo_clave' => '0,1,2,3,4,5,6,7',
    'vinculo_indice' => '0',
  ),
);
	}

}
?>