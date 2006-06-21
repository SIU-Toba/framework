------------------------------------------------------------
--[1369]--  Catalogo Unificado 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '1369', NULL, NULL, 'toba', 'objeto_ei_filtro', 'filtro_catalogo_items', 'catalogos/filtro_catalogo_items.php', NULL, NULL, 'Catalogo Unificado', 'Opciones', '1', NULL, 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-07-19 09:39:59');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '53', '1369', 'filtrar', '&Filtrar', '1', '0', '', 'abm-input-eliminar', NULL, NULL, '1', '', '1', NULL, NULL, NULL, 'no_cargado,cargado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '54', '1369', 'cancelar', '&Cancelar', '0', '0', '', 'abm-input', NULL, NULL, '1', '', '2', NULL, NULL, NULL, 'cargado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('admin', '1369', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '100%', '85px', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1231', 'id', 'ef_editable', 'id', NULL, 'tamano_:_ 20_;_
maximo_:_ 255_;_
', '4', 'ID', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1232', 'inicial', 'ef_combo', 'inicial', NULL, 'dao_:_ carpetas_posibles_;_
clase_:_ ci_catalogo_items_;_
clave_:_ id_;_
valor_:_ nombre_;_
', '1', 'Carpeta', NULL, NULL, NULL, '1', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1233', 'menu', 'ef_combo', 'menu', NULL, 'lista_:_ SI,NO_;_
no_seteado_:_ --Todos--_;_
', '3', 'En menú', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1234', 'nombre', 'ef_editable', 'nombre', NULL, 'tamano_:_ 30_;_
maximo_:_ 255_;_
', '2', 'Nombre', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1000009', 'con_objeto', 'ef_checkbox', 'con_objeto', NULL, 'valor_:_ 1_;_
valor_no_seteado_:_ 0_;_
estado_:_ 0_;_
', '5', 'Cont. Obj.', NULL, 'Filtra la lista de items que contiene (en forma recursiva) el objeto especificado.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1000010', 'objeto_clase', 'ef_combo', 'objeto_clase', NULL, 'dao_:_ get_lista_clases_toba_;_
clase_:_ dao_editores_;_
include_:_ db/dao_editores.php_;_
clave_:_ clase_;_
valor_:_ descripcion_;_
no_seteado_:_ --- Seleccione ---_;_
', '6', 'Tipo de Objeto', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1000011', 'objeto', 'ef_combo', 'objeto', NULL, 'dao_:_ get_lista_objetos_toba_;_
clase_:_ dao_editores_;_
include_:_ db/dao_editores.php_;_
clave_:_ id_;_
valor_:_ descripcion_;_
dependencias_:_ objeto_clase_;_
', '7', 'Objeto', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1369', '1000023', 'tipo_solicitud', 'ef_combo', 'tipo_solicitud', NULL, 'no_seteado_:_ --- Todos ---_;_
sql_:_ SELECT solicitud_tipo, descripcion_corta 
FROM apex_solicitud_tipo 
WHERE solicitud_tipo <> \'fantasma\'
ORDER BY 1_;_
', '8', 'Tipo de Solicitud', NULL, 'Tipo de Solicitud', NULL, NULL, NULL, '0');
