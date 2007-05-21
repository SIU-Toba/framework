
------------------------------------------------------------
-- apex_proyecto
------------------------------------------------------------
INSERT INTO apex_proyecto (proyecto, descripcion, descripcion_corta, estilo, con_frames, frames_clase, frames_archivo, salida_impr_html_c, salida_impr_html_a, menu, path_includes, path_browser, administrador, listar_multiproyecto, orden, palabra_vinculo_std, version_toba, requiere_validacion, usuario_anonimo, usuario_anonimo_desc, usuario_anonimo_grupos_acc, validacion_intentos, validacion_intentos_min, validacion_debug, sesion_tiempo_no_interac_min, sesion_tiempo_maximo_min, sesion_subclase, sesion_subclase_archivo, contexto_ejecucion_subclase, contexto_ejecucion_subclase_archivo, usuario_subclase, usuario_subclase_archivo, encriptar_qs, registrar_solicitud, registrar_cronometro, item_inicio_sesion, item_pre_sesion, item_set_sesion, log_archivo, log_archivo_nivel, fuente_datos, version, version_fecha, version_detalle, version_link) VALUES (
	'toba_editor', --proyecto
	'Editor de Metadatos', --descripcion
	'Toba - Editor', --descripcion_corta
	'cubos', --estilo
	'1', --con_frames
	NULL, --frames_clase
	NULL, --frames_archivo
	NULL, --salida_impr_html_c
	NULL, --salida_impr_html_a
	'css', --menu
	'php', --path_includes
	'/apl', --path_browser
	NULL, --administrador
	'0', --listar_multiproyecto
	'0', --orden
	NULL, --palabra_vinculo_std
	'1.0.0', --version_toba
	'1', --requiere_validacion
	'anonimo', --usuario_anonimo
	'Usuario Anónimo', --usuario_anonimo_desc
	'admin,usuario,documentador', --usuario_anonimo_grupos_acc
	NULL, --validacion_intentos
	NULL, --validacion_intentos_min
	'1', --validacion_debug
	NULL, --sesion_tiempo_no_interac_min
	NULL, --sesion_tiempo_maximo_min
	'sesion_editor', --sesion_subclase
	'customizacion_toba/sesion_editor.php', --sesion_subclase_archivo
	'contexto_ejecucion_editor', --contexto_ejecucion_subclase
	'customizacion_toba/contexto_ejecucion_editor.php', --contexto_ejecucion_subclase_archivo
	NULL, --usuario_subclase
	NULL, --usuario_subclase_archivo
	'0', --encriptar_qs
	'0', --registrar_solicitud
	NULL, --registrar_cronometro
	'/admin/acceso', --item_inicio_sesion
	'3286', --item_pre_sesion
	'3359', --item_set_sesion
	'1', --log_archivo
	'7', --log_archivo_nivel
	'instancia', --fuente_datos
	'1.0.2', --version
	'2006-11-17', --version_fecha
	'SIU-Toba. Ambiente de desarrollo WEB.
Desarrollado por el programa SIU (2003-2006)', --version_detalle
	'www.siu.edu.ar/soluciones/toba'  --version_link
);
