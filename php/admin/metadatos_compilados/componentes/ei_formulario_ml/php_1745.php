<?
//Generador: compilador_proyecto.php

class php_1745
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1745',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => 'eiform_abm_detalle',
    'subclase_archivo' => 'admin/objetos_toba/eiform_abm_detalle.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - General - Eventos (Cuadro)',
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
    'creacion' => '2005-11-22 10:26:04',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_formulario_ml',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario_ml.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_formulario_ml',
    'clase_icono' => 'objetos/ut_formulario_ml.gif',
    'clase_descripcion_corta' => 'EI Formulario Multilinea',
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
    1 => 
    array (
      'identificador' => 'seleccion',
      'etiqueta' => NULL,
      'maneja_datos' => '1',
      'sobre_fila' => '1',
      'confirmacion' => '',
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'doc.gif',
      'en_botonera' => '0',
      'ayuda' => 'Seleccionar la fila',
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => NULL,
    ),
  ),
  'info_formulario' => 
  array (
    'auto_reset' => NULL,
    'scroll' => NULL,
    'ancho' => '500',
    'alto' => NULL,
    'filas' => NULL,
    'filas_agregar' => '1',
    'filas_agregar_online' => '1',
    'filas_ordenar' => '1',
    'filas_numerar' => NULL,
    'columna_orden' => 'orden',
    'analisis_cambios' => 'LINEA',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'identificador',
      'columnas' => 'identificador',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 15;
maximo: 40;',
      'etiqueta' => 'Identificador',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'total' => '0',
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'etiqueta',
      'columnas' => 'etiqueta',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;
maximo: 40;',
      'etiqueta' => 'Etiqueta',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'total' => '0',
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'imagen_recurso',
      'columnas' => 'imagen_recurso_origen',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'no_seteado: Ninguno;
sql: SELECT recurso_origen, descripcion FROM apex_recurso_origen ORDER BY descripcion;',
      'etiqueta' => 'Imagen - origen',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'total' => '0',
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'imagen',
      'columnas' => 'imagen',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;
maximo: 60;',
      'etiqueta' => 'Imagen',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'total' => '0',
      'columna_estilo' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'sobre_fila',
      'columnas' => 'sobre_fila',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;',
      'etiqueta' => 'Nivel Fila',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Se incluye un BOTON en todas las filas del elemento.',
      'orden' => '5',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'en_botonera',
      'columnas' => 'en_botonera',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;',
      'etiqueta' => 'en Bot.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Se genera un boton que dispara este evento.',
      'orden' => '6',
      'total' => '0',
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>