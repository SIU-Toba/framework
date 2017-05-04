<?php

class toba_mc_item__3445
{
	static function get_metadatos()
	{
		return array (
  'basica' => 
  array (
    'item_proyecto' => 'toba_usuarios',
    'item' => '3445',
    'item_nombre' => 'Log de Sesiones y Accesos',
    'item_descripcion' => 'Una sesión es el espacio de tiempo desde que el usuario ingresa a la aplicación hasta que se desloguea del mismo o caduca su sesión en el servidor. En este último caso, no hay forma de registrar en el log la hora exacta en que caduca.
<br><br>
Dentro de una sesión, el usuario realiza una serie de accesos, estos sucesos se registran dependiendo de la configuración del proyecto y de la operación a la que accede. Cada acceso equivale a un pedido de página o request del navegador al servidor.',
    'item_act_buffer_proyecto' => NULL,
    'item_act_buffer' => NULL,
    'item_act_patron_proyecto' => NULL,
    'item_act_patron' => NULL,
    'item_act_accion_script' => NULL,
    'item_solic_tipo' => 'web',
    'item_solic_registrar' => 0,
    'item_solic_obs_tipo_proyecto' => NULL,
    'item_solic_obs_tipo' => NULL,
    'item_solic_observacion' => NULL,
    'item_solic_cronometrar' => 0,
    'item_parametro_a' => NULL,
    'item_parametro_b' => NULL,
    'item_parametro_c' => NULL,
    'item_imagen_recurso_origen' => 'apex',
    'item_imagen' => NULL,
    'punto_montaje' => 12000004,
    'tipo_pagina_punto_montaje' => 12000004,
    'tipo_pagina_clase' => 'toba_usuarios_normal',
    'tipo_pagina_archivo' => 'extension_toba/toba_usuarios_normal.php',
    'item_include_arriba' => NULL,
    'item_include_abajo' => NULL,
    'item_zona_proyecto' => NULL,
    'item_zona' => NULL,
    'zona_punto_montaje' => NULL,
    'item_zona_archivo' => NULL,
    'zona_cons_archivo' => NULL,
    'zona_cons_clase' => NULL,
    'zona_cons_metodo' => NULL,
    'item_publico' => 0,
    'item_existe_ayuda' => NULL,
    'carpeta' => 0,
    'menu' => 1,
    'orden' => '3',
    'publico' => 0,
    'redirecciona' => 0,
    'crono' => 0,
    'solicitud_tipo' => 'web',
    'item_padre' => '3443',
    'cant_dependencias' => 1,
    'cant_items_hijos' => 0,
    'molde' => NULL,
    'retrasar_headers' => 0,
  ),
  'objetos' => 
  array (
    0 => 
    array (
      'objeto_proyecto' => 'toba_usuarios',
      'objeto' => 2240,
      'objeto_nombre' => 'Log de sesiones',
      'objeto_subclase' => 'ci_sesiones',
      'objeto_subclase_archivo' => 'auditoria/sesiones/ci_sesiones.php',
      'orden' => 0,
      'clase_proyecto' => 'toba',
      'clase' => 'toba_ci',
      'clase_archivo' => 'nucleo/componentes/interface/toba_ci.php',
      'fuente_proyecto' => NULL,
      'fuente' => NULL,
      'fuente_motor' => NULL,
      'fuente_host' => NULL,
      'fuente_usuario' => NULL,
      'fuente_clave' => NULL,
      'fuente_base' => NULL,
    ),
  ),
);
	}

}

?>