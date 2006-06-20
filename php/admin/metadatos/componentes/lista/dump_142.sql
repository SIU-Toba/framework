------------------------------------------------------------
--[142]--  ITEM - Objetos asociados 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '142', NULL, '0', 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'ITEM - Objetos asociados', 'Objetos Asociados', NULL, '', 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-09-19 08:50:40');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('toba', '142', 'Objetos Asociados', NULL, 'SELECT i.item, 
o.clase, 
i.orden, 
o.nombre, 
i.objeto,
i.proyecto
FROM apex_item_objeto i,
apex_objeto o %f%
WHERE i.objeto = o.objeto 
AND i.proyecto = o.proyecto
%w%
ORDER BY 2,3;', '1=>\"t\",2=>\"n\",3=>\"t\",4=>\"n\"', 'Clase, Orden, Nombre, Objeto', NULL, '500', NULL, NULL, '0,5,4', '0');
