------------------------------------------------------------
--[1000256]--  Editor ei_formulario_ml 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'1218', --item_id
	'toba_editor', --proyecto
	'1000256', --item
	NULL, --padre_id
	'toba_editor', --padre_proyecto
	'1000248', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba', --pagina_tipo_proyecto
	'titulo', --pagina_tipo
	'toba', --actividad_buffer_proyecto
	'0', --actividad_buffer
	'toba', --actividad_patron_proyecto
	'CI', --actividad_patron
	'Editor ei_formulario_ml', --nombre
	'Un [wiki:Referencia/Objetos/ei_formulario_ml formulario_ml] presenta una grilla de campos editables repetidos una cantidad dada de filas. permitiendo recrear la carga de distintos registros con la misma estructura. La definición y uso de la grilla de campos es similar al  [wiki:Referencia/Objetos/ei_formulario formulario simple] con el agregado de lógica para manejar un número arbitrario de filas.', --descripcion
	'12', --punto_montaje
	NULL, --actividad_accion
	'0', --menu
	NULL, --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	'0', --solicitud_registrar_cron
	NULL, --prueba_directorios
	'toba_editor', --zona_proyecto
	'zona_objeto', --zona
	NULL, --zona_orden
	'0', --zona_listar
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	'0', --publico
	'0', --redirecciona
	NULL, --usuario
	'0', --exportable
	'2005-07-18 02:44:01', --creacion
	'0'  --retrasar_headers
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_editor', --proyecto
	'1000256', --item
	'1386', --objeto
	'0', --orden
	NULL  --inicializar
);
