<?
//Generador: compilador_proyecto.php

class php_1387
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1387',
    'anterior' => NULL,
    'reflexivo' => '1',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor FORM - EF',
    'titulo' => NULL,
    'colapsable' => NULL,
    'descripcion' => 'En esta interface se editan las propiedades de los elementos de formulario.',
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
    'ancho' => NULL,
    'ancho_etiqueta' => '150px',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'columnas',
      'columnas' => 'columnas',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 60;',
      'etiqueta' => 'Datos manejados',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Identificador de los datos que maneja el EF. (Si es compuesto los valores deben indicarse separados por comas)',
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'descripcion',
      'columnas' => 'descripcion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_multilinea',
      'inicializacion' => 'filas: 4;
columnas: 60;',
      'etiqueta' => 'Descripcion',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Descripcion para ayuda sensitiva',
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'colapsado',
      'columnas' => 'colapsado',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1',
      'etiqueta' => 'Colapsado',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'El EF solo se muestra expandiendo el formulario.',
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'desactivado',
      'columnas' => 'desactivado',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_info: SI;',
      'etiqueta' => 'Desactivar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Excluir el EF del ABM',
      'orden' => '4',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'total',
      'columnas' => 'total',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor: 1;
valor_no_seteado: 0;
estado: 0;',
      'etiqueta' => 'Totalizar',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Sólo aplicable a formularios_ml. Incorpora a la columna un proceso automático de sumarización.',
      'orden' => '5',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'etiqueta_estilo',
      'columnas' => 'etiqueta_estilo',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 30;
maximo: 60;',
      'etiqueta' => 'Estilo Etiqueta',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Clase CSS que se desea aplicar a la etiqueta',
      'orden' => '6',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>