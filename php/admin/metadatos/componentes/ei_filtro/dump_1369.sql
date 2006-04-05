------------------------------------------------------------
--[1369]--  Catalogo Unificado 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('toba', '1369', NULL, NULL, 'toba', 'objeto_ei_filtro', 'filtro_catalogo_items', 'admin/catalogos/filtro_catalogo_items.php', NULL, NULL, 'Catalogo Unificado', 'Opciones', '1', NULL, 'toba', 'instancia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-07-19 09:39:59');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug) VALUES ('toba', '53', '1369', 'filtrar', '&Filtrar', '1', '0', '', 'abm-input-eliminar', NULL, NULL, '1', '', '1', NULL, NULL, NULL, 'no_cargado,cargado', NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug) VALUES ('toba', '54', '1369', 'cancelar', '&Cancelar', '0', '0', '', 'abm-input', NULL, NULL, '1', '', '2', NULL, NULL, NULL, 'cargado', NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('toba', '1369', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '100%', '85px', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1369', '1231', 'id', 'ef_editable', 'id', NULL, 'tamano: 20;
maximo: 255;', '4', 'ID', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1369', '1232', 'inicial', 'ef_combo_dao', 'inicial', NULL, 'dao: carpetas_posibles;
clase: ci_catalogo_items;
clave: id;
valor: nombre;', '1', 'Carpeta', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1369', '1233', 'menu', 'ef_combo_lista', 'menu', NULL, 'lista: SI,NO;
no_seteado: --Todos--;', '3', 'En menú', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1369', '1234', 'nombre', 'ef_editable', 'nombre', NULL, 'tamano: 30;
maximo: 255;', '2', 'Nombre', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1369', '1000009', 'con_objeto', 'ef_checkbox', 'con_objeto', NULL, 'valor: 1;
valor_no_seteado: 0;
estado: 0;', '5', 'Cont. Obj.', NULL, 'Filtra la lista de items que contiene (en forma recursiva) el objeto especificado.', NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1369', '1000010', 'objeto_clase', 'ef_combo_dao', 'objeto_clase', NULL, 'dao: get_lista_clases_toba;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: clase;
valor: descripcion;
no_seteado: --- Seleccione ---;', '6', 'Tipo de Objeto', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('toba', '1369', '1000011', 'objeto', 'ef_combo_dao', 'objeto', NULL, 'dao: get_lista_objetos_toba;
clase: dao_editores;
include: admin/db/dao_editores.php;
clave: id;
valor: descripcion;
dependencias: objeto_clase;', '7', 'Objeto', NULL, NULL, NULL, NULL, NULL, '0');
