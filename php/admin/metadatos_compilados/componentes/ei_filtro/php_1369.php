<?
//Generador: compilador_proyecto.php

class php_1369
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1369',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_filtro',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Catalogo Unificado',
    'titulo' => 'Opciones',
    'colapsable' => '1',
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
    'creacion' => '2005-07-19 09:39:59',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/ei_filtro',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ei_filtro.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ei_filtro',
    'clase_icono' => 'objetos/ut_formulario.gif',
    'clase_descripcion_corta' => 'EI - filtro',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '1842',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'filtrar',
      'etiqueta' => '&Filtrar',
      'maneja_datos' => '1',
      'sobre_fila' => '0',
      'confirmacion' => '',
      'estilo' => 'abm-input-eliminar',
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => '',
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => 'no_cargado,cargado',
    ),
    1 => 
    array (
      'identificador' => 'cancelar',
      'etiqueta' => '&Cancelar',
      'maneja_datos' => '0',
      'sobre_fila' => '0',
      'confirmacion' => '',
      'estilo' => 'abm-input',
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => '',
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => 'cargado',
    ),
  ),
  'info_formulario' => 
  array (
    'auto_reset' => NULL,
    'ancho' => '100%',
    'ancho_etiqueta' => '80px',
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'inicial',
      'columnas' => 'inicial',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: carpetas_posibles;
clase: ci_catalogo_items;
clave: id;
valor: nombre;',
      'etiqueta' => 'Carpeta',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'nombre',
      'columnas' => 'nombre',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 30;
maximo: 255;',
      'etiqueta' => 'Nombre',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'menu',
      'columnas' => 'menu',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_combo_lista',
      'inicializacion' => 'lista: SI,NO;
no_seteado: --Todos--;',
      'etiqueta' => 'En menú',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'id',
      'columnas' => 'id',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano:20;
maximo:255;',
      'etiqueta' => 'ID',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>