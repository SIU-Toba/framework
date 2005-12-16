<?
//Generador: compilador_proyecto.php

class php_1399
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1399',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ci',
    'subclase' => 'ci_principal',
    'subclase_archivo' => 'admin/objetos_toba/db_registros/ci_principal.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Editor OBJETO - datos_tabla',
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
    'creacion' => '2005-07-27 00:28:39',
    'clase_editor_proyecto' => 'toba',
    'clase_editor_item' => '/admin/objetos_toba/editores/ci',
    'clase_archivo' => 'nucleo/browser/clases/objeto_ci.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ci',
    'clase_icono' => 'objetos/multi_etapa.gif',
    'clase_descripcion_corta' => 'Controlador de Interface',
    'clase_instanciador_proyecto' => 'toba',
    'clase_instanciador_item' => '1642',
    'objeto_existe_ayuda' => NULL,
  ),
  'info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'eliminar',
      'etiqueta' => '&Eliminar',
      'maneja_datos' => '0',
      'sobre_fila' => '0',
      'confirmacion' => 'Este comando ELIMINARA el COMPONENTE y sus asociaciones con otros elementos del sistema. ¿Desea continuar?',
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'borrar.gif',
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'procesar',
      'etiqueta' => '&Guardar',
      'maneja_datos' => '1',
      'sobre_fila' => '0',
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'guardar.gif',
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => NULL,
    ),
  ),
  'info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => '600',
    'alto' => '450',
    'posicion_botonera' => 'ambos',
    'tipo_navegacion' => 'tab_h',
    'con_toc' => NULL,
  ),
  'info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => '378',
      'identificador' => '1',
      'etiqueta' => 'Propiedades basicas',
      'descripcion' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'objetos/datos_tabla.gif',
      'objetos' => 'base,prop_basicas',
      'eventos' => 'eliminar,procesar',
      'orden' => '1',
    ),
    1 => 
    array (
      'pantalla' => '379',
      'identificador' => '2',
      'etiqueta' => 'Columnas',
      'descripcion' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'columnas',
      'eventos' => 'eliminar,procesar',
      'orden' => '2',
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'base',
      'proyecto' => 'toba',
      'objeto' => '1510',
      'clase' => 'objeto_ei_formulario',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'columnas',
      'proyecto' => 'toba',
      'objeto' => '1398',
      'clase' => 'objeto_ei_formulario_ml',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario_ml.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'datos',
      'proyecto' => 'toba',
      'objeto' => '1533',
      'clase' => 'objeto_datos_relacion',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_relacion.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'prop_basicas',
      'proyecto' => 'toba',
      'objeto' => '1397',
      'clase' => 'objeto_ei_formulario',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario.php',
      'subclase' => 'eiform_ap',
      'subclase_archivo' => 'admin/objetos_toba/db_registros/eiform_ap.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}
?>