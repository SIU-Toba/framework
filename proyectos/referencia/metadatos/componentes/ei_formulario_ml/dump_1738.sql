------------------------------------------------------------
--[1738]--  ABM de Personas - Juegos 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('referencia', '1738', NULL, NULL, 'toba', 'objeto_ei_formulario_ml', 'form_persona_juegos', 'operaciones_simples/abm_personas/form_persona_juegos.php', NULL, NULL, 'ABM de Personas - Juegos', NULL, NULL, NULL, 'referencia', 'referencia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-11-15 03:26:01');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('referencia', '167', '1738', 'modificacion', '&Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, '1', NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('referencia', '1738', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, 'LINEA');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1738', '4389', 'juego', 'ef_combo', 'juego', '1', 'dao_:_ get_juegos_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ nombre_;_
no_seteado_:_ -- SELECCIONAR --_;_
', '1', 'Juego', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1738', '4390', 'dia_semana', 'ef_combo', 'dia_semana', '1', 'dao_:_ get_dias_semana_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ desc_;_
no_seteado_:_ -- SELECCIONAR --_;_
', '2', 'Dia semana', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1738', '4391', 'hora_inicio', 'ef_combo', 'hora_inicio', '1', 'dao_:_ get_horas_dia_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ desc_;_
no_seteado_:_ -- SELECCIONAR --_;_
predeterminado_:_ 17_;_
', '3', 'Hora inicio', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1738', '4392', 'hora_fin', 'ef_combo', 'hora_fin', '1', 'dao_:_ get_horas_dia_;_
clase_:_ consultas_;_
include_:_ operaciones_simples/consultas.php_;_
clave_:_ id_;_
valor_:_ desc_;_
no_seteado_:_ -- SELECCIONAR --_;_
predeterminado_:_ 19_;_
', '4', 'Hora fin', NULL, NULL, NULL, NULL, NULL, '0');
