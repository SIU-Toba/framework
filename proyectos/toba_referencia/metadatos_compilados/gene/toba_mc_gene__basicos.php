<?php

class toba_mc_gene__basicos
{
	static function info_basica()
	{
		return array (
  'nombre' => 'toba_referencia',
  'descripcion' => 'Documentación de Referencia de Toba',
  'descripcion_corta' => 'Toba - Referencia',
  'estilo' => 'plastik',
  'estilo_proyecto' => 'toba',
  'con_frames' => NULL,
  'frames_clase' => NULL,
  'frames_archivo' => NULL,
  'salida_impr_html_c' => NULL,
  'salida_impr_html_a' => NULL,
  'menu' => 'css',
  'menu_archivo' => 'nucleo/menu/toba_menu_css.php',
  'path_includes' => NULL,
  'path_browser' => NULL,
  'administrador' => NULL,
  'listar_multiproyecto' => 1,
  'orden' => NULL,
  'palabra_vinculo_std' => NULL,
  'version_toba' => '1.0.4',
  'requiere_validacion' => 1,
  'usuario_anonimo' => 'anonimo',
  'usuario_anonimo_desc' => 'Usuario Anónimo',
  'usuario_anonimo_grupos_acc' => 'admin',
  'validacion_intentos' => NULL,
  'validacion_intentos_min' => NULL,
  'validacion_debug' => 0,
  'sesion_tiempo_no_interac_min' => NULL,
  'sesion_tiempo_maximo_min' => NULL,
  'sesion_subclase' => NULL,
  'sesion_subclase_archivo' => NULL,
  'contexto_ejecucion_subclase' => 'contexto_ejecucion',
  'contexto_ejecucion_subclase_archivo' => 'contexto_ejecucion.php',
  'usuario_subclase' => NULL,
  'usuario_subclase_archivo' => NULL,
  'encriptar_qs' => 0,
  'registrar_solicitud' => '0',
  'registrar_cronometro' => NULL,
  'item_inicio_sesion' => '1000073',
  'item_pre_sesion' => '1000059',
  'item_set_sesion' => NULL,
  'log_archivo' => 0,
  'log_archivo_nivel' => 1,
  'fuente_datos' => 'toba_referencia',
  'version' => '1.0.3',
  'version_fecha' => '2006-11-06',
  'version_detalle' => 'zxvxc',
  'version_link' => 'asdfasd',
);
	}

	static function info_fuente__toba_referencia()
	{
		return array (
  'proyecto' => 'toba_referencia',
  'fuente_datos' => 'toba_referencia',
  'descripcion' => 'Datos de prueba',
  'descripcion_corta' => 'toba_referencia',
  'fuente_datos_motor' => 'postgres7',
  'host' => NULL,
  'usuario' => NULL,
  'clave' => NULL,
  'base' => NULL,
  'administrador' => NULL,
  'link_instancia' => 1,
  'instancia_id' => 'toba_referencia',
  'subclase_archivo' => NULL,
  'subclase_nombre' => NULL,
  'orden' => NULL,
  'link_base_archivo' => 1,
  'motor' => 'postgres7',
  'profile' => NULL,
);
	}

	static function info_permiso__derecho_a()
	{
		return array (
  'descripcion' => 'Derecho A',
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__derecho_b()
	{
		return array (
  'descripcion' => 'Derecho B',
  'mensaje_particular' => NULL,
);
	}

}

?>