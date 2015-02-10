------------------------------------------------------------
--[30000180]--  Cliente REST - form_debug_rest 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_referencia', --proyecto
	'30000180', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ei_formulario', --clase
	'12000003', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Cliente REST - form_debug_rest', --nombre
	NULL, --titulo
	'0', --colapsable
	NULL, --descripcion
	'toba_referencia', --fuente_datos_proyecto
	'fuente_gis', --fuente_datos
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
	'2014-02-04 15:58:06', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_eventos
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES (
	'toba_referencia', --proyecto
	'30000178', --evento_id
	'30000180', --objeto
	'modificacion', --identificador
	'&Modificar', --etiqueta
	'1', --maneja_datos
	NULL, --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	'apex', --imagen_recurso_origen
	NULL, --imagen
	'0', --en_botonera
	NULL, --ayuda
	'1', --orden
	NULL, --ci_predep
	'1', --implicito
	'0', --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	NULL, --accion
	NULL, --accion_imphtml_debug
	NULL, --accion_vinculo_carpeta
	NULL, --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	NULL, --accion_vinculo_popup
	NULL, --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL, --accion_vinculo_celda
	NULL, --accion_vinculo_servicio
	'0', --es_seleccion_multiple
	'0'  --es_autovinculo
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_ut_formulario
------------------------------------------------------------
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, expandir_descripcion, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_agregar_abajo, filas_agregar_texto, filas_borrar_en_linea, filas_undo, filas_ordenar, filas_ordenar_en_linea, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios, no_imprimir_efs_sin_estado, resaltar_efs_con_estado, template, template_impresion) VALUES (
	'toba_referencia', --objeto_ut_formulario_proyecto
	'30000180', --objeto_ut_formulario
	NULL, --tabla
	NULL, --titulo
	NULL, --ev_agregar
	NULL, --ev_agregar_etiq
	NULL, --ev_mod_modificar
	NULL, --ev_mod_modificar_etiq
	NULL, --ev_mod_eliminar
	NULL, --ev_mod_eliminar_etiq
	NULL, --ev_mod_limpiar
	NULL, --ev_mod_limpiar_etiq
	NULL, --ev_mod_clave
	NULL, --clase_proyecto
	NULL, --clase
	NULL, --auto_reset
	NULL, --ancho
	'150px', --ancho_etiqueta
	'0', --expandir_descripcion
	NULL, --campo_bl
	NULL, --scroll
	NULL, --filas
	NULL, --filas_agregar
	'1', --filas_agregar_online
	'0', --filas_agregar_abajo
	NULL, --filas_agregar_texto
	'0', --filas_borrar_en_linea
	NULL, --filas_undo
	NULL, --filas_ordenar
	'0', --filas_ordenar_en_linea
	NULL, --columna_orden
	NULL, --filas_numerar
	NULL, --ev_seleccion
	NULL, --alto
	NULL, --analisis_cambios
	'0', --no_imprimir_efs_sin_estado
	'0', --resaltar_efs_con_estado
	NULL, --template
	NULL  --template_impresion
);

