<?

class php_1510
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1510',
    'anterior' => NULL,
    'reflexivo' => '1',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - General - Propiedades BASE DATOS',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'En esta interface se definen propiedades basicas de un objeto STANDART',
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
    'creacion' => NULL,
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
    'ancho' => '100%',
    'ancho_etiqueta' => '150px',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'nombre',
      'columnas' => 'nombre',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 50;
maximo: 255;',
      'etiqueta' => 'Nombre',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Nombre del objeto',
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'fuente_datos',
      'columnas' => 'fuente_datos_proyecto, fuente_datos',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db_proyecto',
      'inicializacion' => 'sql: SELECT proyecto, fuente_datos, descripcion_corta  FROM apex_fuente_datos %w% ORDER BY 2;
columna_proyecto: proyecto;',
      'etiqueta' => 'Fuente de Datos',
      'etiqueta_estilo' => NULL,
      'descripcion' => '[wiki:Referencia/FuenteDatos Fuente de datos] a la que se conecta el objeto.',
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'subclase',
      'columnas' => 'subclase',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 40;
maximo: 100;',
      'etiqueta' => 'Subclase',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Nombre optativo de la subclase. Esta tiene que heredar del elemento de la infraestructura seleccionado y utilizar las ventanas permitidas. Ver [wiki:Referencia/Objetos/Extension Extensión de un Objeto].',
      'orden' => '4',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'subclase_archivo',
      'columnas' => 'subclase_archivo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_popup',
      'inicializacion' => 'tamano: 60;
maximo: 80;
item_destino: /admin/objetos_toba/selector_archivo,toba;
ventana: 400,400,yes;
editable: 1;',
      'etiqueta' => 'Subclase - Archivo',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Archivo PHP donde reside la subclase. Ver [wiki:Referencia/Objetos/Extension Extensión de un Objeto].',
      'orden' => '5',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'descripcion',
      'columnas' => 'descripcion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'columnas: 60;',
      'etiqueta' => 'Descripción',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Ayuda contextual que muestra el objeto, opcionalmente puede tener referencias a documentación externa. Por ejemplo esta es una descripción con   esta [wiki:Referencia referencia] y esta [api:index referencia].',
      'orden' => '6',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>