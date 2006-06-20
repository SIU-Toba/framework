------------------------------------------------------------
--[248]--  ESTAD. -  Listado O x  P (2) 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '248', NULL, NULL, 'toba', 'objeto_hoja', NULL, NULL, NULL, NULL, 'ESTAD. -  Listado O x  P (2)', NULL, NULL, NULL, 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-03-09 19:14:35');
INSERT INTO apex_objeto_hoja (objeto_hoja_proyecto, objeto_hoja, sql, ancho, total_y, total_x, total_x_formato, columna_entrada, ordenable, grafico, graf_columnas, graf_filas, graf_gen_invertir, graf_gen_invertible, graf_gen_ancho, graf_gen_alto) VALUES ('admin', '248', 'SELECT	p.proyecto,
p.descripcion,
o.clase,
o.clase,
count(*),
sum(objeto)
FROM apex_proyecto p,
apex_objeto o
WHERE o.proyecto = p.proyecto
AND o.clase <> \'objeto\'
GROUP BY 1,2,3,4
ORDER BY 1,2,3,4;', '500', '1', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '248', '0', '1', NULL, '1', NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '248', '1', '2', 'Clases', '1', NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '248', '2', '5', NULL, '1', NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '248', '3', '6', 'Proyectos', '1', NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '248', '4', '7', 'Objetos', '7', '0', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_hoja_directiva (objeto_hoja_proyecto, objeto_hoja, columna, objeto_hoja_directiva_tipo, nombre, columna_formato, columna_estilo, par_dimension_proyecto, par_dimension, par_tabla, par_columna) VALUES ('admin', '248', '5', '7', 'Suma', '1', '0', NULL, NULL, NULL, NULL);
