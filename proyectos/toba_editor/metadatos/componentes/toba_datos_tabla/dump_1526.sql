------------------------------------------------------------
--[1526]--  OBJETO - DATOS relacion asoc. 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_editor', --proyecto
	'1526', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'odt_dr_asociac', --subclase
	'db/odt_dr_asociac.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'OBJETO - DATOS relacion asoc.', --nombre
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
	'2005-08-28 03:16:13'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, ap, ap_clase, ap_archivo, tabla, alias, modificar_claves) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'apex_objeto_datos_rel_asoc', --tabla
	NULL, --alias
	NULL  --modificar_claves
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'155', --col_id
	'proyecto', --columna
	'C', --tipo
	'1', --pk
	NULL, --secuencia
	'15', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'156', --col_id
	'objeto', --columna
	'E', --tipo
	'1', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'157', --col_id
	'asoc_id', --columna
	'E', --tipo
	'1', --pk
	'apex_objeto_datos_rel_asoc_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'158', --col_id
	'identificador', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'40', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'159', --col_id
	'padre_proyecto', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'15', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'160', --col_id
	'padre_objeto', --columna
	'E', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'161', --col_id
	'padre_id', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'20', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'162', --col_id
	'padre_clave', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'60', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'163', --col_id
	'hijo_proyecto', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'15', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'164', --col_id
	'hijo_objeto', --columna
	'E', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'165', --col_id
	'hijo_id', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'20', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'166', --col_id
	'hijo_clave', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'60', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'167', --col_id
	'cascada', --columna
	'E', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_editor', --objeto_proyecto
	'1526', --objeto
	'168', --col_id
	'orden', --columna
	NULL, --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
--- FIN Grupo de desarrollo 0
