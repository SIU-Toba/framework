------------------------------------------------------------
--[5000004]--  vinculos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 5
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_referencia', --proyecto
	'5000004', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'objeto_ei_cuadro', --clase
	'cuadro_origen', --subclase
	'componentes/eventos/vinculos/cuadro_origen.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'vinculos', --nombre
	'Cuadro', --titulo
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
	'2006-04-19 04:15:20'  --creacion
);
--- FIN Grupo de desarrollo 5

------------------------------------------------------------
-- apex_objeto_eventos
------------------------------------------------------------

--- INICIO Grupo de desarrollo 5
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES (
	'toba_referencia', --proyecto
	'5000003', --evento_id
	'5000004', --objeto
	'en_fila_redefinido', --identificador
	'Vinculo redefinido en PHP', --etiqueta
	NULL, --maneja_datos
	'1', --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	NULL, --imagen_recurso_origen
	NULL, --imagen
	'0', --en_botonera
	NULL, --ayuda
	'1', --orden
	NULL, --ci_predep
	NULL, --implicito
	NULL, --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	'V', --accion
	NULL, --accion_imphtml_debug
	'/mensajes/vinculos', --accion_vinculo_carpeta
	'3310', --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	'1', --accion_vinculo_popup
	'width: 400px, height: 500px, scrollbars: 1, resizable: 1', --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	'popup'  --accion_vinculo_celda
);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES (
	'toba_referencia', --proyecto
	'5000004', --evento_id
	'5000004', --objeto
	'en_fila_normal', --identificador
	'Vinculo NORMAL', --etiqueta
	NULL, --maneja_datos
	'1', --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	NULL, --imagen_recurso_origen
	NULL, --imagen
	'0', --en_botonera
	NULL, --ayuda
	'2', --orden
	NULL, --ci_predep
	NULL, --implicito
	NULL, --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	'V', --accion
	NULL, --accion_imphtml_debug
	'/mensajes/vinculos', --accion_vinculo_carpeta
	'3310', --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	'1', --accion_vinculo_popup
	'width: 400px, height: 500px, scrollbars: 1, resizable: 1', --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL  --accion_vinculo_celda
);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES (
	'toba_referencia', --proyecto
	'5000005', --evento_id
	'5000004', --objeto
	'en_botonera', --identificador
	'Vinculo redefinido en PHP y JS', --etiqueta
	NULL, --maneja_datos
	'0', --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	NULL, --imagen_recurso_origen
	NULL, --imagen
	'1', --en_botonera
	NULL, --ayuda
	'3', --orden
	NULL, --ci_predep
	NULL, --implicito
	NULL, --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	'V', --accion
	NULL, --accion_imphtml_debug
	'/mensajes/vinculos', --accion_vinculo_carpeta
	'3310', --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	'1', --accion_vinculo_popup
	'width: 400px, height: 500px, scrollbars: 1, resizable: 1', --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL  --accion_vinculo_celda
);
--- FIN Grupo de desarrollo 5

------------------------------------------------------------
-- apex_objeto_cuadro
------------------------------------------------------------
INSERT INTO apex_objeto_cuadro (objeto_cuadro_proyecto, objeto_cuadro, titulo, subtitulo, sql, columnas_clave, clave_dbr, archivos_callbacks, ancho, ordenar, paginar, tamano_pagina, tipo_paginado, eof_invisible, eof_customizado, exportar, exportar_rtf, pdf_propiedades, pdf_respetar_paginacion, asociacion_columnas, ev_seleccion, ev_eliminar, dao_nucleo_proyecto, dao_nucleo, dao_metodo, dao_parametros, desplegable, desplegable_activo, scroll, scroll_alto, cc_modo, cc_modo_anidado_colap, cc_modo_anidado_totcol, cc_modo_anidado_totcua) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'5000004', --objeto_cuadro
	NULL, --titulo
	NULL, --subtitulo
	NULL, --sql
	'id, id2', --columnas_clave
	'0', --clave_dbr
	NULL, --archivos_callbacks
	'100%', --ancho
	'0', --ordenar
	'0', --paginar
	NULL, --tamano_pagina
	NULL, --tipo_paginado
	'0', --eof_invisible
	NULL, --eof_customizado
	'0', --exportar
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
	'0', --scroll
	NULL, --scroll_alto
	't', --cc_modo
	'0', --cc_modo_anidado_colap
	NULL, --cc_modo_anidado_totcol
	NULL  --cc_modo_anidado_totcua
);

------------------------------------------------------------
-- apex_objeto_ei_cuadro_columna
------------------------------------------------------------

--- INICIO Grupo de desarrollo 5
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'5000004', --objeto_cuadro
	'5000001', --objeto_cuadro_col
	'id', --clave
	'1', --orden
	'id', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'0', --estilo
	NULL, --ancho
	NULL, --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	NULL, --total
	NULL  --total_cc
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'5000004', --objeto_cuadro
	'5000002', --objeto_cuadro_col
	'id2', --clave
	'2', --orden
	'id2', --titulo
	NULL, --estilo_titulo
	'0', --estilo
	NULL, --ancho
	NULL, --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	NULL, --total
	NULL  --total_cc
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc) VALUES (
	'toba_referencia', --objeto_cuadro_proyecto
	'5000004', --objeto_cuadro
	'5000003', --objeto_cuadro_col
	'descripcion', --clave
	'3', --orden
	'Descripcion', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	NULL, --formateo
	NULL, --vinculo_indice
	NULL, --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	NULL, --total
	NULL  --total_cc
);
--- FIN Grupo de desarrollo 5
