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
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'ci_bloqueo_ip' => 'auditoria/bloqueo_ip/ci_bloqueo_ip.php',
		'ci_bloqueo_usuarios' => 'auditoria/bloqueo_usuarios/ci_bloqueo_usuarios.php',
		'ci_conf_auditoria' => 'auditoria/configuracion/ci_conf_auditoria.php',
		'ci_conf_log' => 'auditoria/configuracion/ci_conf_log.php',
		'form_conf_log' => 'auditoria/configuracion/form_conf_log.php',
		'ci_auditoria' => 'auditoria/log_datos/ci_auditoria.php',
		'filtro_auditoria' => 'auditoria/log_datos/filtro_auditoria.php',
		'pant_auditoria' => 'auditoria/log_datos/pant_auditoria.php',
		'ci_sesiones' => 'auditoria/sesiones/ci_sesiones.php',
		'ci_visor_observaciones' => 'auditoria/sesiones/ci_visor_observaciones.php',
		'cuadro_sesiones' => 'auditoria/sesiones/cuadro_sesiones.php',
		'cuadro_solicitudes' => 'auditoria/sesiones/cuadro_solicitudes.php',
		'ci_ws_logs' => 'auditoria/web_services/ci_ws_logs.php',
		'form_proyecto' => 'auditoria/web_services/form_proyecto.php',
		'pantalla_detalle_ws' => 'auditoria/web_services/pantalla_detalle_ws.php',
		'contexto_ejecucion' => 'extension_toba/contexto_ejecucion.php',
		'fuente' => 'extension_toba/fuente.php',
		'sesion' => 'extension_toba/sesion.php',
		'toba_sin_menu' => 'extension_toba/toba_sin_menu.php',
		'toba_usuarios_comando' => 'extension_toba/toba_usuarios_comando.php',
		'toba_usuarios_modelo' => 'extension_toba/toba_usuarios_modelo.php',
		'toba_usuarios_normal' => 'extension_toba/toba_usuarios_normal.php',
		'InterfaseApiUsuarios' => 'lib/InterfaseApiUsuarios.php',
		'admin_instancia' => 'lib/admin_instancia.php',
		'api_usuarios_1' => 'lib/api_usuarios_1.php',
		'api_usuarios_2' => 'lib/api_usuarios_2.php',
		'consultas_instancia' => 'lib/consultas_instancia.php',
		'rest_arai_usuarios' => 'lib/rest_arai_usuarios.php',
		'ci_login' => 'login/ci_login.php',
		'cuadro_autologin' => 'login/cuadro_autologin.php',
		'pant_login' => 'login/pant_login.php',
		'arbol_perfiles_funcionales' => 'menu/arbol_perfiles_funcionales.php',
		'ci_armador_menues' => 'menu/ci_armador_menues.php',
		'form_armado' => 'menu/form_armado.php',
		'pant_armado' => 'menu/pant_armado.php',
		'pant_descripciones' => 'menu/pant_descripciones.php',
		'pant_final' => 'menu/pant_final.php',
		'ci_asociar_menu_perfil' => 'perfiles/asociacion_menu/ci_asociar_menu_perfil.php',
		'ci_editor' => 'perfiles/perfil_datos/ci_editor.php',
		'ci_navegacion' => 'perfiles/perfil_datos/ci_navegacion.php',
		'ci_editor_perfiles' => 'perfiles/perfil_funcional/ci_editor_perfiles.php',
		'ci_navegacion_perfiles' => 'perfiles/perfil_funcional/ci_navegacion_perfiles.php',
		'datos_relacion_perfiles' => 'perfiles/perfil_funcional/datos_relacion_perfiles.php',
		'ei_form_datos_perfil' => 'perfiles/perfil_funcional/ei_form_datos_perfil.php',
		'arbol_restricciones_funcionales' => 'perfiles/restricciones_funcionales/arbol_restricciones_funcionales.php',
		'ci_restricciones_funcionales' => 'perfiles/restricciones_funcionales/ci_restricciones_funcionales.php',
		'pant_restricciones_funcionales' => 'perfiles/restricciones_funcionales/pant_restricciones_funcionales.php',
		'ci_gen_certificado' => 'servicios_web/generar_certificados/ci_gen_certificado.php',
		'ci_servicios_ofrecidos' => 'servicios_web/keystore_clientes/ci_servicios_ofrecidos.php',
		'pant_edicion_serv_ofrecidos' => 'servicios_web/keystore_clientes/pant_edicion_serv_ofrecidos.php',
		'ci_servicios_consumidos' => 'servicios_web/keystore_servidores/ci_servicios_consumidos.php',
		'cuadro_servicios_consumidos' => 'servicios_web/keystore_servidores/cuadro_servicios_consumidos.php',
		'form_basicos' => 'servicios_web/rest/servicios_consumidos/form_basicos.php',
		'caso_base' => 'testing/selenium/basics/caso_base.php',
		'test_login' => 'testing/selenium/test_login/test_login.php',
		'test_selenium_autoload' => 'testing/selenium/test_selenium_autoload.php',
		'test_usuarios' => 'testing/selenium/test_usuarios/test_usuarios.php',
		'toba_usuarios_autoload' => 'toba_usuarios_autoload.php',
		'apdb_usuario_basicas' => 'usuarios/apdb_usuario_basicas.php',
		'ei_form_basica' => 'usuarios/ei_form_basica.php',
		'ei_form_filtro_usuarios' => 'usuarios/ei_form_filtro_usuarios.php',
		'form_ml_resp_secreta' => 'usuarios/form_ml_resp_secreta.php',
		'gestion_arai_usuarios' => 'usuarios/gestion_arai_usuarios.php',
		'ci_2fa_por_perfil' => 'usuarios/segundo_factor/ci_2fa_por_perfil.php',
		'ci_seleccion_usuario' => 'usuarios/seleccion_usuario/ci_seleccion_usuario.php',
	);
}
?>