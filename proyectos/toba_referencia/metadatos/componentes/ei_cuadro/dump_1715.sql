------------------------------------------------------------
--[1715]--  Cuadro cortes estandar - (completo extendido) 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_referencia', --proyecto
	'1715', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'objeto_ei_cuadro', --clase
	'extension_cuadro_full', --subclase
	'componentes/ei_cuadro - cortes control/extension_cuadro_full.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Cuadro cortes estandar - (completo extendido)', --nombre
	'Localidades de Santa Fe', --titulo
	NULL, --colapsable
	NULL, --descripcion
	NULL, --fuente_datos_proyecto
	NULL, --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2005-11-09 01:08:54'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_cuadro
------------------------------------------------------------
INSERT INTO apex_objeto_cuadro (objeto_cuadro_proyecto, objeto_cuadro, titulo, subtitulo, sql, columnas_clave, clave_dbr, archivos_callbacks, ancho, ordenar, paginar, tamano_pagina, tipo_paginado, eof_invisible, eof_customizado, exportar, exportar_rtf, pdf_propiedades, pdf_respetar_paginacion, asociacion_columnas, ev_seleccion, ev_eliminar, dao_nucleo_proyecto, dao_nucleo, dao_metodo, dao_parametros, desplegable, desplegable_activo, scroll, scroll_alto, cc_modo, cc_modo_anidado_colap, cc_modo_anidado_totcol, cc_modo_anidado_totcua) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	NULL, --titulo
	NULL, --subtitulo
	NULL, --sql
	'zona_id, dep_id, loc_id', --columnas_clave
	NULL, --clave_dbr
	NULL, --archivos_callbacks
	'100%', --ancho
	NULL, --ordenar
	NULL, --paginar
	NULL, --tamano_pagina
	NULL, --tipo_paginado
	NULL, --eof_invisible
	NULL, --eof_customizado
	NULL, --exportar
	NULL, --exportar_rtf
	NULL, --pdf_propiedades
	NULL, --pdf_respetar_paginacion
	NULL, --asociacion_columnas
	NULL, --ev_seleccion
	NULL, --ev_eliminar
	NULL, --dao_nucleo_proyecto
	NULL, --dao_nucleo
	NULL, --dao_metodo
	NULL, --dao_parametros
	NULL, --desplegable
	NULL, --desplegable_activo
	NULL, --scroll
	NULL, --scroll_alto
	't', --cc_modo
	'0', --cc_modo_anidado_colap
	NULL, --cc_modo_anidado_totcol
	NULL  --cc_modo_anidado_totcua
);

------------------------------------------------------------
-- apex_objeto_cuadro_cc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_cuadro_cc (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_cc, identificador, descripcion, orden, columnas_id, columnas_descripcion, pie_contar_filas, pie_mostrar_titular, pie_mostrar_titulos, imp_paginar) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	'20', --objeto_cuadro_cc
	'departamento', --identificador
	'Departamento', --descripcion
	'2', --orden
	'dep_id', --columnas_id
	'departamento', --columnas_descripcion
	'1', --pie_contar_filas
	'1', --pie_mostrar_titular
	'0', --pie_mostrar_titulos
	NULL  --imp_paginar
);
INSERT INTO apex_objeto_cuadro_cc (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_cc, identificador, descripcion, orden, columnas_id, columnas_descripcion, pie_contar_filas, pie_mostrar_titular, pie_mostrar_titulos, imp_paginar) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	'21', --objeto_cuadro_cc
	'zona', --identificador
	'Zona', --descripcion
	'1', --orden
	'zona_id', --columnas_id
	'zona', --columnas_descripcion
	'0', --pie_contar_filas
	'1', --pie_mostrar_titular
	'1', --pie_mostrar_titulos
	NULL  --imp_paginar
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_ei_cuadro_columna
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	'397', --objeto_cuadro_col
	'localidad', --clave
	'1', --orden
	'Localidad', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'5', --estilo
	NULL, --ancho
	'5', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	NULL, --total
	''  --total_cc
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	'398', --objeto_cuadro_col
	'hab_varones', --clave
	'2', --orden
	'Hab. Varones', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'0', --estilo
	NULL, --ancho
	'7', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	''  --total_cc
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	'399', --objeto_cuadro_col
	'hab_mujeres', --clave
	'3', --orden
	'Hab. Mujeres', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'0', --estilo
	NULL, --ancho
	'7', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	''  --total_cc
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	'400', --objeto_cuadro_col
	'hab_total', --clave
	'4', --orden
	'Hab. Total', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'0', --estilo
	NULL, --ancho
	'7', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	'departamento,zona'  --total_cc
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1715', --objeto_cuadro
	'401', --objeto_cuadro_col
	'superficie', --clave
	'5', --orden
	'Superficie', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'0', --estilo
	NULL, --ancho
	'17', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	'departamento,zona'  --total_cc
);
--- FIN Grupo de desarrollo 0
