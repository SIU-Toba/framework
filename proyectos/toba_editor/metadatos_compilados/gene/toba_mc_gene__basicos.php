<?php

class toba_mc_gene__basicos
{
	static function info_basica()
	{
		return array (
  'nombre' => 'toba_editor',
  'descripcion' => 'Editor de Metadatos',
  'descripcion_corta' => 'Toba - Editor',
  'estilo' => 'cubos',
  'estilo_proyecto' => 'toba',
  'con_frames' => 1,
  'frames_clase' => NULL,
  'frames_archivo' => NULL,
  'salida_impr_html_c' => NULL,
  'salida_impr_html_a' => NULL,
  'menu' => 'css',
  'menu_archivo' => 'nucleo/menu/toba_menu_css.php',
  'path_includes' => 'php',
  'path_browser' => '/apl',
  'administrador' => NULL,
  'listar_multiproyecto' => 0,
  'orden' => '0',
  'palabra_vinculo_std' => NULL,
  'version_toba' => '1.0.0',
  'requiere_validacion' => 1,
  'usuario_anonimo' => 'anonimo',
  'usuario_anonimo_desc' => 'Usuario Anónimo',
  'usuario_anonimo_grupos_acc' => 'admin',
  'validacion_intentos' => NULL,
  'validacion_intentos_min' => NULL,
  'validacion_debug' => 1,
  'sesion_tiempo_no_interac_min' => NULL,
  'sesion_tiempo_maximo_min' => NULL,
  'sesion_subclase' => 'sesion_editor',
  'sesion_subclase_archivo' => 'customizacion_toba/sesion_editor.php',
  'contexto_ejecucion_subclase' => 'contexto_ejecucion_editor',
  'contexto_ejecucion_subclase_archivo' => 'customizacion_toba/contexto_ejecucion_editor.php',
  'usuario_subclase' => NULL,
  'usuario_subclase_archivo' => NULL,
  'encriptar_qs' => 0,
  'registrar_solicitud' => '0',
  'registrar_cronometro' => NULL,
  'item_inicio_sesion' => '/admin/acceso',
  'item_pre_sesion' => '3286',
  'item_set_sesion' => '3359',
  'log_archivo' => 1,
  'log_archivo_nivel' => 7,
  'fuente_datos' => 'instancia',
  'version' => '1.0.2',
  'version_fecha' => '2006-11-17',
  'version_detalle' => 'SIU-Toba. Ambiente de desarrollo WEB.
Desarrollado por el programa SIU (2003-2006)',
  'version_link' => 'www.siu.edu.ar/soluciones/toba',
);
	}

	static function info_fuente__instancia()
	{
		return array (
  'proyecto' => 'toba_editor',
  'fuente_datos' => 'instancia',
  'descripcion' => 'Instancia',
  'descripcion_corta' => 'Instancia',
  'fuente_datos_motor' => 'postgres7',
  'host' => NULL,
  'usuario' => NULL,
  'clave' => NULL,
  'base' => NULL,
  'administrador' => NULL,
  'link_instancia' => 1,
  'instancia_id' => NULL,
  'subclase_archivo' => 'customizacion_toba/fuente_editor.php',
  'subclase_nombre' => 'fuente_editor',
  'orden' => NULL,
  'link_base_archivo' => 1,
  'motor' => 'postgres7',
  'profile' => NULL,
);
	}

	static function info_permiso__prueba1()
	{
		return array (
  'descripcion' => 'Utilizar la prueba uno',
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba10()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba11()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba2()
	{
		return array (
  'descripcion' => 'Utilizar la prueba dos',
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba3()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => 'Este es un mensaje particular',
);
	}

	static function info_permiso__prueba4()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba54()
	{
		return array (
  'descripcion' => 'asdfsa',
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba6()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba7()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba84()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => NULL,
);
	}

	static function info_permiso__prueba9()
	{
		return array (
  'descripcion' => NULL,
  'mensaje_particular' => NULL,
);
	}

}

?>