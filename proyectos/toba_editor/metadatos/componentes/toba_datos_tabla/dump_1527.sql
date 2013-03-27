------------------------------------------------------------
--[1527]--  OBJETO - DATOS tabla 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_editor', --proyecto
	'1527', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'12', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'OBJETO - DATOS tabla', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'toba_editor', --fuente_datos_proyecto
	'instancia', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2005-08-28 03:17:10', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'12', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'apex_objeto_db_registros', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'toba_editor', --fuente_datos_proyecto
	'instancia', --fuente_datos
	'1', --permite_actualizacion_automatica
	NULL, --esquema
	NULL  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'169', --col_id
	'objeto_proyecto', --columna
	'C', --tipo
	'1', --pk
	NULL, --secuencia
	'15', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'170', --col_id
	'objeto', --columna
	'E', --tipo
	'1', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'171', --col_id
	'max_registros', --columna
	'E', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'172', --col_id
	'min_registros', --columna
	'E', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'173', --col_id
	'ap', --columna
	'E', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'174', --col_id
	'ap_clase', --columna
	'C', --tipo
	'0', --pk
	NULL, --secuencia
	'60', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'175', --col_id
	'ap_archivo', --columna
	'C', --tipo
	'0', --pk
	NULL, --secuencia
	'60', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'176', --col_id
	'tabla', --columna
	'C', --tipo
	'0', --pk
	NULL, --secuencia
	'60', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'177', --col_id
	'alias', --columna
	'C', --tipo
	'0', --pk
	NULL, --secuencia
	'60', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'328', --col_id
	'modificar_claves', --columna
	'E', --tipo
	'0', --pk
	NULL, --secuencia
	'1', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'1000229', --col_id
	'fuente_datos_proyecto', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'15', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'1000230', --col_id
	'fuente_datos', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'20', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
--- FIN Grupo de desarrollo 1

--- INICIO Grupo de desarrollo 12
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'12000028', --col_id
	'tabla_ext', --columna
	'X', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'apex_objeto_db_registros'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'12000238', --col_id
	'punto_montaje', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'apex_objeto_db_registros'  --tabla
);
--- FIN Grupo de desarrollo 12

--- INICIO Grupo de desarrollo 33
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'33000048', --col_id
	'permite_actualizacion_automatica', --columna
	'E', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'33000066', --col_id
	'esquema', --columna
	'X', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_editor', --objeto_proyecto
	'1527', --objeto
	'33000177', --col_id
	'esquema_ext', --columna
	'X', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
--- FIN Grupo de desarrollo 33
