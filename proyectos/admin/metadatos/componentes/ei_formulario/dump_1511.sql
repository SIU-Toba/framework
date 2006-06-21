------------------------------------------------------------
--[1511]--  OBJETO - DBR - Prop. basicas 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '1511', NULL, NULL, 'toba', 'objeto_ei_formulario', 'eiform_ap', 'objetos_toba/db_tablas/eiform_ap.php', NULL, NULL, 'OBJETO - DBR - Prop. basicas', 'Administrador de Persistencia PREDETERMINADO', NULL, NULL, 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-07-26 23:56:28');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '88', '1511', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('admin', '1511', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1511', '1258', 'ap', 'ef_combo', 'ap', NULL, 'sql_:_ SELECT ap, descripcion FROM apex_admin_persistencia
WHERE categoria = \'R\'_;_
', '2', 'AP por defecto', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1511', '1259', 'ap_archivo', 'ef_popup', 'ap_archivo', NULL, 'tamano_:_ 60_;_
maximo_:_ 80_;_
item_destino_:_ /admin/objetos_toba/selector_archivo_;_
ventana_:_ width: 400,height: 400,scroll: yes_;_
editable_:_ 1_;_
', '4', 'AP - Archivo', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1511', '1260', 'ap_clase', 'ef_editable', 'ap_clase', NULL, 'tamano_:_ 40_;_
maximo_:_ 80_;_
', '3', 'AP - Clase', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1511', '1442', 'clave', 'ef_editable', 'clave', NULL, 'tamano_:_ 40_;_
maximo_:_ 60_;_
', '1', 'Clave', NULL, 'Componentes asociativos de la clave del elemento.', NULL, '1', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1511', '4413', 'debug', 'ef_checkbox', 'debug', NULL, 'valor_:_ 1_;_
valor_no_seteado_:_ 0_;_
estado_:_ 0_;_
', '5', 'Modo Debug', NULL, 'En el modo debug el objeto muestra un esquema de las tablas al inicio del pedido de página y otro al final. En caso de querer hacer un dump puntual usar el método [api:Objetos/Persistencia/objeto_datos_relacion dump_esquema].<br><br>
La salida es en formato SVG, ver requisitos del [wiki:Referencia/Objetos/ei_esquema objeto_ei_esquema] encargado de construirlo.', NULL, NULL, NULL, '0');
