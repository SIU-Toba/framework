<?
//Generador: compilador_proyecto.php

class php_1356
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1356',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'eiform_prop_basicas',
    'subclase_archivo' => 'admin/objetos_toba/ci/eiform_prop_basicas.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - Editor CI - Prop. basicas',
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
    'ancho_etiqueta' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'ancho',
      'columnas' => 'ancho',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 10;',
      'etiqueta' => 'Ancho',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'alto',
      'columnas' => 'alto',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 10;',
      'etiqueta' => 'Alto',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '6',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'tipo_navegacion',
      'columnas' => 'tipo_navegacion',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_db',
      'inicializacion' => 'sql: SELECT tipo_navegacion, descripcion
FROM apex_objeto_mt_me_tipo_nav;
no_seteado: Definida por la subclase;',
      'etiqueta' => 'Tipo de Navegacion',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Si el CI tiene mas de una pantalla, estas pueden navegarse a travez de una de las formas estandar.',
      'orden' => '12',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'con_toc',
      'columnas' => 'con_toc',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_checkbox',
      'inicializacion' => 'valor:1;',
      'etiqueta' => 'Wizard Incluye TOC',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Se muestra una tabla mostrando donde se encuentra posicionado actualmente el usuario.',
      'orden' => '13',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'posicion_botonera',
      'columnas' => 'posicion_botonera',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_lista_c',
      'inicializacion' => 'lista: abajo,Inferior/arriba,Superior/ambos,Superior e Inferior;',
      'etiqueta' => 'Posición botonera',
      'etiqueta_estilo' => NULL,
      'descripcion' => 'Determina la posición gráfica que ocupan los botones del CI.',
      'orden' => '14',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>