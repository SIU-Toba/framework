<?php

class toba_mc_comp__1000177
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 1000177,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_principal',
    'subclase_archivo' => 'componentes/efs/ef_popup/ci_principal.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Uso del ef_popup',
    'titulo' => NULL,
    'colapsable' => 0,
    'descripcion' => NULL,
    'fuente_proyecto' => NULL,
    'fuente' => NULL,
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
    'creacion' => '2006-09-01 09:56:05',
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '/admin/objetos_toba/editores/ci',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '/admin/objetos_toba/editores/ci',
    'clase_icono' => 'objetos/multi_etapa.gif',
    'clase_descripcion_corta' => 'ci',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1642',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'cant_dependencias' => '2',
  ),
  '_info_eventos' => 
  array (
    0 => 
    array (
      'identificador' => 'refrescar',
      'etiqueta' => '&Refrescar',
      'maneja_datos' => 1,
      'sobre_fila' => NULL,
      'confirmacion' => NULL,
      'estilo' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => 'refrescar.png',
      'en_botonera' => 1,
      'ayuda' => NULL,
      'ci_predep' => NULL,
      'implicito' => 0,
      'defecto' => 0,
      'grupo' => NULL,
      'accion' => NULL,
      'accion_imphtml_debug' => 0,
      'accion_vinculo_carpeta' => NULL,
      'accion_vinculo_item' => NULL,
      'accion_vinculo_objeto' => NULL,
      'accion_vinculo_popup' => 0,
      'accion_vinculo_popup_param' => NULL,
      'accion_vinculo_celda' => NULL,
      'accion_vinculo_target' => NULL,
    ),
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => '600px',
    'alto' => NULL,
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => 'tab_h',
    'con_toc' => 0,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1000040,
      'identificador' => 'formulario',
      'etiqueta' => 'Simple',
      'descripcion' => 'En su comportamiento básico el [wiki_toba:Referencia/efs/popup ef_popup] necesita:<br>
<ul>
<li>Apuntar a un ítem que contenga un cuadro cuyo evento de selección retorne el valor al formulario original
<li>Un método de carga de la descripción. Esto es cuando se carga el ef con una clave (digamos 2) se necesita saber cual es la descripción asociada a esa clave y para eso se ejecuta el método.
</ul>',
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => 'form',
      'eventos' => 'refrescar',
      'orden' => 1,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
    1 => 
    array (
      'pantalla' => 1000143,
      'identificador' => 'cascada',
      'etiqueta' => 'En Cascada',
      'descripcion' => 'Cuando el popup es esclavo de una cascada, además del método de carga de descripción se necesita definir el método que será responsable de responder a la cascada.
<p>En este método por ejemplo se puede dar un valor por defecto al ef_popup o guardar algo en la toba::memoria para que el ítem de popup pueda leerlo
</p>',
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => 'form_cascada',
      'eventos' => 'refrescar',
      'orden' => 2,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'form',
      'proyecto' => 'toba_referencia',
      'objeto' => 1000175,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_referencia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'form_cascada',
      'proyecto' => 'toba_referencia',
      'objeto' => 1916,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'toba_referencia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}

?>