------------------------------------------------------------
--[1000232]--  ELEMENTO de FORMULARIO (EF) 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'180', --item_id
	'toba_editor', --proyecto
	'1000232', --item
	'177', --padre_id
	'toba_editor', --padre_proyecto
	'1000264', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'titulo', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'abms_cuadro_proyecto', --actividad_patron
	'ELEMENTO de FORMULARIO (EF)', --nombre
	NULL, --descripcion
	'12', --punto_montaje
	'', --actividad_accion
	'0', --menu
	'135', --orden
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
	'apex', --imagen_recurso_origen
	'objetos/abms_ef.gif', --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --publico
	'0', --redirecciona
	NULL, --usuario
	'0', --exportable
	'2004-04-12 14:47:48', --creacion
	NULL  --retrasar_headers
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_item_info
------------------------------------------------------------
INSERT INTO apex_item_info (item_id, item_proyecto, item, descripcion_breve, descripcion_larga) VALUES (
	NULL, --item_id
	'toba_editor', --item_proyecto
	'1000232', --item
	NULL, --descripcion_breve
	'<para>Este es un parrafo de prueba,
Este es un parrafo de prueba,
Este es un parrafo de prueba,
Este es un parrafo de prueba,</para>'  --descripcion_larga
);
