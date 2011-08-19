------------------------------------------------------------
--[1711]--  Cuadro cortes estandar - Sum adhoc 2 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_referencia', --proyecto
	'1711', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ei_cuadro', --clase
	'12000003', --punto_montaje
	'extension_cuadro_sumarizacion_b', --subclase
	'componentes/ei_cuadro - cortes control/extension_cuadro_sumarizacion_b.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Cuadro cortes estandar - Sum adhoc 2', --nombre
	'Localidades de Santa Fe', --titulo
	'0', --colapsable
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
	'2005-11-07 19:59:13', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_cuadro
------------------------------------------------------------
INSERT INTO apex_objeto_cuadro (objeto_cuadro_proyecto, objeto_cuadro, titulo, subtitulo, sql, columnas_clave, columna_descripcion, clave_dbr, archivos_callbacks, ancho, ordenar, paginar, tamano_pagina, tipo_paginado, mostrar_total_registros, eof_invisible, eof_customizado, siempre_con_titulo, exportar_paginado, exportar, exportar_rtf, pdf_propiedades, pdf_respetar_paginacion, asociacion_columnas, ev_seleccion, ev_eliminar, dao_nucleo_proyecto, dao_nucleo, dao_metodo, dao_parametros, desplegable, desplegable_activo, scroll, scroll_alto, cc_modo, cc_modo_anidado_colap, cc_modo_anidado_totcol, cc_modo_anidado_totcua) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	NULL, --titulo
	NULL, --subtitulo
	NULL, --sql
	'zona_id, dep_id, loc_id', --columnas_clave
	NULL, --columna_descripcion
	'0', --clave_dbr
	NULL, --archivos_callbacks
	'100%', --ancho
	'0', --ordenar
	'0', --paginar
	NULL, --tamano_pagina
	'P', --tipo_paginado
	'0', --mostrar_total_registros
	'0', --eof_invisible
	NULL, --eof_customizado
	'0', --siempre_con_titulo
	NULL, --exportar_paginado
	'1', --exportar
	'1', --exportar_rtf
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
	'0', --scroll
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
INSERT INTO apex_objeto_cuadro_cc (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_cc, identificador, descripcion, orden, columnas_id, columnas_descripcion, pie_contar_filas, pie_mostrar_titular, pie_mostrar_titulos, imp_paginar, modo_inicio_colapsado) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'12', --objeto_cuadro_cc
	'departamento', --identificador
	'Departamento', --descripcion
	'2', --orden
	'dep_id', --columnas_id
	'departamento', --columnas_descripcion
	'0', --pie_contar_filas
	NULL, --pie_mostrar_titular
	'0', --pie_mostrar_titulos
	NULL, --imp_paginar
	'0'  --modo_inicio_colapsado
);
INSERT INTO apex_objeto_cuadro_cc (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_cc, identificador, descripcion, orden, columnas_id, columnas_descripcion, pie_contar_filas, pie_mostrar_titular, pie_mostrar_titulos, imp_paginar, modo_inicio_colapsado) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'13', --objeto_cuadro_cc
	'zona', --identificador
	'Zona', --descripcion
	'1', --orden
	'zona_id', --columnas_id
	'zona', --columnas_descripcion
	'0', --pie_contar_filas
	NULL, --pie_mostrar_titular
	'0', --pie_mostrar_titulos
	NULL, --imp_paginar
	'0'  --modo_inicio_colapsado
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_ei_cuadro_columna
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'377', --objeto_cuadro_col
	'localidad', --clave
	'1', --orden
	'Localidad', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-tex-p2', --estilo
	NULL, --ancho
	'5', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	NULL, --total
	'', --total_cc
	NULL, --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	NULL, --permitir_html
	NULL, --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'378', --objeto_cuadro_col
	'hab_varones', --clave
	'2', --orden
	'Hab. Varones', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-num-p1', --estilo
	NULL, --ancho
	'7', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	'', --total_cc
	NULL, --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	NULL, --permitir_html
	NULL, --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'379', --objeto_cuadro_col
	'hab_mujeres', --clave
	'3', --orden
	'Hab. Mujeres', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-num-p1', --estilo
	NULL, --ancho
	'7', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	'', --total_cc
	NULL, --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	NULL, --permitir_html
	NULL, --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'380', --objeto_cuadro_col
	'hab_total', --clave
	'4', --orden
	'Hab. Total', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-num-p1', --estilo
	NULL, --ancho
	'7', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	'departamento,zona', --total_cc
	NULL, --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	NULL, --permitir_html
	NULL, --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'381', --objeto_cuadro_col
	'superficie', --clave
	'5', --orden
	'Superficie', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-num-p1', --estilo
	NULL, --ancho
	'17', --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'1', --total
	'departamento,zona', --total_cc
	NULL, --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	NULL, --permitir_html
	NULL, --grupo
	NULL  --evento_asociado
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_cuadro_col_cc
------------------------------------------------------------
INSERT INTO apex_objeto_cuadro_col_cc (objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, total) VALUES (
	'12', --objeto_cuadro_cc
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'380', --objeto_cuadro_col
	'1'  --total
);
INSERT INTO apex_objeto_cuadro_col_cc (objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, total) VALUES (
	'13', --objeto_cuadro_cc
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'380', --objeto_cuadro_col
	'1'  --total
);
INSERT INTO apex_objeto_cuadro_col_cc (objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, total) VALUES (
	'12', --objeto_cuadro_cc
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'381', --objeto_cuadro_col
	'1'  --total
);
INSERT INTO apex_objeto_cuadro_col_cc (objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, total) VALUES (
	'13', --objeto_cuadro_cc
	'toba_referencia', --objeto_cuadro_proyecto
	'1711', --objeto_cuadro
	'381', --objeto_cuadro_col
	'1'  --total
);
