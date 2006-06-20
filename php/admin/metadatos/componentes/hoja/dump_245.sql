------------------------------------------------------------
--[245]--  ESTAD. - Listado O x  P 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '245', NULL, NULL, 'toba', 'objeto_hoja', NULL, NULL, NULL, NULL, 'ESTAD. - Listado O x  P', NULL, NULL, 'Listado de objetos generados por proyecto', 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-03-08 15:30:20');
INSERT INTO apex_objeto_hoja (objeto_hoja_proyecto, objeto_hoja, sql, ancho, total_y, total_x, total_x_formato, columna_entrada, ordenable, grafico, graf_columnas, graf_filas, graf_gen_invertir, graf_gen_invertible, graf_gen_ancho, graf_gen_alto) VALUES ('toba', '245', 'SELECT	o.clase,
o.clase,
p.proyecto,
p.descripcion,
count(*)
FROM apex_proyecto p,
apex_objeto o
WHERE o.proyecto = p.proyecto
AND o.clase <> \'objeto\'
GROUP BY 1,2,3,4
ORDER BY 1,2,3,4;', NULL, '1', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('toba', '245', '0', '3', NULL, '1', '4', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('toba', '245', '1', '4', 'Clases', '1', '4', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('toba', '245', '2', '5', NULL, '1', '4', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('toba', '245', '3', '6', 'Proyectos', '5', '6', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('toba', '245', '4', '7', 'Objetos', '1', '0', NULL, NULL, NULL, NULL);
