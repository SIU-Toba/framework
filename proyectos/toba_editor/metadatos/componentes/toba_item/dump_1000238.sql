------------------------------------------------------------
--[1000238]--  CARPETA - Editor 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'285', --item_id
	'toba_editor', --proyecto
	'1000238', --item
	'9', --padre_id
	'toba_editor', --padre_proyecto
	'1000266', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'titulo', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'abms_cd_c', --actividad_patron
	'CARPETA - Editor', --nombre
	NULL, --descripcion
	'12', --punto_montaje
	NULL, --actividad_accion
	'1', --menu
	'7', --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	'0', --solicitud_registrar_cron
	NULL, --prueba_directorios
	'toba_editor', --zona_proyecto
	'zona_carpeta', --zona
	'4', --zona_orden
	'1', --zona_listar
	'apex', --imagen_recurso_origen
	'nucleo/carpeta.gif', --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	'0', --publico
	'0', --redirecciona
	'juan', --usuario
	'0', --exportable
	'2003-09-18 01:36:51', --creacion
	'0'  --retrasar_headers
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_editor', --proyecto
	'1000238', --item
	'1000158', --objeto
	'0', --orden
	NULL  --inicializar
);
