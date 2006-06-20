------------------------------------------------------------
--[211]--  USUARIO - Perfil (dimensiones) 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '211', NULL, '0', 'toba', 'objeto_cuadro', 'objeto_cuadro_perfil', 'acciones/obsoletos/usuarios/editor_perfiles_clases.php', 'toba', NULL, 'USUARIO - Perfil (dimensiones)', 'Dimensiones restringidas', NULL, NULL, 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2003-10-29 14:33:09');
INSERT INTO apex_objeto_cuadro (objeto_cuadro_proyecto, objeto_cuadro, titulo, subtitulo, sql, columnas_clave, clave_dbr, archivos_callbacks, ancho, ordenar, paginar, tamano_pagina, tipo_paginado, eof_invisible, eof_customizado, exportar, exportar_rtf, pdf_propiedades, pdf_respetar_paginacion, asociacion_columnas, ev_seleccion, ev_eliminar, dao_nucleo_proyecto, dao_nucleo, dao_metodo, dao_parametros, desplegable, desplegable_activo, scroll, scroll_alto, cc_modo, cc_modo_anidado_colap, cc_modo_anidado_totcol, cc_modo_anidado_totcua) VALUES ('admin', '211', 'Dimensiones restringidas', NULL, 'SELECT p.usuario_perfil_datos_proyecto as usu_proy,
p.usuario_perfil_datos as usu,
d.proyecto as dim_proy,
d.dimension as dim,
d.nombre as nombre, 
d.dimension_tipo as tipo, 
t.nombre as tipo_nombre,
d.fuente_datos as fuente,
t.item_editor_restric_proyecto as editor_proyecto,
t.item_editor_restric as editor,
t.ventana_editor_x as x,
t.ventana_editor_y as y
FROM apex_dimension d,
apex_dimension_perfil_datos p,
apex_dimension_tipo t
WHERE d.dimension = p.dimension
AND d.proyecto = p.dimension_proyecto
AND t.dimension_tipo = d.dimension_tipo
AND t.proyecto = d.dimension_tipo_proyecto
%w% ORDER BY 2;', 'usu_proy, usu, dim_proy, dim', NULL, NULL, '400', NULL, NULL, NULL, NULL, NULL, 'No existe ninguna dimension RESTRINGIDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '211', '-1', 'D', '7', '1%', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'navegar_dimension', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '211', '0', 'Res.', '0', '1%', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'presentar_editor', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '211', '1', 'Dimension', '4', NULL, NULL, NULL, NULL, 'nombre', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '211', '1.5', 'Tipo', '4', NULL, NULL, NULL, NULL, 'tipo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, total_cc, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('admin', '211', '2', 'e', '0', '1%', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'abms', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
