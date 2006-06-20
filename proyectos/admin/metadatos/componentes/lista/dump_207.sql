------------------------------------------------------------
--[207]--  Infra - Listado PROYECTO 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '207', NULL, '0', 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'Infra - Listado PROYECTO', 'Proyectos definidos', NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-10-03 16:09:42');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('admin', '207', 'Proyectos definidos', NULL, 'SELECT proyecto, descripcion, orden
FROM apex_proyecto %f% %w%
ORDER BY orden;', '0 => \"t\", 1 => \"t\", 2 => \"n\"', 'Identificacion, Descripcion, Orden', NULL, '400', NULL, NULL, '0', NULL);
