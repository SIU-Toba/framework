<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class toba_usuarios_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); }
	}

	static $clases = array(
		'ci_bloqueo_ip' => 'auditoria/bloqueo_ip/ci_bloqueo_ip.php',
		'ci_bloqueo_usuarios' => 'auditoria/bloqueo_usuarios/ci_bloqueo_usuarios.php',
		'ci_auditoria' => 'auditoria/log_datos/ci_auditoria.php',
		'filtro_auditoria' => 'auditoria/log_datos/filtro_auditoria.php',
		'pant_auditoria' => 'auditoria/log_datos/pant_auditoria.php',
		'ci_sesiones' => 'auditoria/sesiones/ci_sesiones.php',
		'ci_visor_observaciones' => 'auditoria/sesiones/ci_visor_observaciones.php',
		'cuadro_sesiones' => 'auditoria/sesiones/cuadro_sesiones.php',
		'cuadro_solicitudes' => 'auditoria/sesiones/cuadro_solicitudes.php',
		'contexto_ejecucion' => 'extension_toba/contexto_ejecucion.php',
		'fuente' => 'extension_toba/fuente.php',
		'sesion' => 'extension_toba/sesion.php',
		'toba_usuarios_modelo' => 'extension_toba/toba_usuarios_modelo.php',
		'toba_usuarios_normal' => 'extension_toba/toba_usuarios_normal.php',
		'admin_instancia' => 'lib/admin_instancia.php',
		'consultas_instancia' => 'lib/consultas_instancia.php',
		'ci_login' => 'login/ci_login.php',
		'cuadro_autologin' => 'login/cuadro_autologin.php',
		'form_proyecto' => 'perfiles/form_proyecto.php',
		'ci_editor' => 'perfiles/perfil_datos/ci_editor.php',
		'ci_navegacion' => 'perfiles/perfil_datos/ci_navegacion.php',
		'arbol_perfiles_funcionales' => 'perfiles/perfil_funcional/arbol_perfiles_funcionales.php',
		'ci_editor_perfiles' => 'perfiles/perfil_funcional/ci_editor_perfiles.php',
		'ci_navegacion_perfiles' => 'perfiles/perfil_funcional/ci_navegacion_perfiles.php',
		'datos_relacion_perfiles' => 'perfiles/perfil_funcional/datos_relacion_perfiles.php',
		'ei_form_datos_perfil' => 'perfiles/perfil_funcional/ei_form_datos_perfil.php',
		'arbol_restricciones_funcionales' => 'perfiles/restricciones_funcionales/arbol_restricciones_funcionales.php',
		'ci_restricciones_funcionales' => 'perfiles/restricciones_funcionales/ci_restricciones_funcionales.php',
		'pant_restricciones_funcionales' => 'perfiles/restricciones_funcionales/pant_restricciones_funcionales.php',
		'apdb_usuario_basicas' => 'usuarios/apdb_usuario_basicas.php',
		'ci_editor' => 'usuarios/ci_editor.php',
		'ci_navegacion' => 'usuarios/ci_navegacion.php',
		'ei_form_basica' => 'usuarios/ei_form_basica.php',
		'ei_form_filtro_usuarios' => 'usuarios/ei_form_filtro_usuarios.php',
		'rest_arai_usuarios' => 'lib/rest_arai_usuarios.php',
		'gestion_arai_usuarios' => 'usuarios/gestion_arai_usuarios.php'
	);
}
?>