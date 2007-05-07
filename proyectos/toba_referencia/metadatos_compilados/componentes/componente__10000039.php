<?php

class componente__10000039
{
	static function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba_referencia',
    'objeto' => 10000039,
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ci',
    'subclase' => 'ci_inscripcion_examen',
    'subclase_archivo' => 'puntos_de_control/ci_inscripcion_examen.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Inscripcion a examen',
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
    'creacion' => '2007-01-04 11:51:59',
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
    'cant_dependencias' => '4',
  ),
  'info_eventos' => 
  array (
  ),
  'puntos_control' => 
  array (
  ),
  'info_ci' => 
  array (
    'ev_procesar_etiq' => NULL,
    'ev_cancelar_etiq' => NULL,
    'objetos' => NULL,
    'ancho' => NULL,
    'alto' => NULL,
    'posicion_botonera' => 'abajo',
    'tipo_navegacion' => NULL,
    'con_toc' => 0,
  ),
  'info_ci_me_pantalla' => 
  array (
    0 => 
    array (
      'pantalla' => 10000014,
      'identificador' => 'pant_sel_persona',
      'etiqueta' => 'Selecc. Persona',
      'descripcion' => 'En este ejemplo se ejecutan controles con distintas configuraciones <br>durante la ejecucion del item. Y se agrega un dump del fragmento de sesion <br> correspondiente al estado de los puntos de control.',
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'personas',
      'eventos' => NULL,
      'orden' => 1,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
    1 => 
    array (
      'pantalla' => 10000015,
      'identificador' => 'pant_sel_carrera',
      'etiqueta' => 'Selecc. Carrera',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'alumnos',
      'eventos' => NULL,
      'orden' => 2,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
    2 => 
    array (
      'pantalla' => 10000016,
      'identificador' => 'pant_sel_materia',
      'etiqueta' => 'Selecc. Materia',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'materias',
      'eventos' => NULL,
      'orden' => 3,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
    3 => 
    array (
      'pantalla' => 10000017,
      'identificador' => 'pant_sel_comision',
      'etiqueta' => 'Selecc. Comisión',
      'descripcion' => NULL,
      'tip' => NULL,
      'imagen_recurso_origen' => NULL,
      'imagen' => NULL,
      'objetos' => 'comisiones',
      'eventos' => NULL,
      'orden' => 4,
      'subclase' => NULL,
      'subclase_archivo' => NULL,
    ),
  ),
  'info_dependencias' => 
  array (
    0 => 
    array (
      'identificador' => 'alumnos',
      'proyecto' => 'toba_referencia',
      'objeto' => 10000041,
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'comisiones',
      'proyecto' => 'toba_referencia',
      'objeto' => 10000043,
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'materias',
      'proyecto' => 'toba_referencia',
      'objeto' => 10000042,
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'personas',
      'proyecto' => 'toba_referencia',
      'objeto' => 10000040,
      'clase' => 'objeto_ei_cuadro',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ei_cuadro.php',
      'subclase' => NULL,
      'subclase_archivo' => NULL,
      'fuente' => NULL,
      'parametros_a' => NULL,
      'parametros_b' => NULL,
    ),
  ),
);
	}

}
?>