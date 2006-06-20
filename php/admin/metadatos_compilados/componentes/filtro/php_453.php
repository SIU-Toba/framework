<?

class php_453
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '453',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_filtro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'AUDITORIA - Solicitud Consola',
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
    'creacion' => '2004-07-05 16:29:44',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos/editores/filtro',
    'clase_archivo' => 'nucleo/browser/clases/objeto_filtro.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos/editores/filtro',
    'clase_icono' => 'objetos/filtro.gif',
    'clase_descripcion_corta' => 'FILTRO',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '/admin/objetos/instanciadores/filtro',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_dimensiones' => 
  array (
    0 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'buscar_ereg',
      'fuente' => 'instancia',
      'nombre' => 'Buscar Cadena (ereg)',
      'descripcion' => 'Busca una expresion regular en un campo',
      'tipo' => 'texto_operador',
      'inicializacion' => 'tamano: 20;
maximo: 40;
operador: ~*;',
      'etiqueta' => 'Llamada',
      'tabla' => NULL,
      'columna' => 'llamada',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
    1 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'Cronometro',
      'fuente' => 'instancia',
      'nombre' => 'Cronometrado',
      'descripcion' => 'Filtrar elementos cronometrados',
      'tipo' => 'checkbox',
      'inicializacion' => 'valor: 1;
valor_info: SI;
operador: >=;',
      'etiqueta' => NULL,
      'tabla' => NULL,
      'columna' => '(SELECT COUNT(*) FROM apex_solicitud_cronometro soc WHERE soc.solicitud = s.solicitud)',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
    2 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'tiempo',
      'fuente' => 'instancia',
      'nombre' => 'Tiempo',
      'descripcion' => 'Buscar un tiempo de ejecucion',
      'tipo' => 'numero_conector',
      'inicializacion' => 'digitos: 4;',
      'etiqueta' => 'Tiempo ejecucion',
      'tabla' => NULL,
      'columna' => 'tiempo_respuesta',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
    3 => 
    array (
      'grupo' => NULL,
      'grupo_nombre' => NULL,
      'grupo_des' => NULL,
      'dimension' => 'lapso',
      'fuente' => 'instancia',
      'nombre' => 'Lapso de Meses',
      'descripcion' => 'Especificar un lapso de meses
(Lo modificamos, ya que de la forma en la que estaba planteada, no funcionaba correctamente)',
      'tipo' => 'mes_lapso',
      'inicializacion' => 'anio_i: 2004;
anio_f: 2005;',
      'etiqueta' => 'Lapso',
      'tabla' => NULL,
      'columna' => 'date_part(\'month\',s.momento) %-%
date_part(\'year\',s.momento) %-%
date_part(\'month\',s.momento) %-%
date_part(\'year\',s.momento)',
      'obligatorio' => NULL,
      'no_interactivo' => NULL,
      'predeterminado' => NULL,
    ),
  ),
);
	}

}
?>