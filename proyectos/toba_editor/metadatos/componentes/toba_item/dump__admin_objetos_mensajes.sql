------------------------------------------------------------
--[/admin/objetos/mensajes]--  Propiedades - Mensajes 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, creacion) VALUES (
	'455', --item_id
	'toba_editor', --proyecto
	'/admin/objetos/mensajes', --item
	'97', --padre_id
	'toba_editor', --padre_proyecto
	'/admin/objetos_toba', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'titulo', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'abms_cd_c', --actividad_patron
	'Propiedades - Mensajes', --nombre
	'Mensajes asociados al componente. Forma parte del esquema de [wiki:Referencia/Mensajes Mensajes y Notificaciones]', --descripcion
	NULL, --actividad_accion
	'0', --menu
	'15', --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	'0', --solicitud_registrar_cron
	NULL, --prueba_directorios
	'toba_editor', --zona_proyecto
	'zona_objeto', --zona
	'0', --zona_orden
	'1', --zona_listar
	'apex', --imagen_recurso_origen
	'mensaje.gif', --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	'0', --publico
	'0', --redirecciona
	NULL, --usuario
	'2004-08-25 05:54:04'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_editor', --proyecto
	'/admin/objetos/mensajes', --item
	'1000189', --objeto
	'0', --orden
	NULL  --inicializar
);
