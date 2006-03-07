<?

class php_1636
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1636',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'form_opciones',
    'subclase_archivo' => 'admin/objetos_toba/clonador/form_opciones.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Tipo de destino',
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
    'creacion' => '2005-10-21 16:53:55',
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
    'ancho_etiqueta' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'nuevo_nombre',
      'columnas' => 'nuevo_nombre',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 50;
maximo: 80;',
      'etiqueta' => 'Nuevo nombre',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'con_destino',
      'columnas' => 'con_destino',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;
estado: 0;',
      'etiqueta' => 'Asignar a otro objeto/item',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Una vez clonado el objeto, es posible asignarlo a otro objeto o item existente.',
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'tipo',
      'columnas' => 'tipo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_tipos_destino;
clase: ci_clonador_objetos;
include: admin/objetos_toba/clonador/ci_clonador_objetos.php;
clave: clase;
valor: clase;
no_seteado: --- Seleccione ---;',
      'etiqueta' => 'Destino - Tipo',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'objeto_id',
      'columnas' => 'objeto',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_objetos_destino;
clase: ci_clonador_objetos;
include: admin/objetos_toba/clonador/ci_clonador_objetos.php;
clave: id;
valor: descripcion;
no_seteado: --- Seleccione ---;
dependencias: tipo;',
      'etiqueta' => 'Destino',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'ci_pantalla',
      'columnas' => 'pantalla',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_pantallas_de_ci;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: pantalla;
valor: descripcion;
no_seteado: Ninguna;
dependencias: objeto_id;',
      'etiqueta' => 'Pantalla',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'identificador',
      'columnas' => 'id_dependencia',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;
maximo: 20;',
      'etiqueta' => 'Identificador',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '6',
      'colapsado' => NULL,
    ),
    6 => 
    array (
      'identificador' => 'min_filas',
      'columnas' => 'min_filas',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => NULL,
      'etiqueta' => 'Min. Filas',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '7',
      'colapsado' => NULL,
    ),
    7 => 
    array (
      'identificador' => 'max_filas',
      'columnas' => 'max_filas',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => NULL,
      'etiqueta' => 'Max. Filas',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '8',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>