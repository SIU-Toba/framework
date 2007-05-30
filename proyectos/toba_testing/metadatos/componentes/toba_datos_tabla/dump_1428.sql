------------------------------------------------------------
--[1428]--  ABM - Paises 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_testing', --proyecto
	'1428', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'ABM - Paises', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'toba', --fuente_datos_proyecto
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
	'2005-08-17 18:21:54'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, ap, ap_clase, ap_archivo, tabla, alias, modificar_claves) VALUES (
	'toba_testing', --objeto_proyecto
	'1428', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'0', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'apex_test_paises', --tabla
	NULL, --alias
	NULL  --modificar_claves
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_testing', --objeto_proyecto
	'1428', --objeto
	'18', --col_id
	'pais', --columna
	'E', --tipo
	'1', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_testing', --objeto_proyecto
	'1428', --objeto
	'19', --col_id
	'nombre', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'40', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_testing', --objeto_proyecto
	'1428', --objeto
	'20', --col_id
	'codigoiso', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'2', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
--- FIN Grupo de desarrollo 0
