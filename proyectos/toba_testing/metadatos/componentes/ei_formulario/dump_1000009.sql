------------------------------------------------------------
--[1000009]--  Cascadas - form - form 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba_testing', '1000009', NULL, NULL, 'toba', 'objeto_ei_formulario', 'form_simple', 'p_acciones/efs/form_simple.php', NULL, NULL, 'Cascadas - form - form', NULL, NULL, NULL, 'toba_testing', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006-05-15 13:58:38');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('toba_testing', '1000016', '1000009', 'modificacion', '&Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('toba_testing', '1000009', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000030', 'combo_dao1', 'ef_combo', 'combo_dao1', '1', 'dao_:_ get_combo_dao1_;_
clase_:_ dao_estatico_;_
include_:_ p_acciones/efs/dao_estatico.php_;_
clave_:_ clave_;_
valor_:_ desc_;_
dependencias_:_ popup,combo_lista,combo_lista_c_;_
cascada_relajada_:_ 1_;_
no_seteado_:_ --- Seleccione ---_;_', '5', 'Combo Dao1 (estat)', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000031', 'combo_dao2', 'ef_combo', 'combo_dao2', '1', 'dao_:_ get_combo_dao2_;_
clave_:_ clave_;_
valor_:_ valor_;_
dependencias_:_ combo_lista,combo_lista_c,radio_esclavo_;_
cascada_relajada_:_ 1_;_
no_seteado_:_ --- Seleccione ---_;_', '6', 'Combo Dao2 (din)', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000032', 'combo_sql', 'ef_combo', 'combo_sql', '1', 'sql_:_ SELECT \'clave_dinamica\' as clave, \'%combo_dao1% - %combo_dao2%\' as valor
UNION
SELECT \'clave_fija\' as clave, \'valor_fijo\' as valor_;_
dependencias_:_ combo_dao1, combo_dao2_;_
cascada_relajada_:_ 1_;_', '7', 'Combo Sql', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000033', 'combo_lista', 'ef_combo', 'combo_lista', NULL, 'lista_:_ A,B,C_;_
no_seteado_:_ --- Seleccione ---_;_', '1', 'Combo Lista', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000034', 'combo_lista_c', 'ef_combo', 'combo_lista_c', NULL, 'lista_:_ a/Una A,b/Una B,c/Una C_;_
no_seteado_:_ --- Palabra ---_;_', '4', 'Combo Lista Clave', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000043', 'editable_dao', 'ef_editable', 'editable_dao', '1', 'dao_:_ get_editable_dao_;_
dependencias_:_ combo_lista, combo_lista_c_;_
cascada_relajada_:_ 1_;_', '8', 'Editable DAO', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000044', 'editable_sql', 'ef_editable', 'editable_sql', '1', 'sql_:_ select \'%combo_lista%\'_;_
dependencias_:_ combo_lista_;_
cascada_relajada_:_ 1_;_', '9', 'Editable SQL', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000047', 'multi_lista', 'ef_multi_seleccion_lista', 'multi_lista', '1', 'dao_:_ get_datos_multi_;_
dependencias_:_ combo_lista_;_
cascada_relajada_:_ 1_;_
mostrar_utilidades_:_ 1_;_', '10', 'Multi Lista', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000048', 'multi_check', 'ef_multi_seleccion_check', 'multi_check', '1', 'dao_:_ get_datos_multi_;_
dependencias_:_ combo_lista_;_
cascada_relajada_:_ 1_;_
mostrar_utilidades_:_ 1_;_', '11', 'Multi Check', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000070', 'popup', 'ef_popup', 'popup', '1', 'dao_:_ get_popup_;_
dependencias_:_ combo_lista_;_
cascada_relajada_:_ 1_;_
item_destino_:_ 1000017_;_
ventana_:_ width: width:300,height: height:200_;_
editable_:_ 1_;_', '2', 'Popup', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000009', '1000073', 'radio_esclavo', 'ef_radio', 'radio_esclavo', '1', 'dao_:_ get_radio_esclavo_;_
dependencias_:_ combo_lista_;_
cascada_relajada_:_ 1_;_
no_seteado_:_ NINGUNO_;_', '3', 'Radio', NULL, NULL, NULL, NULL, NULL, NULL);
