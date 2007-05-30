------------------------------------------------------------
--[/pruebas_item/item_popup]--  Popup 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, creacion) VALUES (
	'1175', --item_id
	'toba_testing', --proyecto
	'/pruebas_item/item_popup', --item
	NULL, --padre_id
	'toba_testing', --padre_proyecto
	'/pruebas_item', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'popup_ef', --pagina_tipo
	'toba_testing', --actividad_buffer_proyecto
	'48', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'especifico', --actividad_patron
	'Popup', --nombre
	NULL, --descripcion
	NULL, --actividad_accion
	NULL, --menu
	NULL, --orden
	NULL, --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	NULL, --solicitud_registrar_cron
	NULL, --prueba_directorios
	NULL, --zona_proyecto
	NULL, --zona
	NULL, --zona_orden
	NULL, --zona_listar
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --publico
	NULL, --redirecciona
	NULL, --usuario
	'2005-03-23 09:35:50'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_testing', --proyecto
	'/pruebas_item/item_popup', --item
	'1149', --objeto
	'0', --orden
	NULL  --inicializar
);
