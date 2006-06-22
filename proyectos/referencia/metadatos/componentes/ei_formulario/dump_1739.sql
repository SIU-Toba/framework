------------------------------------------------------------
--[1739]--  ABM Personas - Deportes 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('referencia', '1739', NULL, NULL, 'toba', 'objeto_ei_formulario', 'form_persona_deportes', 'operaciones_simples/abm_personas/form_persona_deportes.php', NULL, NULL, 'ABM Personas - Deportes', NULL, NULL, NULL, 'referencia', 'referencia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-11-15 03:26:55');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('referencia', '173', '1739', 'alta', '&Alta', '1', NULL, NULL, 'abm-input-eliminar', NULL, NULL, '1', NULL, '1', NULL, '0', NULL, 'no_cargado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('referencia', '174', '1739', 'baja', '&Eliminar', NULL, NULL, '¿Desea ELIMINAR el registro?', 'abm-input-eliminar', 'apex', 'borrar.gif', '1', NULL, '2', NULL, '0', NULL, 'cargado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('referencia', '175', '1739', 'modificacion', '&Modificacion', '1', NULL, NULL, 'abm-input', NULL, NULL, '1', NULL, '3', NULL, '0', NULL, 'cargado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('referencia', '176', '1739', 'cancelar', 'Ca&ncelar', '0', NULL, NULL, 'abm-input', NULL, NULL, '1', NULL, '4', NULL, '0', NULL, 'cargado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('referencia', '1739', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', NULL, NULL, NULL, '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, 'NO');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1739', '4393', 'deporte', 'ef_combo', 'deporte', '1', 'dao_:_ get_deportes_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ nombre_;_
no_seteado_:_ -- SELECCIONAR --_;_
', '1', 'Deporte', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1739', '4394', 'dia_semana', 'ef_combo', 'dia_semana', '1', 'dao_:_ get_dias_semana_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ desc_;_
no_seteado_:_ -- SELECCIONAR --_;_
', '2', 'Dia semana', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1739', '4395', 'hora_inicio', 'ef_combo', 'hora_inicio', '1', 'dao_:_ get_horas_dia_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ desc_;_
no_seteado_:_ -- SELECCIONAR --_;_
predeterminado_:_ 17_;_
', '3', 'Hora inicio', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1739', '4396', 'hora_fin', 'ef_combo', 'hora_fin', '1', 'dao_:_ get_horas_dia_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ desc_;_
no_seteado_:_ -- SELECCIONAR --_;_
predeterminado_:_ 19_;_
', '4', 'Hora fin.', NULL, NULL, NULL, NULL, NULL, '0');
