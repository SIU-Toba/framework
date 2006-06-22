------------------------------------------------------------
--[1000020]--  Comportamientos JS - 1 - form 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba_testing', '1000020', NULL, NULL, 'toba', 'objeto_ei_formulario', NULL, NULL, NULL, NULL, 'Comportamientos JS - 1 - form', NULL, NULL, NULL, 'toba_testing', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006-05-24 15:27:58');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('toba_testing', '1000122', '1000020', 'modificar', 'Modificar', '1', NULL, NULL, NULL, NULL, NULL, '1', NULL, '1', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('toba_testing', '1000020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '200px', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000020', '1000045', 'multi_lista', 'ef_multi_seleccion_lista', 'multi_lista', '1', 'lista_:_ a/A,b/B,c/C,d/D,e/E_;_
predeterminado_:_ c,d_;_
cant_minima_:_ 3_;_
cant_maxima_:_ 4_;_
mostrar_utilidades_:_ 1_;_', '2', 'Multi selección Lista', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000020', '1000046', 'multi_check', 'ef_multi_seleccion_check', 'multi_check', '1', 'lista_:_ a/A,b/B,c/C,d/D,e/E_;_
predeterminado_:_ c,d_;_
cant_minima_:_ 3_;_
cant_maxima_:_ 4_;_
mostrar_utilidades_:_ 1_;_', '3', 'Multi selección Check', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000020', '1000057', 'multi_doble', 'ef_multi_seleccion_doble', 'multi_doble', '1', 'lista_:_ a/Letra A,b/Letra B,c/Letra C,d/Letra D,e/Letra E_;_
predeterminado_:_ b,c_;_
cant_minima_:_ 3_;_
cant_maxima_:_ 4_;_', '4', 'Multi selección Doble', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000020', '1000071', 'radio', 'ef_radio', 'radio', '1', 'lista_:_ a/Valor A, b/Valor B, c/Valor C, d/Valor D_;_
no_seteado_:_ NINGUNO_;_', '1', 'Radio', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000020', '1000078', 'multilinea_maximo', 'ef_editable_multilinea', 'multilinea_maximo', '1', 'filas_:_ 5_;_
columnas_:_ 30_;_
maximo_:_ 20_;_
ajustable_:_ 1_;_', '5', 'Multilinea Maximo', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000020', '1000090', 'upload', 'ef_upload', 'upload', '1', '', '6', 'Upload', NULL, NULL, '0', '0', NULL, '0');
