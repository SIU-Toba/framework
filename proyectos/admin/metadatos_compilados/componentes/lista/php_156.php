<?

class php_156
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '156',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_lista',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'ITEM - Notas',
    'titulo' => 'Notas',
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
    'creacion' => '2003-09-23 07:47:51',
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
    'titulo' => 'Notas',
    'subtitulo' => NULL,
    'sql' => 'SELECT item_nota, creacion,
nota_tipo, usuario_origen, usuario_destino, texto
FROM apex_item_nota %f% %w%
ORDER BY creacion DESC;',
    'col_ver' => '1=>"t", 2=>"t",3=>"t",4=>"t",5=>"t"',
    'col_formato' => NULL,
    'col_titulos' => 'Alta, Tipo, De, Para, Texto',
    'ancho' => '700',
    'ordenar' => NULL,
    'exportar' => NULL,
    'vinculo_clave' => '0',
    'vinculo_indice' => 'cargar',
  ),
);
	}

}
?>