<?
//Generador: compilador_proyecto.php

class php_1608
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '1608',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ci',
    'subclase' => 'ci_catalogo_objetos',
    'subclase_archivo' => 'admin/catalogos/ci_catalogo_objetos.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Catalogo de objetos',
    'titulo' => 'Catálogo de objetos',
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
    'creacion' => '2005-09-16 10:27:10',
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
      'identificador' => 'sacar_foto',
      'etiqueta' => '&Foto',
      'maneja_datos' => NULL,
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'proyecto',
      'imagen' => 'foto.gif',
      'en_botonera' => '1',
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => NULL,
      'grupo' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'refrescar',
      'etiqueta' => '&Refrescar',
      'maneja_datos' => '1',
      'sobre_fila' => '0',
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'proyecto',
      'imagen' => 'refrescar.gif',
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
    'ancho' => NULL,
    'alto' => NULL,
    'posicion_botonera' => 'arriba',
    'tipo_navegacion' => NULL,
    'con_toc' => NULL,
  ),
  'info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => '472',
      'identificador' => 'listado',
      'etiqueta' => 'Listado de objetos',
      'descripcion' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'fotos,listado',
      'eventos' => 'refrescar,sacar_foto',
      'orden' => '1',
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'fotos',
      'proyecto' => 'toba',
      'objeto' => '1383',
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'listado',
      'proyecto' => 'toba',
      'objeto' => '1611',
      'clase' => 'objeto_ei_arbol',
      'clase_archivo' => 'nucleo/browser/clases/objeto_ei_arbol.php',
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