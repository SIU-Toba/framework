------------------------------------------------------------
--[126]--  Infra - Listado PATRONES 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '126', NULL, '0', 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'Infra - Listado PATRONES', 'Patrones existentes', NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-08-30 03:39:34');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('admin', '126', 'Patrones existentes', NULL, 'SELECT patron, archivo 
FROM apex_patron %f% %w%
ORDER BY 2', '0=>\"t\", 1=>\"t\"', 'patron, Archivo', NULL, '500', NULL, NULL, '0', NULL);
