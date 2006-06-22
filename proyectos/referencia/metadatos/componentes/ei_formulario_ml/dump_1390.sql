------------------------------------------------------------
--[1390]--  Dependencias en ML 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('referencia', '1390', NULL, NULL, 'toba', 'objeto_ei_formulario_ml', NULL, NULL, NULL, NULL, 'Dependencias en ML', NULL, NULL, NULL, 'referencia', 'referencia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-07-25 10:55:35');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('referencia', '20', '1390', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('referencia', '1390', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, 'NO');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1390', '1342', 'localidad', 'ef_combo', 'localidad', NULL, 'no_seteado_:_ -- Seleccione --_;_
sql_:_ SELECT \'bb\', \'Bahía Blanca\' WHERE \'ar\' = \'%pais%\' AND \'ba\' = \'%provincia%\'
UNION
SELECT \'sj\', \'San Juan\' WHERE \'ar\' = \'%pais%\' AND \'sj\' = \'%provincia%\'_;_
dependencias_:_ pais, provincia_;_
', '2', NULL, NULL, NULL, NULL, NULL, '4', NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1390', '1343', 'pais', 'ef_combo', 'pais', NULL, 'no_seteado_:_ -- Seleccione --_;_
sql_:_ SELECT \'ar\', \'Argentina\'
UNION
SELECT \'br\', \'Brasil\'_;_
', '0', 'País', NULL, NULL, NULL, NULL, '4', NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1390', '1344', 'provincia', 'ef_combo', 'provincia', NULL, 'no_seteado_:_ -- Seleccione --_;_
sql_:_ SELECT \'ba\', \'Buenos Aires\' WHERE \'ar\' = \'%pais%\'
UNION
SELECT \'sj\', \'San Juan\' WHERE \'ar\' = \'%pais%\'_;_
dependencias_:_ pais_;_
', '1', 'Provincia', NULL, NULL, NULL, NULL, '4', NULL);
