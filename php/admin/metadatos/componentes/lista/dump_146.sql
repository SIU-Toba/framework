------------------------------------------------------------
--[146]--  OBJETO - Editor HOJA - Directivas 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '146', NULL, '0', 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'OBJETO - Editor HOJA - Directivas', 'Directivas', NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-09-21 01:07:36');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('toba', '146', 'Directivas', NULL, 'SELECT d.objeto_hoja_proyecto,
d.objeto_hoja,
d.columna,
t.nombre,
d.nombre,
cf.descripcion,
ce.descripcion,
d.par_dimension
FROM %f% 
apex_objeto_hoja_directiva_ti t,
apex_objeto_hoja_directiva d
LEFT OUTER JOIN apex_columna_estilo ce 
ON d.columna_estilo = ce.columna_estilo
LEFT OUTER JOIN apex_columna_formato cf
ON d.columna_formato = cf.columna_formato
WHERE d.objeto_hoja_directiva_tipo = t.objeto_hoja_directiva_tipo
%w%
ORDER BY 1,2,3;', '2=>\"n\",3=>\"t\",4=>\"t\",5=>\"t\",6=>\"t\"', 'Columna,Tipo, Nombre,Formato,Estilo', NULL, '500', NULL, NULL, '0,1,2', 'abm');
