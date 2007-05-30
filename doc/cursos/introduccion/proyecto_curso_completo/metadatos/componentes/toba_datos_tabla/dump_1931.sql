------------------------------------------------------------
--[1931]--  Sedes 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'curso', --proyecto
	'1931', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Sedes', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'curso', --fuente_datos_proyecto
	'curso', --fuente_datos
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
	'2007-05-07 18:07:02'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, ap, ap_clase, ap_archivo, tabla, alias, modificar_claves) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'soe_sedes', --tabla
	NULL, --alias
	'0'  --modificar_claves
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'557', --col_id
	'institucion', --columna
	'E', --tipo
	'1', --pk
	'', --secuencia
	'-1', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0'  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'558', --col_id
	'sede', --columna
	'E', --tipo
	'1', --pk
	'soe_sedes_sede_seq', --secuencia
	'-1', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0'  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'559', --col_id
	'nombre', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'255', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0'  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'560', --col_id
	'idpais', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'2', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'1'  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'561', --col_id
	'idprovincia', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'4', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'1'  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'562', --col_id
	'codigopostal', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'10', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0'  --externa
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros_ext
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_ext (objeto_proyecto, objeto, externa_id, tipo, sincro_continua, metodo, clase, include, sql) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'5', --externa_id
	'dao', --tipo
	'1', --sincro_continua
	'get_pais_localidad', --metodo
	'soe_consultas', --clase
	'lib/soe_consultas.php', --include
	NULL  --sql
);
INSERT INTO apex_objeto_db_registros_ext (objeto_proyecto, objeto, externa_id, tipo, sincro_continua, metodo, clase, include, sql) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'6', --externa_id
	'dao', --tipo
	'1', --sincro_continua
	'get_provincia_localidad', --metodo
	'soe_consultas', --clase
	'lib/soe_consultas.php', --include
	NULL  --sql
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros_ext_col
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'5', --externa_id
	'560', --col_id
	'1'  --es_resultado
);
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'5', --externa_id
	'562', --col_id
	'0'  --es_resultado
);
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'6', --externa_id
	'561', --col_id
	'1'  --es_resultado
);
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'curso', --objeto_proyecto
	'1931', --objeto
	'6', --externa_id
	'562', --col_id
	'0'  --es_resultado
);
