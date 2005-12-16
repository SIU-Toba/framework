<?
//Generador: compilador_proyecto.php

class php_1358
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1358',
    'anterior' => NULL,
    'reflexivo' => '0',
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => NULL,
    'subclase_archivo' => NULL,
    'objeto_categoria_proyecto' => 'toba',
    'objeto_categoria' => NULL,
    'nombre' => 'OBJETO - General - Dependencias',
    'titulo' => 'Objeto Asociado',
    'colapsable' => NULL,
    'descripcion' => 'Asocia objetos a items',
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
      'identificador' => 'alta',
      'etiqueta' => '&Agregar',
      'maneja_datos' => '1',
      'sobre_fila' => '0',
      'confirmacion' => '',
      'estilo' => 'abm-input',
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'en_botonera' => '1',
      'ayuda' => '',
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => 'no_cargado',
    ),
    1 => 
    array (
      'identificador' => 'baja',
      'etiqueta' => '&Eliminar',
      'maneja_datos' => '0',
      'sobre_fila' => '0',
      'confirmacion' => '¿Desea ELIMINAR el registro?',
      'estilo' => 'abm-input',
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'borrar.gif',
      'en_botonera' => '1',
      'ayuda' => '',
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => 'cargado',
    ),
    2 => 
    array (
      'identificador' => 'modificacion',
      'etiqueta' => '&Modificar',
      'maneja_datos' => '1',
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
    3 => 
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
    'auto_reset' => '1',
    'ancho' => '500',
    'ancho_etiqueta' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'identificador',
      'columnas' => 'identificador',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable',
      'inicializacion' => 'tamano: 20;',
      'etiqueta' => 'Rol',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'dependencia_clase',
      'columnas' => 'clase',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_lista_clases_toba;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: clase;
valor: descripcion;
no_seteado: --- SELECCIONAR ---;',
      'etiqueta' => 'Clase',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'dependencia',
      'columnas' => 'proyecto, objeto_proveedor',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_combo_dao',
      'inicializacion' => 'dao: get_lista_objetos_toba;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: proyecto, objeto;
valor: descripcion;
no_seteado: --- SELECCIONAR ---;
dependencias: dependencia_clase;',
      'etiqueta' => 'Objeto',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>