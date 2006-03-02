<?

class php_1814
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1814',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Prop. Básicas',
    'titulo' => 'Propiedades Generales',
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
    'creacion' => '2006-02-17 04:38:34',
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
      'etiqueta' => '&Modificacion',
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
      'identificador' => 'usuario',
      'columnas' => 'usuario',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;',
      'etiqueta' => 'Usuario',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'nombre',
      'columnas' => 'nombre',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => NULL,
      'etiqueta' => 'Nombre',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'clave',
      'columnas' => 'clave',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable_clave',
      'inicializacion' => 'tamano: 40;',
      'etiqueta' => 'Clave',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'autentificacion',
      'columnas' => 'autentificacion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_lista_c',
      'inicializacion' => 'predeterminado: md5;
lista: md5,Encriptación MD5/plano,Texto Plano (inseguro);',
      'etiqueta' => 'Fomato clave',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Formato en que se almacena la clave en la base de datos y contra el cual se hace la autentificación en el login.',
      'orden' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'email',
      'columnas' => 'email',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 30;',
      'etiqueta' => 'Email',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'vencimiento',
      'columnas' => 'vencimiento',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_fecha',
      'inicializacion' => '',
      'etiqueta' => 'Vencimiento',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '6',
      'colapsado' => NULL,
    ),
    6 => 
    array (
      'identificador' => 'parametro_a',
      'columnas' => 'parametro_a',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => NULL,
      'etiqueta' => 'Parametro a',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '7',
      'colapsado' => NULL,
    ),
    7 => 
    array (
      'identificador' => 'parametro_b',
      'columnas' => 'parametro_b',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => NULL,
      'etiqueta' => 'Parametro b',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '8',
      'colapsado' => NULL,
    ),
    8 => 
    array (
      'identificador' => 'parametro_c',
      'columnas' => 'parametro_c',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => NULL,
      'etiqueta' => 'Parametro c',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '9',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>