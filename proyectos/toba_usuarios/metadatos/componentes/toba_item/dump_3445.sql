------------------------------------------------------------
--[3445]--  Log de Sesiones y Accesos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	'3444', --item_id
	'toba_usuarios', --proyecto
	'3445', --item
	NULL, --padre_id
	'toba_usuarios', --padre_proyecto
	'3443', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'toba_usuarios', --pagina_tipo_proyecto
	'toba_usuarios_normal', --pagina_tipo
	NULL, --actividad_buffer_proyecto
	NULL, --actividad_buffer
	NULL, --actividad_patron_proyecto
	NULL, --actividad_patron
	'Log de Sesiones y Accesos', --nombre
	'Una sesi�n es el espacio de tiempo desde que el usuario ingresa a la aplicaci�n hasta que se desloguea del mismo o caduca su sesi�n en el servidor. En este �ltimo caso, no hay forma de registrar en el log la hora exacta en que caduca.
<br><br>
Dentro de una sesi�n, el usuario realiza una serie de accesos, estos sucesos se registran dependiendo de la configuraci�n del proyecto y de la operaci�n a la que accede. Cada acceso equivale a un pedido de p�gina o request del navegador al servidor.', --descripcion
	'12000004', --punto_montaje
	NULL, --actividad_accion
	'1', --menu
	'3', --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	'0', --solicitud_registrar_cron
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
	'0'  --retrasar_headers
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'toba_usuarios', --proyecto
	'3445', --item
	'2240', --objeto
	'0', --orden
	NULL  --inicializar
);
