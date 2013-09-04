------------------------------------------------------------
--[12000118]--  DT - maestra 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 12
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_testing', --proyecto
	'12000118', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'12000005', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'DT - maestra', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'toba_testing', --fuente_datos_proyecto
	'testing', --fuente_datos
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
	'2010-07-26 20:52:00', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 12

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'12000005', --punto_montaje
	'4', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'maestra', --tabla
	'esclava', --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'toba_testing', --fuente_datos_proyecto
	'testing', --fuente_datos
	'1', --permite_actualizacion_automatica
	NULL, --esquema
	NULL  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 12
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'12000046', --col_id
	'nombre', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'maestra'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'12000050', --col_id
	'fk_proyecto', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'esclava'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'12000051', --col_id
	'fk_identificador', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'esclava'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'12000052', --col_id
	'apellido', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'esclava'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'12000053', --col_id
	'proyecto', --columna
	'E', --tipo
	'1', --pk
	'"seq_maestra"', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'maestra'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'12000054', --col_id
	'identificador', --columna
	'C', --tipo
	'1', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'maestra'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'12000057', --col_id
	'id', --columna
	'E', --tipo
	'1', --pk
	'"seq_esclava"', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'esclava'  --tabla
);
--- FIN Grupo de desarrollo 12

------------------------------------------------------------
-- apex_objeto_db_columna_fks
------------------------------------------------------------

--- INICIO Grupo de desarrollo 12
INSERT INTO apex_objeto_db_columna_fks (id, objeto_proyecto, objeto, tabla, columna, tabla_ext, columna_ext) VALUES (
	'12000003', --id
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'maestra', --tabla
	'proyecto', --columna
	'esclava', --tabla_ext
	'fk_proyecto'  --columna_ext
);
INSERT INTO apex_objeto_db_columna_fks (id, objeto_proyecto, objeto, tabla, columna, tabla_ext, columna_ext) VALUES (
	'12000004', --id
	'toba_testing', --objeto_proyecto
	'12000118', --objeto
	'maestra', --tabla
	'identificador', --columna
	'esclava', --tabla_ext
	'fk_identificador'  --columna_ext
);
--- FIN Grupo de desarrollo 12
