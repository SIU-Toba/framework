<?

class php_1511
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1511',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'eiform_ap',
    'subclase_archivo' => 'admin/objetos_toba/db_tablas/eiform_ap.php',
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
      'identificador' => 'ap',
      'columnas' => 'ap',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'sql: SELECT ap, descripcion FROM apex_admin_persistencia
WHERE categoria = \'R\';',
      'etiqueta' => 'AP por defecto',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'ap_clase',
      'columnas' => 'ap_clase',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 80;',
      'etiqueta' => 'AP - Clase',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'ap_archivo',
      'columnas' => 'ap_archivo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_popup',
      'inicializacion' => 'tamano: 60;
maximo: 80;
item_destino: /admin/objetos_toba/selector_archivo,toba;
ventana: 400,400,yes;',
      'etiqueta' => 'AP - Archivo',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'debug',
      'columnas' => 'debug',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;
estado: 0;',
      'etiqueta' => 'Modo Debug',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'En el modo debug el objeto muestra un esquema de las tablas al inicio del pedido de página y otro al final. En caso de querer hacer un dump puntual usar el método [api:Objetos/Persistencia/objeto_datos_relacion dump_esquema].<br><br>
La salida es en formato SVG, ver requisitos del [wiki:Referencia/Objetos/ei_esquema objeto_ei_esquema] encargado de construirlo.',
      'orden' => '5',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>