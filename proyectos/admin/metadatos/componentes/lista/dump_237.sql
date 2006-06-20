------------------------------------------------------------
--[237]--  OBJETO - Editor CUADRO - Col 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '237', NULL, NULL, 'toba', 'objeto_lista', NULL, NULL, NULL, NULL, 'OBJETO - Editor CUADRO - Col', 'COLUMNAS definidas', NULL, 'Lista del editor del Cuadro', 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-03-01 16:17:21');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('admin', '237', 'COLUMNAS definidas', NULL, 'SELECT c.objeto_cuadro_proyecto,
c.objeto_cuadro,
c.orden,
c.titulo,
c.valor_sql,
c.valor_fijo,
c.valor_proceso_esp,
c.vinculo_indice,
e.descripcion
FROM %f% apex_objeto_cuadro_columna c,
apex_columna_estilo e
WHERE c.columna_estilo =  e.columna_estilo
%w%
ORDER BY 3;', '2=>\"n\",3=>\"t\",4=>\"t\",5=>\"t\",6=>\"t\",7=>\"t\",8=>\"t\"', 'Orden, Titulo, Valor SQL, Valor fijo, Valor proceso, Vinc., Estilo', NULL, '600', NULL, NULL, '0,1,2', 'abms');
