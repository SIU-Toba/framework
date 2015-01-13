------------------------------------------------------------
--[3441]--  Editor de Dimensiones 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'3440', --item_id
	'toba_editor', --proyecto
	'3441', --item
	NULL, --padre_id
	'toba_editor', --padre_proyecto
	'3439', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'titulo', --pagina_tipo
	NULL, --actividad_buffer_proyecto
	NULL, --actividad_buffer
	NULL, --actividad_patron_proyecto
	NULL, --actividad_patron
	'Editor de Dimensiones', --nombre
	NULL, --descripcion
	'12', --punto_montaje
	NULL, --actividad_accion
	'0', --menu
	NULL, --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	NULL, --solicitud_registrar_cron
	NULL, --prueba_directorios
	'toba_editor', --zona_proyecto
	'zona_dimension', --zona
	'1', --zona_orden
	'1', --zona_listar
	'apex', --imagen_recurso_origen
	'filtrar.png', --imagen
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

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_editor', --proyecto
	'3441', --item
	'2206', --objeto
	'0', --orden
	NULL  --inicializar
);
