<?

class php_116
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '116',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_lista',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'Infra - ABMS items',
    'titulo' => 'Listado de ABMS-ITEMs',
    'colapsable' => NULL,
    'descripcion' => 'Lista de items generados',
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
    'creacion' => '2003-08-28 00:29:06',
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
    'titulo' => 'Listado de ABMS-ITEMs',
    'subtitulo' => '',
    'sql' => 'SELECT 	a.objeto_abms, 
a.tabla, 
i.orden, 
i.columna, 
i.elemento_formulario,
i.ef_ini
FROM apl_objeto_abms_item i, apl_objeto_abms a %f%
WHERE a.objeto_abms = i.objeto_abms %w%
ORDER BY 1,3;',
    'col_ver' => '0=>"n",1=>"t",2=>"n",3=>"t",4=>"t",5=>"t"',
    'col_formato' => '',
    'col_titulos' => 'Objeto, Tabla, Orden,Columna, EF, Inicializacion',
    'ancho' => '700',
    'ordenar' => '0',
    'exportar' => NULL,
    'vinculo_clave' => '0,3',
    'vinculo_indice' => 'editar',
  ),
);
	}

}
?>