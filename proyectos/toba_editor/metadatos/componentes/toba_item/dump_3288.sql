------------------------------------------------------------
--[3288]--  Grupo de Acceso - Items 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'3287', --item_id
	'toba_editor', --proyecto
	'3288', --item
	NULL, --padre_id
	'toba_editor', --padre_proyecto
	'1000260', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'titulo', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'especifico', --actividad_patron
	'Grupo de Acceso - Items', --nombre
	NULL, --descripcion
	'12', --punto_montaje
	'acciones/usuarios/editor_grupos_asignaritems.php', --actividad_accion
	'0', --menu
	'11', --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	'0', --solicitud_registrar_cron
	NULL, --prueba_directorios
	'toba_editor', --zona_proyecto
	'zona_grupo_acceso', --zona
	'2', --zona_orden
	'1', --zona_listar
	'apex', --imagen_recurso_origen
	'usuarios/grupo.gif', --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	'0', --publico
	'0', --redirecciona
	NULL, --usuario
	'0', --exportable
	NULL, --creacion
	'0'  --retrasar_headers
);
--- FIN Grupo de desarrollo 0
