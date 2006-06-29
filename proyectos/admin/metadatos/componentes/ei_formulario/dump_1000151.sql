------------------------------------------------------------
--[1000151]--  Editor Parámetros Efs: popup 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '1000151', NULL, NULL, 'toba', 'objeto_ei_formulario', NULL, NULL, NULL, NULL, 'Editor Parámetros Efs: popup', NULL, '0', NULL, 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006-06-28 17:58:07');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '1000159', '1000151', 'modificacion', '&Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('admin', '1000151', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1000151', '1000174', 'item_proyecto', 'ef_combo', 'item_proyecto', '1', 'dao_:_ get_proyectos_accesibles_;_
clase_:_ dao_editores_;_
include_:_ modelo/consultas/dao_editores.php_;_
clave_:_ proyecto_;_
valor_:_ descripcion_corta_;_
no_seteado_:_ --- Seleccione ---_;_', '1', 'Proyecto', NULL, 'Proyecto al que pertenece el ítem destino.', '0', '0', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1000151', '1000175', 'item_carpeta', 'ef_combo', 'item_carpeta', '0', 'dao_:_ get_carpetas_posibles_;_
clase_:_ dao_editores_;_
include_:_ modelo/consultas/dao_editores.php_;_
clave_:_ id_;_
valor_:_ nombre_;_
dependencias_:_ item_proyecto_;_
no_seteado_:_ --- Seleccione ---_;_', '2', 'Carpeta', NULL, 'Carpeta a la que pertenece el ítem destino (sólo sirve para encontrar el ítem durante la edición).', '0', '0', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1000151', '1000176', 'item_id', 'ef_combo', 'item_id', '1', 'dao_:_ get_items_carpeta_;_
clase_:_ dao_editores_;_
include_:_ modelo/consultas/dao_editores.php_;_
clave_:_ id_;_
valor_:_ descripcion_;_
dependencias_:_ item_carpeta_;_', '3', 'Item', NULL, 'Ítem que se invoca en el popup.', '0', '0', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1000151', '1000177', 'ventana', 'ef_editable', 'ventana', '0', 'tamano_:_ 25_;_
maximo_:_ 255_;_', '4', 'Parámetros Visuales', NULL, 'Parámetros que se utilizan en la apertura del popup. Por ejemplo:
<pre>width: 400,height: 400,scroll: yes</pre>', '0', '0', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1000151', '1000178', 'editable', 'ef_checkbox', 'editable', '0', '', '5', 'Permitir edición', NULL, 'El valor es editable libremente por parte del usuario. Requiere que la clave sea igual al valor. La ventana de popup funciona sólo como una forma rápida de carga.', '0', '0', NULL, '0');
