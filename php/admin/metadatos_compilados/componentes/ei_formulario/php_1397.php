<?

class php_1397
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1397',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'eiform_ap',
    'subclase_archivo' => 'admin/objetos_toba/db_registros/eiform_ap.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - DBR - Prop. basicas',
    'titulo' => 'Administrador de Persistencia PREDETERMINADO',
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
    'creacion' => '2005-07-26 23:56:28',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_formulario',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_formulario',
    'clase_icono' => 'objetos/ut_formulario.gif',
    'clase_descripcion_corta' => 'Formulario',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '1842',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'modificacion',
      'etiqueta' => 'Modificacion',
      'maneja_datos' => '1',
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '0',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => '1',
      'grupo' => NULL,
    ),
  ),
  'info_formulario' => 
  array (
    'auto_reset' => NULL,
    'ancho' => NULL,
    'ancho_etiqueta' => '150px',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'tabla',
      'columnas' => 'tabla',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 120;',
      'etiqueta' => 'Tabla',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Nombre de la tabla de la base de datos con la que va a trabajar el objeto.',
      'orden' => '3',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'ap',
      'columnas' => 'ap',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'predeterminado: 1;
sql: SELECT ap, descripcion FROM apex_admin_persistencia
WHERE categoria = \'T\' ORDER BY 2 DESC;',
      'etiqueta' => 'Tipo de persistencia',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'ap_clase',
      'columnas' => 'ap_clase',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 80;',
      'etiqueta' => 'Subclase',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'ap_archivo',
      'columnas' => 'ap_archivo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_popup',
      'inicializacion' => 'tamano: 60;
maximo: 80;
item_destino: /admin/objetos_toba/selector_archivo,toba;
ventana: 400,400,yes;
editable: 1;',
      'etiqueta' => 'Archivo',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Seleccionar un archivo PHP.',
      'orden' => '6',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'modificar_claves',
      'columnas' => 'modificar_claves',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;
estado: 0;',
      'etiqueta' => 'Permitir mod. claves',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Por defecto las columnas marcadas como claves primarias de un tabla no son modificables.',
      'orden' => '7',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>