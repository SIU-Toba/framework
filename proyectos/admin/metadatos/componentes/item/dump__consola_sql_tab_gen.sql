------------------------------------------------------------
--[/consola/sql_tab_gen]--  Generador de SQL administrativo (TABLAS) 
------------------------------------------------------------
INSERT INTO apex_item (proyecto, padre_proyecto, actividad_buffer_proyecto, zona_proyecto, item_id, item, padre_id, padre, carpeta, nivel_acceso, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer, actividad_patron_proyecto, actividad_patron, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, solicitud_tipo, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, usuario, creacion) VALUES ('admin','admin','admin',NULL, '270', '/consola/sql_tab_gen', '1', '/consola', '0', '0', 'toba', 'consumidor_html', 'Generador de SQL administrativo (TABLAS)', NULL, '0', 'toba', 'especifico', 'acciones/consola/sql_tab_gen.php', NULL, '355', NULL, 'toba', 'item_datos', NULL, NULL, NULL, 'consola', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-03-09 05:25:19');
INSERT INTO apex_item_info (item_proyecto, item_id, item, descripcion_breve, descripcion_larga) VALUES ('admin','270', '/consola/sql_tab_gen', 'Generacion de SQL Administrativo de TABLAS
a partir de los metadatos existentes
en las tablas \"apex_mod_datos_*\"', '<para>-p <proyecto> -- Generar SQL para unproyecto
-t <regex> -- Filtrar las tablas
-h --Incluir tablas historicas
-x   Seleccionar el modo de ejecucion
        \"del\" -- SQL de vaciado
        \"del_full\" -- SQL de vaciado sin restricciones
        \"drop\" -- Eliminacion de tablas
        \"vac\" -- Vacuum de tablas</para>');
