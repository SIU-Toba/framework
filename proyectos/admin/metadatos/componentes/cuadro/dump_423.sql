------------------------------------------------------------
--[423]--  AUDITORIA - Solicitudes Browser 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '423', NULL, NULL, 'toba', 'objeto_cuadro', 'objeto_cuadro_solicitudes', 'acciones/actividad/cuadro_solicitudes.php', NULL, NULL, 'AUDITORIA - Solicitudes Browser', 'Solicitudes Browser', NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2004-06-16 16:35:55');
INSERT INTO apex_objeto_cuadro (objeto_cuadro_proyecto, objeto_cuadro, titulo, subtitulo, sql, columnas_clave, clave_dbr, archivos_callbacks, ancho, ordenar, paginar, tamano_pagina, tipo_paginado, eof_invisible, eof_customizado, exportar, exportar_rtf, pdf_propiedades, pdf_respetar_paginacion, asociacion_columnas, ev_seleccion, ev_eliminar, dao_nucleo_proyecto, dao_nucleo, dao_metodo, dao_parametros, desplegable, desplegable_activo, scroll, scroll_alto, cc_modo, cc_modo_anidado_colap, cc_modo_anidado_totcol, cc_modo_anidado_totcua) VALUES ('admin', '423', 'Solicitudes Browser', NULL, 'SELECT  se.sesion_browser as sesion_browser,
se.usuario as usuario,
s.solicitud as solicitud,
s.item_proyecto || \' - \' || s.item as item,
s.momento as momento,
s.tiempo_respuesta as tiempo_respuesta,
sob.ip as ip,
COUNT(sc.solicitud) as cronometro,

(SELECT COUNT(*) FROM apex_solicitud_observacion sso WHERE sso.solicitud = s.solicitud %w%) as observacion,

(SELECT COUNT(*) FROM apex_solicitud_obj_observacion soo WHERE soo.solicitud = s.solicitud  %w%) as observacion_obj

FROM apex_solicitud_browser sob,  apex_sesion_browser se,
apex_solicitud s LEFT OUTER JOIN apex_solicitud_cronometro sc
ON sc.solicitud = s.solicitud

WHERE   s.solicitud = sob.solicitud_browser
AND     sob.sesion_browser = se.sesion_browser 
%w%
GROUP BY 1,2,3,4,5,6,7
ORDER BY 1,2;', 'se.sesion_browser', NULL, NULL, '80%', NULL, NULL, NULL, NULL, NULL, 'No existen solicitudes de browser', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '423', '2', 'Item', '4', NULL, NULL, NULL, NULL, 'item', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '423', '3', 'Momento', '0', NULL, NULL, NULL, NULL, 'momento', '5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '423', '4', 'Tiempo Respuesta', '0', NULL, NULL, NULL, NULL, 'tiempo_respuesta', '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '423', '5', 'IP', '0', NULL, NULL, NULL, NULL, 'ip', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '423', '6', 'C.', '7', '1%', NULL, NULL, NULL, 'solicitud', NULL, NULL, NULL, 'procesar_celda_cronometro', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '423', '7', 'Obs.', '7', '1%', NULL, NULL, NULL, 'solicitud', NULL, NULL, NULL, 'procesar_celda_observacion', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '423', '8', 'Obj. Obs.', '7', '1%', NULL, NULL, NULL, 'solicitud', NULL, NULL, NULL, 'procesar_celda_observacion_obj', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL);
