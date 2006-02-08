------------------------------------------------------------
--[221]--  OBJETO - Vinculos 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '221', NULL, NULL, 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'OBJETO - Vinculos', 'Vinculos existentes', NULL, 'Vinculos atachados al OBJETO.', 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-09-23 05:26:17');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('toba', '221', 'Vinculos existentes', NULL, 'SELECT origen_item_proyecto,
origen_item,
origen_objeto_proyecto,
origen_objeto,
destino_item_proyecto,
destino_item,
destino_objeto_proyecto,
destino_objeto,
canal,
indice, 
texto, 
imagen 
FROM apex_vinculo 
%f% %w%
ORDER BY 2,4;', '5=>\"t\", 7=>\"n\", 8=>\"t\",9=>\"t\",10=>\"t\",11=>\"t\"', 'Destino, Objeto, Canal, Indice, Texto, Imagen', NULL, '600', NULL, NULL, '0,1,2,3,4,5,6,7', '0');