------------------------------------------------------------
-- apex_objeto_ei_formulario_ef
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_fila, objeto_ei_formulario, objeto_ei_formulario_proyecto, identificador, elemento_formulario, columnas, obligatorio, oculto_relaja_obligatorio, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total, inicializacion, permitir_html, deshabilitar_rest_func, estado_defecto, solo_lectura, solo_lectura_modificacion, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_sql, carga_fuente, carga_lista, carga_col_clave, carga_col_desc, carga_maestros, carga_cascada_relaj, cascada_mantiene_estado, carga_permite_no_seteado, carga_no_seteado, carga_no_seteado_ocultar, edit_tamano, edit_maximo, edit_mascara, edit_unidad, edit_rango, edit_filas, edit_columnas, edit_wrap, edit_resaltar, edit_ajustable, edit_confirmar_clave, edit_expreg, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include, popup_puede_borrar_estado, fieldset_fin, check_valor_si, check_valor_no, check_desc_si, check_desc_no, check_ml_toggle, fijo_sin_estado, editor_ancho, editor_alto, editor_botonera, selec_cant_minima, selec_cant_maxima, selec_utilidades, selec_tamano, selec_ancho, selec_serializar, selec_cant_columnas, upload_extensiones, punto_montaje, placeholder) VALUES (
	'30000249', --objeto_ei_formulario_fila
	'30000180', --objeto_ei_formulario
	'toba_referencia', --objeto_ei_formulario_proyecto
	'url', --identificador
	'ef_fijo', --elemento_formulario
	'url', --columnas
	'0', --obligatorio
	'0', --oculto_relaja_obligatorio
	'1', --orden
	'Recurso', --etiqueta
	NULL, --etiqueta_estilo
	NULL, --descripcion
	'0', --colapsado
	'0', --desactivado
	NULL, --estilo
	NULL, --total
	NULL, --inicializacion
	'1', --permitir_html
	'0', --deshabilitar_rest_func
	NULL, --estado_defecto
	'1', --solo_lectura
	'0', --solo_lectura_modificacion
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_sql
	NULL, --carga_fuente
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_maestros
	NULL, --carga_cascada_relaj
	'0', --cascada_mantiene_estado
	'0', --carga_permite_no_seteado
	NULL, --carga_no_seteado
	NULL, --carga_no_seteado_ocultar
	NULL, --edit_tamano
	NULL, --edit_maximo
	NULL, --edit_mascara
	NULL, --edit_unidad
	NULL, --edit_rango
	NULL, --edit_filas
	NULL, --edit_columnas
	NULL, --edit_wrap
	NULL, --edit_resaltar
	NULL, --edit_ajustable
	NULL, --edit_confirmar_clave
	NULL, --edit_expreg
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL, --popup_carga_desc_include
	NULL, --popup_puede_borrar_estado
	NULL, --fieldset_fin
	NULL, --check_valor_si
	NULL, --check_valor_no
	NULL, --check_desc_si
	NULL, --check_desc_no
	NULL, --check_ml_toggle
	'0', --fijo_sin_estado
	NULL, --editor_ancho
	NULL, --editor_alto
	NULL, --editor_botonera
	NULL, --selec_cant_minima
	NULL, --selec_cant_maxima
	NULL, --selec_utilidades
	NULL, --selec_tamano
	NULL, --selec_ancho
	NULL, --selec_serializar
	NULL, --selec_cant_columnas
	NULL, --upload_extensiones
	NULL, --punto_montaje
	NULL  --placeholder
);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_fila, objeto_ei_formulario, objeto_ei_formulario_proyecto, identificador, elemento_formulario, columnas, obligatorio, oculto_relaja_obligatorio, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total, inicializacion, permitir_html, deshabilitar_rest_func, estado_defecto, solo_lectura, solo_lectura_modificacion, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_sql, carga_fuente, carga_lista, carga_col_clave, carga_col_desc, carga_maestros, carga_cascada_relaj, cascada_mantiene_estado, carga_permite_no_seteado, carga_no_seteado, carga_no_seteado_ocultar, edit_tamano, edit_maximo, edit_mascara, edit_unidad, edit_rango, edit_filas, edit_columnas, edit_wrap, edit_resaltar, edit_ajustable, edit_confirmar_clave, edit_expreg, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include, popup_puede_borrar_estado, fieldset_fin, check_valor_si, check_valor_no, check_desc_si, check_desc_no, check_ml_toggle, fijo_sin_estado, editor_ancho, editor_alto, editor_botonera, selec_cant_minima, selec_cant_maxima, selec_utilidades, selec_tamano, selec_ancho, selec_serializar, selec_cant_columnas, upload_extensiones, punto_montaje, placeholder) VALUES (
	'30000251', --objeto_ei_formulario_fila
	'30000180', --objeto_ei_formulario
	'toba_referencia', --objeto_ei_formulario_proyecto
	'respuesta', --identificador
	'ef_fijo', --elemento_formulario
	'respuesta', --columnas
	'0', --obligatorio
	'0', --oculto_relaja_obligatorio
	'2', --orden
	'Respuesta', --etiqueta
	NULL, --etiqueta_estilo
	NULL, --descripcion
	'0', --colapsado
	'0', --desactivado
	NULL, --estilo
	NULL, --total
	NULL, --inicializacion
	'1', --permitir_html
	'0', --deshabilitar_rest_func
	NULL, --estado_defecto
	'0', --solo_lectura
	'0', --solo_lectura_modificacion
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_sql
	NULL, --carga_fuente
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_maestros
	NULL, --carga_cascada_relaj
	'0', --cascada_mantiene_estado
	'0', --carga_permite_no_seteado
	NULL, --carga_no_seteado
	NULL, --carga_no_seteado_ocultar
	NULL, --edit_tamano
	NULL, --edit_maximo
	NULL, --edit_mascara
	NULL, --edit_unidad
	NULL, --edit_rango
	NULL, --edit_filas
	NULL, --edit_columnas
	NULL, --edit_wrap
	NULL, --edit_resaltar
	NULL, --edit_ajustable
	NULL, --edit_confirmar_clave
	NULL, --edit_expreg
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL, --popup_carga_desc_include
	NULL, --popup_puede_borrar_estado
	NULL, --fieldset_fin
	NULL, --check_valor_si
	NULL, --check_valor_no
	NULL, --check_desc_si
	NULL, --check_desc_no
	NULL, --check_ml_toggle
	'0', --fijo_sin_estado
	NULL, --editor_ancho
	NULL, --editor_alto
	NULL, --editor_botonera
	NULL, --selec_cant_minima
	NULL, --selec_cant_maxima
	NULL, --selec_utilidades
	NULL, --selec_tamano
	NULL, --selec_ancho
	NULL, --selec_serializar
	NULL, --selec_cant_columnas
	NULL, --upload_extensiones
	NULL, --punto_montaje
	NULL  --placeholder
);
--- FIN Grupo de desarrollo 30
