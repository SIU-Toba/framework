------------------------------------------------------------
--[1366]--  OBJETO - General - Eventos 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '1366', NULL, NULL, 'toba', 'objeto_ei_formulario', 'eiform_eventos', 'objetos_toba/eiform_eventos.php', NULL, NULL, 'OBJETO - General - Eventos', NULL, NULL, NULL, 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '52', '1366', 'aceptar', '&Aceptar', '1', '0', NULL, NULL, NULL, NULL, '1', NULL, '1', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '51', '1366', 'cancelar', '&Cancelar', '0', '0', NULL, 'abm-input', NULL, NULL, '1', NULL, '2', NULL, '0', NULL, 'cargado', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '163', '1366', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, '3', NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('admin', '1366', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '500', '150px', NULL, NULL, NULL, '1', '1', NULL, '1', NULL, '1', '1', NULL, 'NO');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '1228', 'ayuda', 'ef_editable_textarea', 'ayuda', NULL, 'filas_:_ 4_;_
columnas_:_ 50_;_
', '3', 'Ayuda', NULL, 'Texto orientativo a mostrar cuando se posiciona el mouse sobre el elemento grafico que dispara el evento.', NULL, NULL, '4', '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '1229', 'confirmacion', 'ef_editable_textarea', 'confirmacion', NULL, 'filas_:_ 4_;_
columnas_:_ 50_;_
', '2', 'Confirmacion', NULL, 'Texto de confirmacion a mostrar antes de disparar el evento.', NULL, NULL, '4', '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '1230', 'estilo', 'ef_editable', 'estilo', NULL, 'tamano_:_ 40_;_
', '1', 'Estilo', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '1532', 'sobre_fila', 'ef_checkbox', 'sobre_fila', NULL, 'valor_:_ 1_;_
valor_no_seteado_:_ 0_;_
', '4', 'A nivel de fila', NULL, 'Para aquellos objetos que manejan filas, el evento se incluye en cada una de estas.', NULL, '1', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '1539', 'maneja_datos', 'ef_checkbox', 'maneja_datos', NULL, 'valor_:_ 1_;_
valor_no_seteado_:_ 0_;_
estado_:_ 1_;_
', '5', 'Maneja datos', NULL, 'Si un evento maneja datos realiza validaciones de lo editado y generalmente acarrea estos datos como parametros del evento. En el caso de un CI, implica que no se va a procesar ningun EI que esta dentro del mismo.', NULL, '1', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '4366', 'grupo', 'ef_editable', 'grupo', NULL, 'tamano_:_ 40_;_
maximo_:_ 80_;_
', '6', 'Grupos', NULL, 'Este identificador permite catalogar los eventos en grupos. Hay que ingresar la lista de grupos a los que el evento pertenece seperados por comas. Existen primitivas en los EI que permiten definir que grupo mostrar.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '4578', 'accion', 'ef_combo', 'accion', NULL, 'no_seteado_:_ Ninguna_;_
lista_:_ V/Vinculo_;_
', '7', 'Accion predefinida', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '4580', 'accion_vin_target', 'ef_editable', 'accion_vinculo_target', NULL, 'tamano_:_ 40_;_
maximo_:_ 40_;_
', '11', 'Vinculo - Target', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '4581', 'accion_vin_celda', 'ef_editable', 'accion_vinculo_celda', NULL, 'tamano_:_ 40_;_
maximo_:_ 40_;_
', '10', 'Vinculo - Celda mem.', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '5000001', 'accion_vin_carpeta', 'ef_combo', 'accion_vinculo_carpeta', NULL, 'dao_:_ get_carpetas_posibles_;_
clase_:_ dao_editores_;_
include_:_ modelo/consultas/dao_editores.php_;_
clave_:_ id_;_
valor_:_ nombre_;_
no_seteado_:_ -- SELECCIONAR --_;_
', '8', 'Vinculo - Carpeta', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '5000002', 'accion_vin_item', 'ef_combo', 'accion_vinculo_item', NULL, 'dao_:_ get_items_carpeta_;_
clase_:_ dao_editores_;_
include_:_ modelo/consultas/dao_editores.php_;_
clave_:_ id_;_
valor_:_ descripcion_;_
no_seteado_:_ -- SELECCIONAR --_;_
dependencias_:_ accion_vin_carpeta_;_
', '9', 'Vinculo - Item', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '5000003', 'accion_vin_popup', 'ef_checkbox', 'accion_vinculo_popup', NULL, 'valor_:_ 1_;_
valor_no_seteado_:_ 0_;_
', '12', 'Vinculo - Es popup', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1366', '5000004', 'accion_vin_popup_par', 'ef_editable', 'accion_vinculo_popup_param', NULL, 'tamano_:_ 60_;_
', '13', 'Vinculo - Popup param.', NULL, 'width: 400px, height: 500px, scrollbars: 1, resizable: 1', NULL, NULL, NULL, '0');
