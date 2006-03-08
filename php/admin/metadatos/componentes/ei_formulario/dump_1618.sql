------------------------------------------------------------
--[1618]--  Selección Archivos CSS 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '1618', NULL, NULL, 'toba', 'objeto_ei_formulario', NULL, NULL, NULL, NULL, 'Selección Archivos CSS', NULL, NULL, NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-09-22 11:12:49');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug) VALUES ('toba', '109', '1618', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('toba', '1618', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1618', '1553', 'origen', 'ef_combo_db', 'origen', '1', 'no_seteado: --- Seleccione ---;
sql: SELECT estilo, descripcion 
FROM apex_estilo 
WHERE descripcion <> \'no\'
ORDER BY descripcion;', '1', 'Origen', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1618', '1554', 'destino', 'ef_combo_db', 'destino', '1', 'no_seteado: --- Seleccione ---;
sql: SELECT estilo, descripcion 
FROM apex_estilo 
WHERE descripcion <> \'no\'
ORDER BY descripcion;', '2', 'Destino', NULL, NULL, NULL, NULL, NULL, NULL);
