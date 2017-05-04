<?php

class toba_mc_gene__basicos
{
	static function info_basica()
	{
		return array (
  'nombre' => 'toba_usuarios',
  'descripcion' => 'Administración de Usuarios Toba',
  'descripcion_corta' => 'Toba - Usuarios',
  'estilo' => 'plastik',
  'estilo_proyecto' => 'toba',
  'con_frames' => 1,
  'frames_clase' => NULL,
  'frames_archivo' => NULL,
  'salida_impr_html_c' => NULL,
  'salida_impr_html_a' => NULL,
  'menu' => 'css',
  'menu_archivo' => 'nucleo/menu/toba_menu_css.php',
  'path_includes' => NULL,
  'path_browser' => NULL,
  'administrador' => NULL,
  'listar_multiproyecto' => 0,
  'orden' => NULL,
  'palabra_vinculo_std' => NULL,
  'version_toba' => 'trunk',
  'requiere_validacion' => 1,
  'usuario_anonimo' => NULL,
  'usuario_anonimo_desc' => NULL,
  'usuario_anonimo_grupos_acc' => NULL,
  'validacion_intentos' => NULL,
  'validacion_intentos_min' => 5,
  'validacion_bloquear_usuario' => 1,
  'validacion_debug' => 0,
  'sesion_tiempo_no_interac_min' => NULL,
  'sesion_tiempo_maximo_min' => NULL,
  'sesion_subclase' => 'sesion',
  'sesion_subclase_archivo' => 'extension_toba/sesion.php',
  'contexto_ejecucion_subclase' => 'contexto_ejecucion',
  'contexto_ejecucion_subclase_archivo' => 'extension_toba/contexto_ejecucion.php',
  'usuario_subclase' => NULL,
  'usuario_subclase_archivo' => NULL,
  'encriptar_qs' => 0,
  'registrar_solicitud' => '0',
  'registrar_cronometro' => NULL,
  'item_inicio_sesion' => '3432',
  'item_pre_sesion' => '33000037',
  'item_pre_sesion_popup' => 0,
  'item_set_sesion' => NULL,
  'log_archivo' => 1,
  'log_archivo_nivel' => 7,
  'fuente_datos' => 'toba_usuarios',
  'version' => NULL,
  'version_fecha' => NULL,
  'version_detalle' => NULL,
  'version_link' => NULL,
  'tiempo_espera_ms' => 5000,
  'navegacion_ajax' => 0,
  'codigo_ga_tracker' => NULL,
  'extension_toba' => false,
  'extension_proyecto' => false,
  'pm_impresion' => 12000004,
  'pm_sesion' => 12000004,
  'pm_contexto' => 12000004,
  'pm_usuario' => 12000004,
  'es_css3' => 0,
);
	}

	static function info_fuente__toba_usuarios()
	{
		return array (
  'proyecto' => 'toba_usuarios',
  'fuente_datos' => 'toba_usuarios',
  'descripcion' => 'Fuente toba_usuarios',
  'descripcion_corta' => 'toba_usuarios',
  'fuente_datos_motor' => 'postgres7',
  'host' => NULL,
  'punto_montaje' => NULL,
  'subclase_archivo' => 'extension_toba/fuente.php',
  'subclase_nombre' => 'fuente',
  'orden' => NULL,
  'schema' => NULL,
  'instancia_id' => 'toba_usuarios',
  'administrador' => NULL,
  'link_instancia' => 1,
  'tiene_auditoria' => 0,
  'parsea_errores' => 0,
  'permisos_por_tabla' => 0,
  'usuario' => NULL,
  'clave' => NULL,
  'base' => NULL,
  'link_base_archivo' => 1,
  'motor' => 'postgres7',
  'profile' => NULL,
  'mapeo_tablas_dt' => 
  array (
    'apex_usuario' => 2182,
    'apex_usuario_proyecto' => 2183,
    'apex_grupo_acc_restriccion_funcional' => 2204,
    'apex_permiso_grupo_acc' => 2205,
    'apex_usuario_grupo_acc' => 2206,
    'apex_usuario_perfil_datos' => 2218,
    'apex_usuario_perfil_datos_dims' => 2222,
    'apex_usuario_proyecto_perfil_datos' => 2260,
    'apex_usuario_grupo_acc_miembros' => 30000107,
    'apex_usuario_pregunta_secreta' => 33000064,
    'apex_proyecto' => 33000096,
    'apex_menu' => 33000119,
    'apex_menu_operaciones' => 33000120,
    'apex_restriccion_funcional' => 45000004,
  ),
);
	}

	static function info_indices_componentes()
	{
		return array (
);
	}

}

?>