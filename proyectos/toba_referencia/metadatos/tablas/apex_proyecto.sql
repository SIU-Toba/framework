
------------------------------------------------------------
-- apex_proyecto
------------------------------------------------------------
INSERT INTO apex_proyecto (proyecto, descripcion, descripcion_corta, estilo, con_frames, frames_clase, frames_archivo, pm_impresion, salida_impr_html_c, salida_impr_html_a, menu, path_includes, path_browser, administrador, listar_multiproyecto, orden, palabra_vinculo_std, version_toba, requiere_validacion, usuario_anonimo, usuario_anonimo_desc, usuario_anonimo_grupos_acc, validacion_intentos, validacion_intentos_min, validacion_bloquear_usuario, validacion_debug, sesion_tiempo_no_interac_min, sesion_tiempo_maximo_min, pm_sesion, sesion_subclase, sesion_subclase_archivo, pm_contexto, contexto_ejecucion_subclase, contexto_ejecucion_subclase_archivo, pm_usuario, usuario_subclase, usuario_subclase_archivo, encriptar_qs, registrar_solicitud, registrar_cronometro, item_inicio_sesion, item_pre_sesion, item_pre_sesion_popup, item_set_sesion, log_archivo, log_archivo_nivel, fuente_datos, pagina_tipo, version, version_fecha, version_detalle, version_link, tiempo_espera_ms, navegacion_ajax, codigo_ga_tracker, extension_toba, extension_proyecto) VALUES (
	'toba_referencia', --proyecto
	'Documentación de Referencia de Toba', --descripcion
	'Toba - Referencia', --descripcion_corta
	'v2_azul', --estilo
	NULL, --con_frames
	NULL, --frames_clase
	NULL, --frames_archivo
	'12000003', --pm_impresion
	NULL, --salida_impr_html_c
	NULL, --salida_impr_html_a
	'css', --menu
	NULL, --path_includes
	NULL, --path_browser
	NULL, --administrador
	'1', --listar_multiproyecto
	NULL, --orden
	NULL, --palabra_vinculo_std
	'trunk', --version_toba
	'1', --requiere_validacion
	'anonimo', --usuario_anonimo
	'Usuario Anónimo', --usuario_anonimo_desc
	'admin', --usuario_anonimo_grupos_acc
	'3', --validacion_intentos
	NULL, --validacion_intentos_min
	'2', --validacion_bloquear_usuario
	'0', --validacion_debug
	NULL, --sesion_tiempo_no_interac_min
	NULL, --sesion_tiempo_maximo_min
	'12000003', --pm_sesion
	NULL, --sesion_subclase
	NULL, --sesion_subclase_archivo
	'12000003', --pm_contexto
	'contexto_ejecucion', --contexto_ejecucion_subclase
	'contexto_ejecucion.php', --contexto_ejecucion_subclase_archivo
	'12000003', --pm_usuario
	NULL, --usuario_subclase
	NULL, --usuario_subclase_archivo
	'0', --encriptar_qs
	'1', --registrar_solicitud
	NULL, --registrar_cronometro
	'3294', --item_inicio_sesion
	'33000033', --item_pre_sesion
	'0', --item_pre_sesion_popup
	NULL, --item_set_sesion
	'1', --log_archivo
	'7', --log_archivo_nivel
	'toba_referencia', --fuente_datos
	'referencia', --pagina_tipo
	NULL, --version
	NULL, --version_fecha
	NULL, --version_detalle
	NULL, --version_link
	'1000', --tiempo_espera_ms
	'0', --navegacion_ajax
	NULL, --codigo_ga_tracker
	FALSE, --extension_toba
	FALSE  --extension_proyecto
);
