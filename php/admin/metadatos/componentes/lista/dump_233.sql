------------------------------------------------------------
--[233]--  Infra - NIVELES de EJECUCION 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '233', NULL, '0', 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'Infra - NIVELES de EJECUCION', 'Niveles de Ejecucion', NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-12-17 14:19:12');
INSERT INTO apex_objeto_info (objeto_proyecto, objeto, descripcion_breve, descripcion_larga) VALUES ('toba', '233', 'Información asociada.', NULL);
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('toba', '233', 'Niveles de Ejecucion', NULL, 'SELECT nivel_ejecucion, descripcion
FROM apex_nivel_ejecucion;', '0=>\"t\", 1=>\"t\"', 'Identificacion, Descripcion', NULL, '400', NULL, NULL, '0', 'abm');
