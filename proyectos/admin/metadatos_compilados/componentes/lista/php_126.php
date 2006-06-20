<?

class php_126
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '126',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_lista',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'Infra - Listado PATRONES',
    'titulo' => 'Patrones existentes',
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
    'creacion' => '2003-08-30 03:39:34',
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
    'titulo' => 'Patrones existentes',
    'subtitulo' => NULL,
    'sql' => 'SELECT patron, archivo 
FROM apex_patron %f% %w%
ORDER BY 2',
    'col_ver' => '0=>"t", 1=>"t"',
    'col_formato' => NULL,
    'col_titulos' => 'patron, Archivo',
    'ancho' => '500',
    'ordenar' => NULL,
    'exportar' => NULL,
    'vinculo_clave' => '0',
    'vinculo_indice' => NULL,
  ),
);
	}

}
?>