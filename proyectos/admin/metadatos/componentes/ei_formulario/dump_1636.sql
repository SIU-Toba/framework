------------------------------------------------------------
--[1636]--  Tipo de destino 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '1636', NULL, NULL, 'toba', 'objeto_ei_formulario', 'form_opciones', 'objetos_toba/clonador/form_opciones.php', NULL, NULL, 'Tipo de destino', NULL, NULL, NULL, 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-10-21 16:53:55');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '116', '1636', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('admin', '1636', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4243', 'tipo', 'ef_combo', 'tipo', NULL, 'dao_:_ get_tipos_destino_;_
clase_:_ ci_clonador_objetos_;_
include_:_ objetos_toba/clonador/ci_clonador_objetos.php_;_
clave_:_ clase_;_
valor_:_ clase_;_
no_seteado_:_ --- Seleccione ---_;_
', '3', 'Destino - Tipo', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4244', 'con_destino', 'ef_checkbox', 'con_destino', NULL, 'valor_:_ 1_;_
valor_no_seteado_:_ 0_;_
estado_:_ 0_;_
', '2', 'Asignar a otro objeto/item', NULL, 'Una vez clonado el objeto, es posible asignarlo a otro objeto o item existente.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4245', 'objeto_id', 'ef_combo', 'objeto', NULL, 'dao_:_ get_objetos_destino_;_
clase_:_ ci_clonador_objetos_;_
include_:_ objetos_toba/clonador/ci_clonador_objetos.php_;_
clave_:_ id_;_
valor_:_ descripcion_;_
no_seteado_:_ --- Seleccione ---_;_
dependencias_:_ tipo_;_
', '4', 'Destino', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4246', 'nuevo_nombre', 'ef_editable', 'nuevo_nombre', '1', 'tamano_:_ 50_;_
maximo_:_ 80_;_
', '1', 'Nuevo nombre', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4247', 'ci_pantalla', 'ef_combo', 'pantalla', NULL, 'dao_:_ get_pantallas_de_ci_;_
clase_:_ dao_editores_;_
include_:_ modelo/consultas/dao_editores.php_;_
clave_:_ pantalla_;_
valor_:_ descripcion_;_
no_seteado_:_ Ninguna_;_
dependencias_:_ objeto_id_;_
', '5', 'Pantalla', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4343', 'identificador', 'ef_editable', 'id_dependencia', NULL, 'tamano_:_ 20_;_
maximo_:_ 20_;_
', '6', 'Identificador', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4344', 'min_filas', 'ef_editable_numero', 'min_filas', NULL, '_;_
', '7', 'Min. Filas', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1636', '4345', 'max_filas', 'ef_editable_numero', 'max_filas', NULL, '_;_
', '8', 'Max. Filas', NULL, NULL, NULL, NULL, NULL, NULL);
