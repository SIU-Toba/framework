------------------------------------------------------------
--[1355]--  OBJETO - General - Propiedades BASE 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('admin', '1355', NULL, '1', 'toba', 'objeto_ei_formulario', 'eiform_prop_base', 'objetos_toba/eiform_prop_base.php', 'toba', NULL, 'OBJETO - General - Propiedades BASE', NULL, NULL, 'En esta interface se definen propiedades basicas de un objeto STANDART', 'admin', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('admin', '33', '1355', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('admin', '1355', 'apex_objeto', 'Características principales', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100%', '150px', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1170', 'colapsable', 'ef_checkbox', 'colapsable', NULL, 'valor_:_ 1_;_
valor_info_:_ SI_;_
', '8', 'Colapsable', NULL, 'Indica si el objeto tiene capacidad de plegarse y desplegarse a pedido del usuario. Requiere que el objeto tenga definido título. En ejecución usar el método colapsar() para cambiar el estado inicial de este objeto.', '1', NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1171', 'descripcion', 'ef_editable_multilinea', 'descripcion', NULL, 'filas_:_ 7_;_
columnas_:_ 50_;_
', '9', 'Descripcion', NULL, 'Descripcion del objeto.', '1', NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1172', 'fuente_datos', 'ef_combo', 'fuente_datos_proyecto, fuente_datos', '1', 'dao_:_ get_fuentes_datos_;_
clase_:_ dao_editores_;_
include_:_ db/dao_editores.php_;_
clave_:_ proyecto, fuente_datos_;_
valor_:_ descripcion_corta_;_
', '3', 'Fuente de Datos', NULL, 'Fuente de datos a la que se conecta el objeto.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1173', 'nombre', 'ef_editable', 'nombre', '1', 'tamano_:_ 50_;_
maximo_:_ 255_;_
', '2', 'Nombre', NULL, 'Nombre del objeto, no tiene injerencia en el funcionamiento del objeto, solo sirve para su ubicación y referencia posterior en este administrador.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1174', 'subclase', 'ef_editable', 'subclase', NULL, 'tamano_:_ 40_;_
maximo_:_ 100_;_
', '5', 'Subclase', NULL, 'Nombre de la clase. (La clase tiene que heredar el elemento de la infraestructura seleccionado y utilizar las ventanas permitidas)', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1175', 'subclase_archivo', 'ef_popup', 'subclase_archivo', NULL, 'tamano_:_ 60_;_
maximo_:_ 80_;_
item_destino_:_ /admin/objetos_toba/selector_archivo_;_
ventana_:_ width: 400,height: 400,scroll: yes_;_
editable_:_ 1_;_
', '6', 'Subclase - Archivo', NULL, 'Archivo PHP donde reside la subclase.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1176', 'tipo_clase', 'ef_combo', 'tipo_clase', NULL, 'no_seteado_:_ --- FILTRAR ---_;_
sql_:_ SELECT clase_tipo, descripcion_corta FROM apex_clase_tipo_;_
', '4', 'Tipo Clase', NULL, 'Esto es (por ahora) para testear cascadas', NULL, '1', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '1177', 'titulo', 'ef_editable', 'titulo', NULL, 'tamano_:_ 60_;_
maximo_:_ 80_;_
', '7', 'Titulo interface', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '4346', 'id', 'ef_editable', 'objeto', NULL, 'tamano_:_ 10_;_
solo_lectura_:_ 1_;_
', '1', 'ID', NULL, NULL, NULL, '1', NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '4434', 'parametro_a', 'ef_editable', 'parametro_a', NULL, 'tamano_:_ 60_;_
maximo_:_ 100_;_
', '10', 'Parametro A', NULL, NULL, '1', NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '4435', 'parametro_b', 'ef_editable', 'parametro_b', NULL, 'tamano_:_ 60_;_
maximo_:_ 100_;_
', '11', 'Parametro B', NULL, NULL, '1', NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '4436', 'parametro_c', 'ef_editable', 'parametro_c', NULL, 'tamano_:_ 60_;_
maximo_:_ 100_;_
', '12', 'Parametro C', NULL, NULL, '1', NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '4437', 'parametro_d', 'ef_editable', 'parametro_d', NULL, 'tamano_:_ 60_;_
maximo_:_ 100_;_
', '13', 'Parametro D', NULL, NULL, '1', NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('admin', '1355', '4438', 'parametro_e', 'ef_editable', 'parametro_e', NULL, 'tamano_:_ 60_;_
maximo_:_ 100_;_
', '14', 'Parametro E', NULL, NULL, '1', NULL, NULL, '0');
