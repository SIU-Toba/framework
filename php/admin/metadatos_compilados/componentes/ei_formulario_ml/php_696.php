<?
//Generador: compilador_proyecto.php

class php_696
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '696',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario_ml',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'PLAN - Hitos (Prueba)',
    'titulo' => 'Hitos',
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
    'creacion' => NULL,
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
  ),
  'info_formulario' => 
  array (
    'auto_reset' => '1',
    'scroll' => NULL,
    'ancho' => '500',
    'alto' => '200',
    'filas' => '5',
    'filas_agregar' => NULL,
    'filas_agregar_online' => '1',
    'filas_ordenar' => NULL,
    'filas_numerar' => NULL,
    'columna_orden' => NULL,
    'analisis_cambios' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'objeto_plan_proyecto',
      'columnas' => 'objeto_plan_proyecto',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_oculto_proyecto',
      'inicializacion' => '',
      'etiqueta' => NULL,
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '0',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'objeto_plan',
      'columnas' => 'objeto_plan',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_oculto',
      'inicializacion' => '',
      'etiqueta' => NULL,
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '0.5',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'posicion',
      'columnas' => 'posicion',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 2;',
      'etiqueta' => 'Pos.',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Posicion de la actividad en Y',
      'orden' => '0.75',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'descripcion_corta',
      'columnas' => 'descripcion_corta',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 50;',
      'etiqueta' => 'Descripcion corta',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'fecha',
      'columnas' => 'fecha',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable_fecha',
      'inicializacion' => '',
      'etiqueta' => 'Fecha inicio',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1.5',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'anotacion',
      'columnas' => 'anotacion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 50;',
      'etiqueta' => 'Anotacion',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'total' => NULL,
      'columna_estilo' => NULL,
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>