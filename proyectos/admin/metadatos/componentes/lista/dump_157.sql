------------------------------------------------------------
--[157]--  OBJETO - Notas 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '157', NULL, '0', 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'OBJETO - Notas', 'Notas', NULL, '', 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-09-23 07:47:51');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('admin', '157', 'Notas', NULL, 'SELECT objeto_nota, creacion,
nota_tipo, usuario_origen, usuario_destino, texto
FROM apex_objeto_nota %f% %w%
ORDER BY creacion DESC;', '1=>\"t\", 2=>\"t\",3=>\"t\",4=>\"t\",5=>\"t\"', 'Alta, Tipo, De, Para, Texto', '5=>\"formato_salto_linea_html\"', '400', NULL, NULL, '0', 'abm');
