------------------------------------------------------------
--[1270]--  Dependencias en formulario 
------------------------------------------------------------
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES ('referencia', '1270', NULL, NULL, 'toba', 'objeto_ei_formulario', NULL, NULL, NULL, NULL, 'Dependencias en formulario', NULL, NULL, NULL, 'referencia', 'referencia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-06-01 10:16:48');
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES ('referencia', '1', '1270', 'modificacion', 'Modificacion', '1', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_agregar, ev_agregar_etiq, ev_mod_modificar, ev_mod_modificar_etiq, ev_mod_eliminar, ev_mod_eliminar_etiq, ev_mod_limpiar, ev_mod_limpiar_etiq, ev_mod_clave, clase_proyecto, clase, auto_reset, ancho, ancho_etiqueta, campo_bl, scroll, filas, filas_agregar, filas_agregar_online, filas_undo, filas_ordenar, columna_orden, filas_numerar, ev_seleccion, alto, analisis_cambios) VALUES ('referencia', '1270', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '150px', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1313', 'descripcion_dao', 'ef_editable', 'descripcion_dao', NULL, 'include_:_ efs/dependencias/dao.php_;_
clase_:_ dao_;_
dao_:_ get_descripcion_;_
dependencias_:_ localidad_dao_;_
tamano_:_ 40_;_
', '10', 'Descripción DAO', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1314', 'descripcion_sql', 'ef_editable', 'descripcion_sql', NULL, 'tamano_:_ 40_;_
sql_:_ SELECT \'Esta es la descripción de Bahía Blanca\' WHERE \'bb\' = \'%localidad_sql%\'
UNION
SELECT \'Esta es la descripción de San Juan\' WHERE \'sj\' = \'%localidad_sql%\'_;_
dependencias_:_ localidad_sql_;_
', '5', 'Descripción SQL', NULL, NULL, NULL, NULL, NULL, '0');
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1315', 'localidad_dao', 'ef_combo', 'localidad_dao', NULL, 'include_:_ efs/dependencias/dao.php_;_
clase_:_ dao_;_
dao_:_ get_localidades_;_
no_seteado_:_ -- Seleccione --_;_
clave_:_ id_;_
valor_:_ valor_;_
dependencias_:_ pais_dao, provincia_dao_;_
', '9', 'Localidad con DAO', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1316', 'localidad_sql', 'ef_combo', 'localidad_sql', NULL, 'no_seteado_:_ -- Seleccione --_;_
sql_:_ SELECT \'bb\', \'Bahía Blanca\' WHERE \'ar\' = \'%pais_sql%\' AND \'ba\' = \'%provincia_sql%\'
UNION
SELECT \'sj\', \'San Juan\' WHERE \'ar\' = \'%pais_sql%\' AND \'sj\' = \'%provincia_sql%\'_;_
dependencias_:_ pais_sql, provincia_sql_;_
', '3', 'Localidad con SQL', NULL, 'En la SQL de la localidad se dejan marcas  %pais_sql% y %provincia_sql%, correspondientes a los ids de los efs del que depende, para que liguen su valor cuando se seleccionen.', NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1317', 'pais_dao', 'ef_combo', 'pais_dao', NULL, 'include_:_ efs/dependencias/dao.php_;_
clase_:_ dao_;_
dao_:_ get_paises_;_
no_seteado_:_ -- Seleccione --_;_
clave_:_ id_;_
valor_:_ valor_;_
', '7', 'País con DAO', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1318', 'pais_sql', 'ef_combo', 'pais_sql', NULL, 'no_seteado_:_ -- Seleccione --_;_
sql_:_ SELECT \'ar\', \'Argentina\'
UNION
SELECT \'br\', \'Brasil\'_;_
', '1', 'País con SQL', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1319', 'provincia_dao', 'ef_combo', 'provincia_dao', NULL, 'include_:_ efs/dependencias/dao.php_;_
clase_:_ dao_;_
dao_:_ get_provincias_;_
no_seteado_:_ -- Seleccione --_;_
clave_:_ id_;_
valor_:_ valor_;_
dependencias_:_ pais_dao_;_
', '8', 'Provincia con DAO', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1320', 'provincia_sql', 'ef_combo', 'provincia_sql', NULL, 'no_seteado_:_ -- Seleccione --_;_
sql_:_ SELECT \'ba\', \'Buenos Aires\' WHERE \'ar\' = \'%pais_sql%\'
UNION
SELECT \'sj\', \'San Juan\' WHERE \'ar\' = \'%pais_sql%\'_;_
dependencias_:_ pais_sql_;_
', '2', 'Provincia con SQL', NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '1321', 'separador', 'ef_barra_divisora', 'separador', NULL, '_;_
', '6', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO apex_objeto_ei_formulario_ef (objeto_ei_formulario_proyecto, objeto_ei_formulario, objeto_ei_formulario_fila, identificador, elemento_formulario, columnas, obligatorio, inicializacion, orden, etiqueta, etiqueta_estilo, descripcion, colapsado, desactivado, estilo, total) VALUES ('referencia', '1270', '4232', 'barrio_sql', 'ef_multi_seleccion_check', 'barrio_sql', NULL, 'dependencias_:_ localidad_sql_;_
dependencia_estricta_:_ 1_;_
sql_:_ SELECT 0, \'Universitario\' WHERE \'%localidad_sql%\' = \'bb\'
UNION
SELECT 1, \'Centro\' WHERE \'%localidad_sql%\' = \'bb\'
UNION
SELECT 2, \'Rosendo Lopez\' WHERE \'%localidad_sql%\' = \'bb\'_;_
', '4', 'Barrio con SQL', NULL, NULL, NULL, NULL, NULL, '0');
