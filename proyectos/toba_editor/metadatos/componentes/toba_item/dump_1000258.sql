------------------------------------------------------------
--[1000258]--  Catálogo 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'92', --item_id
	'toba_editor', --proyecto
	'1000258', --item
	'95', --padre_id
	'toba_editor', --padre_proyecto
	'1000264', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba_editor', --pagina_tipo_proyecto
	'editor_frame_izq', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'especifico', --actividad_patron
	'Catálogo', --nombre
	'acciones/configuracion/catalogo.php', --descripcion
	'12', --punto_montaje
	NULL, --actividad_accion
	'0', --menu
	'0', --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	NULL, --solicitud_registrar_cron
	NULL, --prueba_directorios
	NULL, --zona_proyecto
	NULL, --zona
	NULL, --zona_orden
	'0', --zona_listar
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	'0', --publico
	'0', --redirecciona
	NULL, --usuario
	'0', --exportable
	'2003-10-07 01:35:54', --creacion
	'0'  --retrasar_headers
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_editor', --proyecto
	'1000258', --item
	'1903', --objeto
	'0', --orden
	NULL  --inicializar
);
