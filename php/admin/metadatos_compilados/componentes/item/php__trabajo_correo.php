<?
//Generador: compilador_proyecto.php

class php__trabajo_correo
{
	function get_metadatos()
	{
		return array (
  'info' => 
  array (
    'item_proyecto' => 'toba',
    'item' => '/trabajo/correo',
    'item_nombre' => 'Envio de mensajes',
    'item_descripcion' => 'Enviar correo a usuarios del proyecto',
    'item_act_buffer_proyecto' => 'toba',
    'item_act_buffer' => '0',
    'item_act_patron_proyecto' => 'toba',
    'item_act_patron' => 'especifico',
    'item_act_patron_script' => 'NO APLICABLE',
    'item_act_accion_script' => 'acciones/trabajo/correo.php',
    'item_solic_tipo' => 'browser',
    'item_solic_registrar' => NULL,
    'item_solic_obs_tipo_proyecto' => NULL,
    'item_solic_obs_tipo' => NULL,
    'item_solic_observacion' => NULL,
    'item_solic_cronometrar' => NULL,
    'item_parametro_a' => NULL,
    'item_parametro_b' => NULL,
    'item_parametro_c' => NULL,
    'tipo_pagina_clase' => 'tp_normal',
    'tipo_pagina_archivo' => 'nucleo/browser/tipo_pagina/tp_normal.php',
    'item_include_arriba' => NULL,
    'item_include_abajo' => NULL,
    'item_zona_proyecto' => 'toba',
    'item_zona' => 'zona_trabajo',
    'item_zona_archivo' => 'nucleo/browser/zona/zona_trabajo.php',
    'item_publico' => NULL,
    'item_existe_ayuda' => NULL,
    'carpeta' => '0',
    'menu' => NULL,
    'orden' => '10',
    'publico' => NULL,
    'crono' => NULL,
    'solicitud_tipo' => 'browser',
  ),
  'info_objetos' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba',
      'objeto' => '777',
      'objeto_nombre' => 'Correo',
      'objeto_subclase' => NULL,
      'objeto_subclase_archivo' => NULL,
      'orden' => '0',
      'clase_proyecto' => 'toba',
      'clase' => 'ci_cn',
      'clase_archivo' => 'nucleo/browser/subclases/ci_cn.php',
      'fuente_proyecto' => 'toba',
      'fuente' => 'instancia',
      'fuente_motor' => 'postgres7',
      'fuente_host' => NULL,
      'fuente_usuario' => NULL,
      'fuente_clave' => NULL,
      'fuente_base' => 'No aplicable',
    ),
    1 => 
    array (
      'objeto_proyecto' => 'toba',
      'objeto' => '778',
      'objeto_nombre' => 'Correo',
      'objeto_subclase' => 'objeto_cn_correo',
      'objeto_subclase_archivo' => 'acciones/trabajo/correo_cn.php',
      'orden' => '0',
      'clase_proyecto' => 'toba',
      'clase' => 'objeto_cn',
      'clase_archivo' => 'nucleo/negocio/objeto_cn.php',
      'fuente_proyecto' => 'toba',
      'fuente' => 'instancia',
      'fuente_motor' => 'postgres7',
      'fuente_host' => NULL,
      'fuente_usuario' => NULL,
      'fuente_clave' => NULL,
      'fuente_base' => 'No aplicable',
    ),
  ),
);
	}

}
?>