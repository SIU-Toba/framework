<?
//Generador: compilador_proyecto.php

class php_1394
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1394',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ci',
    'subclase' => 'ci_principal',
    'subclase_archivo' => 'admin/objetos_toba/ei_formulario/ci_principal.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Editor OBJETO - ei_formulario',
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
      'pantalla' => '372',
      'identificador' => '1',
      'etiqueta' => 'Propiedades basicas',
      'descripcion' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'objetos/ut_formulario.gif',
      'objetos' => 'base,prop_basicas',
      'eventos' => 'procesar,eliminar',
      'orden' => '1',
    ),
    1 => 
    array (
      'pantalla' => '373',
      'identificador' => '2',
      'etiqueta' => 'Elementos de Formulario',
      'descripcion' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'efs',
      'eventos' => 'procesar,eliminar',
      'orden' => '2',
    ),
    2 => 
    array (
      'pantalla' => '374',
      'identificador' => '3',
      'etiqueta' => 'Eventos',
      'descripcion' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'reflexion/evento.gif',
      'objetos' => 'eventos',
      'eventos' => 'eliminar,procesar',
      'orden' => '3',
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'base',
      'proyecto' => 'toba',
      'objeto' => '1355',
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
      'identificador' => 'datos',
      'proyecto' => 'toba',
      'objeto' => '1534',
      'clase' => 'objeto_datos_relacion',
      'clase_archivo' => 'nucleo/persistencia/objeto_datos_relacion.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'efs',
      'proyecto' => 'toba',
      'objeto' => '1390',
      'clase' => 'objeto_ci',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ci.php',
      'subclase' => 'ci_efs',
      'subclase_archivo' => 'admin/objetos_toba/ci_efs.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'eventos',
      'proyecto' => 'toba',
      'objeto' => '1747',
      'clase' => 'objeto_ci',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ci.php',
      'subclase' => 'ci_eventos',
      'subclase_archivo' => 'admin/objetos_toba/ci_eventos.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'prop_basicas',
      'proyecto' => 'toba',
      'objeto' => '1396',
      'clase' => 'objeto_ei_formulario',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}
?>