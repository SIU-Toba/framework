------------------------------------------------------------
--[2654]--  ABM de Juegos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'2653', --item_id
	'toba_referencia', --proyecto
	'2654', --item
	NULL, --padre_id
	'toba_referencia', --padre_proyecto
	'1000200', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba_referencia', --pagina_tipo_proyecto
	'referencia', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'CI', --actividad_patron
	'ABM de Juegos', --nombre
	NULL, --descripcion
	NULL, --punto_montaje
	'', --actividad_accion
	'1', --menu
	'1', --orden
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
	NULL, --creacion
	'1'  --retrasar_headers
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_referencia', --proyecto
	'2654', --item
	'1721', --objeto
	'0', --orden
	NULL  --inicializar
);

------------------------------------------------------------
-- apex_item_permisos_tablas
------------------------------------------------------------
INSERT INTO apex_item_permisos_tablas (proyecto, item, fuente_datos, esquema, tabla, permisos) VALUES (
	'toba_referencia', --proyecto
	'2654', --item
	'toba_referencia', --fuente_datos
	'referencia', --esquema
	'ref_juegos', --tabla
	'select,insert,update,delete'  --permisos
);
