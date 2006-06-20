------------------------------------------------------------
--[472]--  PROYECTO - Tareas concluidas 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '472', NULL, NULL, 'toba', 'objeto_hoja', NULL, NULL, NULL, NULL, 'PROYECTO - Tareas concluidas', NULL, NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-07-22 04:16:30');
INSERT INTO apex_objeto_hoja (objeto_hoja_proyecto, objeto_hoja, sql, ancho, total_y, total_x, total_x_formato, columna_entrada, ordenable, grafico, graf_columnas, graf_filas, graf_gen_invertir, graf_gen_invertible, graf_gen_ancho, graf_gen_alto) VALUES ('admin', '472', 'SELECT v.version as version,
v.version as version,
t.tarea as tarea,
t.tarea as tarea,
tm.descripcion as tema_desc,
tt.descripcion as tarea_tipo,
t.descripcion as descripcion
FROM apex_ap_tarea t,
apex_ap_tarea_tema tm,
apex_ap_version v,
apex_ap_tarea_tipo tt
WHERE t.version = v.version
AND t.version_proyecto = v.proyecto
AND t.tarea_tema = tm.tarea_tema
AND t.tarea_tipo = tt.tarea_tipo
AND t.tarea_estado = 3
GROUP BY 1, 2,3,4,5,6,7
ORDER BY 1 DESC , 2,5,6,7;', '600', NULL, NULL, '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '472', '1', '1', NULL, '1', '0', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '472', '2', '2', 'Version', '1', '0', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '472', '3', '5', NULL, '1', '0', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '472', '4', '6', NULL, '1', '0', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '472', '5', '7', 'Tema', '1', '4', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '472', '6', '7', 'Tipo', '1', '4', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '472', '7', '7', 'Descripcion', '14', '4', NULL, NULL, NULL, NULL);
