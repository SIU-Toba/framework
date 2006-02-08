------------------------------------------------------------
--[/consola/sql_tab_gen]--  Generador de SQL administrativo (TABLAS) 
------------------------------------------------------------
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, usuario, creacion) VALUES ('270', 'toba', '/consola/sql_tab_gen', '1', 'toba', '/consola', '0', '0', 'consola', 'toba', 'consumidor_html', 'Generador de SQL administrativo (TABLAS)', NULL, 'toba', '0', 'toba', 'especifico', 'acciones/consola/sql_tab_gen.php', NULL, '355', NULL, 'toba', 'item_datos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-03-09 05:25:19');
INSERT INTO apex_item_info (item_id, item_proyecto, item, descripcion_breve, descripcion_larga) VALUES ('270', 'toba', '/consola/sql_tab_gen', 'Generacion de SQL Administrativo de TABLAS
a partir de los metadatos existentes
en las tablas \"apex_mod_datos_*\"', '<para>-p <proyecto> -- Generar SQL para unproyecto
-t <regex> -- Filtrar las tablas
-h --Incluir tablas historicas
-x   Seleccionar el modo de ejecucion
        \"del\" -- SQL de vaciado
        \"del_full\" -- SQL de vaciado sin restricciones
        \"drop\" -- Eliminacion de tablas
        \"vac\" -- Vacuum de tablas</para>');
