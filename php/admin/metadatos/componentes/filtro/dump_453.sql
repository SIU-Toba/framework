------------------------------------------------------------
--[453]--  AUDITORIA - Solicitud Consola 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '453', NULL, NULL, 'toba', 'objeto_filtro', NULL, NULL, NULL, NULL, 'AUDITORIA - Solicitud Consola', NULL, NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-07-05 16:29:44');
INSERT INTO apex_objeto_filtro (objeto_filtro_proyecto, objeto_filtro, dimension_proyecto, dimension, etiqueta, tabla, columna, orden, requerido, no_interactivo, predeterminado) VALUES ('toba', '453', 'toba', 'buscar_ereg', 'Llamada', NULL, 'llamada', '1', NULL, NULL, NULL);
INSERT INTO apex_objeto_filtro (objeto_filtro_proyecto, objeto_filtro, dimension_proyecto, dimension, etiqueta, tabla, columna, orden, requerido, no_interactivo, predeterminado) VALUES ('toba', '453', 'toba', 'Cronometro', NULL, NULL, '(SELECT COUNT(*) FROM apex_solicitud_cronometro soc WHERE soc.solicitud = s.solicitud)', '2', NULL, NULL, NULL);
INSERT INTO apex_objeto_filtro (objeto_filtro_proyecto, objeto_filtro, dimension_proyecto, dimension, etiqueta, tabla, columna, orden, requerido, no_interactivo, predeterminado) VALUES ('toba', '453', 'toba', 'lapso', 'Lapso', NULL, 'date_part(\'month\',s.momento) %-%
date_part(\'year\',s.momento) %-%
date_part(\'month\',s.momento) %-%
date_part(\'year\',s.momento)', '4', NULL, NULL, NULL);
INSERT INTO apex_objeto_filtro (objeto_filtro_proyecto, objeto_filtro, dimension_proyecto, dimension, etiqueta, tabla, columna, orden, requerido, no_interactivo, predeterminado) VALUES ('toba', '453', 'toba', 'tiempo', 'Tiempo ejecucion', NULL, 'tiempo_respuesta', '3', NULL, NULL, NULL);
