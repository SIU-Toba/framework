------------------------------------------------------------
--[116]--  Infra - ABMS items 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '116', NULL, '0', 'toba', 'objeto_lista', NULL, NULL, 'toba', NULL, 'Infra - ABMS items', 'Listado de ABMS-ITEMs', NULL, 'Lista de items generados', 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-08-28 00:29:06');
INSERT INTO apex_objeto_lista (objeto_lista_proyecto, objeto_lista, titulo, subtitulo, sql, col_ver, col_titulos, col_formato, ancho, ordenar, exportar, vinculo_clave, vinculo_indice) VALUES ('admin', '116', 'Listado de ABMS-ITEMs', '', 'SELECT 	a.objeto_abms, 
a.tabla, 
i.orden, 
i.columna, 
i.elemento_formulario,
i.ef_ini
FROM apl_objeto_abms_item i, apl_objeto_abms a %f%
WHERE a.objeto_abms = i.objeto_abms %w%
ORDER BY 1,3;', '0=>\"n\",1=>\"t\",2=>\"n\",3=>\"t\",4=>\"t\",5=>\"t\"', 'Objeto, Tabla, Orden,Columna, EF, Inicializacion', '', '700', '0', NULL, '0,3', 'editar');
