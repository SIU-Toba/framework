<?php

class toba_mc_comp__2008
{
	static function get_metadatos()
	{
		return array (
  '_info' => 
  array (
    'proyecto' => 'toba_editor',
    'objeto' => 2008,
    'anterior' => NULL,
    'identificador' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'toba_ci',
    'subclase' => 'ci_abms_principal',
    'subclase_archivo' => 'asistentes/grilla/ci_abms_principal.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Asistente - GRILLA',
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
    'creacion' => '2007-09-19 16:39:15',
    'punto_montaje' => 12,
    'clase_editor_proyecto' => 'toba_editor',
    'clase_editor_item' => '1000249',
    'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
    'clase_vinculos' => NULL,
    'clase_editor' => '1000249',
    'clase_icono' => 'objetos/multi_etapa.gif',
    'clase_descripcion_corta' => 'ci',
    'clase_instanciador_proyecto' => 'toba_editor',
    'clase_instanciador_item' => '1642',
    'objeto_existe_ayuda' => NULL,
    'ap_clase' => NULL,
    'ap_archivo' => NULL,
    'ap_punto_montaje' => NULL,
    'cant_dependencias' => 6,
    'posicion_botonera' => 'abajo',
  ),
  '_info_eventos' => 
  array (
  ),
  '_info_puntos_control' => 
  array (
  ),
  '_info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => '100%',
    'alto' => NULL,
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => 'tab_h',
    'con_toc' => 0,
    'botonera_barra_item' => NULL,
  ),
  '_info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1064,
      'identificador' => 'pant_basica',
      'etiqueta' => 'Informacion básica',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 1,
      'punto_montaje' => 12,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
    1 => 
    array (
      'pantalla' => 1067,
      'identificador' => 'pant_form',
      'etiqueta' => 'Formulario',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => 'apex',
      'imagen' => NULL,
      'objetos' => NULL,
      'eventos' => NULL,
      'orden' => 2,
      'punto_montaje' => 12,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'template' => NULL,
      'template_impresion' => NULL,
    ),
  ),
  '_info_obj_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 1064,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 2008,
      'dep_id' => 955,
      'orden' => 1,
      'identificador_pantalla' => 'pant_basica',
      'identificador_dep' => 'form_molde',
    ),
    1 => 
    array (
      'pantalla' => 1067,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 2008,
      'dep_id' => 956,
      'orden' => 1,
      'identificador_pantalla' => 'pant_form',
      'identificador_dep' => 'cuadro_form_filas',
    ),
    2 => 
    array (
      'pantalla' => 1064,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 2008,
      'dep_id' => 954,
      'orden' => 2,
      'identificador_pantalla' => 'pant_basica',
      'identificador_dep' => 'form_basico',
    ),
    3 => 
    array (
      'pantalla' => 1067,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 2008,
      'dep_id' => 957,
      'orden' => 2,
      'identificador_pantalla' => 'pant_form',
      'identificador_dep' => 'form_form_fila',
    ),
    4 => 
    array (
      'pantalla' => 1064,
      'proyecto' => 'toba_editor',
      'objeto_ci' => 2008,
      'dep_id' => 958,
      'orden' => 3,
      'identificador_pantalla' => 'pant_basica',
      'identificador_dep' => 'form_filas',
    ),
  ),
  '_info_evt_pantalla' => 
  array (
  ),
  '_info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'cuadro_form_filas',
      'proyecto' => 'toba_editor',
      'objeto' => 2011,
      'clase' => 'toba_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'datos',
      'proyecto' => 'toba_editor',
      'objeto' => 2006,
      'clase' => 'toba_datos_relacion',
      'clase_archivo' => 'nucleo/componentes/persistencia/toba_datos_relacion.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'form_basico',
      'proyecto' => 'toba_editor',
      'objeto' => 2007,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => 'form_basico',
      'subclase_archivo' => 'asistentes/grilla/form_basico.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'form_filas',
      'proyecto' => 'toba_editor',
      'objeto' => 2009,
      'clase' => 'toba_ei_formulario_ml',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario_ml.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'form_form_fila',
      'proyecto' => 'toba_editor',
      'objeto' => 2012,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => 'form_carga_sql_opciones',
      'subclase_archivo' => 'asistentes/form_carga_sql_opciones.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'form_molde',
      'proyecto' => 'toba_editor',
      'objeto' => 2010,
      'clase' => 'toba_ei_formulario',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_formulario.php',
      'subclase' => 'form_molde',
      'subclase_archivo' => 'asistentes/form_molde.php',
      'fuente' => 'instancia',
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}

?>