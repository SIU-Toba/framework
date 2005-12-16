<?
//Generador: compilador_proyecto.php

class php_705
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'proyecto' => 'toba',
    'objeto' => '705',
    'anterior' => NULL,
    'reflexivo' => NULL,
    'clase_proyecto' => 'toba',
    'clase' => 'objeto_ei_formulario',
    'subclase' => 'objeto_ei_formulario_p2',
    'subclase_archivo' => 'acciones/pruebas/capas/prueba_2_interface.php',
    'objeto_categoria_proyecto' => NULL,
    'objeto_categoria' => NULL,
    'nombre' => 'Prueba 2 - Planificar',
    'titulo' => 'Planificar',
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
    'creacion' => '2004-11-11 16:12:01',
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
    'ancho' => '400',
    'ancho_etiqueta' => NULL,
  ),
  'info_formulario_ef' => 
  array (
    0 => 
    array (
      'identificador' => 'deuda',
      'columnas' => 'deuda',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 10;
solo_lectura: 1;',
      'etiqueta' => 'Deuda',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '1',
      'colapsado' => NULL,
    ),
    1 => 
    array (
      'identificador' => 'deuda_condona',
      'columnas' => 'deuda_condona',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 10;
javascript: onchange, calcular_total(this.form);',
      'etiqueta' => 'Deuda Condona',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2',
      'colapsado' => NULL,
    ),
    2 => 
    array (
      'identificador' => 'deuda_total',
      'columnas' => 'deuda_total',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 10;
solo_lectura: 1;
estilo: ef-input-numero-2;',
      'etiqueta' => 'Deuda TOTAL',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '2.5',
      'colapsado' => NULL,
    ),
    3 => 
    array (
      'identificador' => 'interes',
      'columnas' => 'interes',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 10;
solo_lectura: 1;',
      'etiqueta' => 'Intereses',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '3',
      'colapsado' => NULL,
    ),
    4 => 
    array (
      'identificador' => 'interes_condona',
      'columnas' => 'interes_condona',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 10;
javascript: onchange, calcular_total(this.form);',
      'etiqueta' => 'Interes condona',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '4',
      'colapsado' => NULL,
    ),
    5 => 
    array (
      'identificador' => 'interes_total',
      'columnas' => 'interes_total',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 10;
solo_lectura: 1;
estilo: ef-input-numero-2;',
      'etiqueta' => 'Interes TOTAL',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5',
      'colapsado' => NULL,
    ),
    6 => 
    array (
      'identificador' => 'total',
      'columnas' => 'total',
      'obligatorio' => NULL,
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 10;
solo_lectura: 1;
estilo: ef-input-numero-3;',
      'etiqueta' => 'Total',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '5.5',
      'colapsado' => NULL,
    ),
    7 => 
    array (
      'identificador' => 'cuotas',
      'columnas' => 'cuotas',
      'obligatorio' => '1',
      'elemento_formulario' => 'ef_editable_numero',
      'inicializacion' => 'cifras: 2;',
      'etiqueta' => 'Cuotas',
      'etiqueta_estilo' => NULL,
      'descripcion' => NULL,
      'orden' => '6',
      'colapsado' => NULL,
    ),
  ),
);
	}

}
?>