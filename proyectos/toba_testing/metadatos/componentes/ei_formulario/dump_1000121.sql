------------------------------------------------------------
--[1000121]--  Cascadas - form clave_comp 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba_testing', '1000121', NULL, NULL, 'toba', 'objeto_ei_formulario', NULL, NULL, NULL, NULL, 'Cascadas - form clave_comp', NULL, NULL, NULL, 'toba_testing', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2006-05-26 11:44:01');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('toba_testing', '1000123', '1000121', 'modificacion', '&Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('toba_testing', '1000121', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000057', 'combo_dao1', 'ef_combo', 'combo_dao1_c1,combo_dao1_c2', NULL, 'dao_:_ get_combo_dao_comp1_;_
clase_:_ dao_estatico_;_
include_:_ p_acciones/efs/dao_estatico.php_;_
clave_:_ clave1,clave2_;_
valor_:_ desc_;_
no_seteado_:_ --- Seleccione ---_;_
dependencias_:_ combo_lista,combo_lista_c_;_
', '3', 'Combo Dao1 (estat) 2 Claves', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000058', 'combo_dao2', 'ef_combo', 'combo_dao2_c1, combo_dao2_c2, combo_dao2_c3', NULL, 'dao_:_ get_combo_dao_comp2_;_
clave_:_ clave1,clave2, clave3_;_
valor_:_ valor_;_
no_seteado_:_ --- Seleccione ---_;_
dependencias_:_ combo_lista,combo_lista_c_;_
', '4', 'Combo Dao2 (din) 3 Claves', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000059', 'combo_sql', 'ef_combo', 'combo_sql', NULL, 'sql_:_ SELECT \'clave_dinamica\' as clave, \'%combo_dao1% - %combo_dao2%\' as valor
UNION
SELECT \'clave_fija\' as clave, \'valor_fijo\' as valor_;_
dependencias_:_ combo_dao1, combo_dao2_;_
', '5', 'Combo Sql', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000060', 'combo_lista', 'ef_combo', 'combo_lista', NULL, 'lista_:_ A,B,C_;_
no_seteado_:_ --- Palabra ---_;_
', '1', 'Combo Lista', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000061', 'combo_lista_c', 'ef_combo', 'combo_lista_c', NULL, 'no_seteado_:_ --- Palabra ---_;_
lista_:_ a/Una A,b/Una B,c/Una C_;_
', '2', 'Combo Lista Clave', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000062', 'editable_dao', 'ef_editable', 'editable_dao', NULL, 'dao_:_ get_editable_dao_;_
dependencias_:_ combo_lista, combo_lista_c_;_
', '6', 'Editable DAO', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000063', 'editable_sql', 'ef_editable', 'editable_sql', NULL, 'sql_:_ select \'%combo_lista%\'_;_
dependencias_:_ combo_lista_;_
', '7', 'Editable SQL', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000064', 'multi_lista', 'ef_multi_seleccion_lista', 'multi_lista1,multi_lista2', NULL, 'dao_:_ get_datos_multi_claves_;_
clave_:_ clave1,clave2_;_
valor_:_ valor_;_
dependencias_:_ combo_lista_;_
mostrar_utilidades_:_ 1_;_
', '8', 'Multi Lista', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000065', 'multi_check', 'ef_multi_seleccion_check', 'multi_check1,multi_check2', NULL, 'dao_:_ get_datos_multi_claves_;_
clave_:_ clave1,clave2_;_
valor_:_ valor_;_
dependencias_:_ combo_lista_;_
mostrar_utilidades_:_ 1_;_
', '9', 'Multi Check', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba_testing', '1000121', '1000066', 'multi_doble', 'ef_multi_seleccion_doble', 'multi_doble1,multi_doble2', NULL, 'dao_:_ get_datos_multi_claves_;_
clave_:_ clave1,clave2_;_
valor_:_ valor_;_
dependencias_:_ combo_lista_;_
', '10', 'Multi Doble', NULL, NULL, NULL, NULL, NULL, '0');
