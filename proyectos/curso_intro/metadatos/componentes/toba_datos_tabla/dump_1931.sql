------------------------------------------------------------
--[1931]--  DT - sede 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'curso_intro', --proyecto
	'1931', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'12000006', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'DT - sede', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'curso_intro', --fuente_datos_proyecto
	'curso_intro', --fuente_datos
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
	'2007-05-07 18:07:02', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'curso_intro', --objeto_proyecto
	'1931', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'12000006', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'sede', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'curso_intro', --fuente_datos_proyecto
	'curso_intro', --fuente_datos
	'1', --permite_actualizacion_automatica
	NULL, --esquema
	NULL  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 45
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'curso_intro', --objeto_proyecto
	'1931', --objeto
	'45000009', --col_id
	'id_institucion', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'curso_intro', --objeto_proyecto
	'1931', --objeto
	'45000010', --col_id
	'id_sede', --columna
	'E', --tipo
	'1', --pk
	'soe_sedes_sede_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'curso_intro', --objeto_proyecto
	'1931', --objeto
	'45000011', --col_id
	'nombre', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'255', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'curso_intro', --objeto_proyecto
	'1931', --objeto
	'45000012', --col_id
	'cp', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'10', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
--- FIN Grupo de desarrollo 45
