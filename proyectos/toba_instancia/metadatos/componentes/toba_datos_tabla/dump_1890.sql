------------------------------------------------------------
--[1890]--  Usuario - Prop.básicas 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_instancia', --proyecto
	'1890', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Usuario - Prop.básicas', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'toba_instancia', --fuente_datos_proyecto
	'toba_instancia', --fuente_datos
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
	'2006-11-02 17:47:47'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, ap, ap_clase, ap_archivo, tabla, alias, modificar_claves) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'0', --ap
	'apdb_usuario_basicas', --ap_clase
	'usuario/apdb_usuario_basicas.php', --ap_archivo
	'apex_usuario', --tabla
	NULL, --alias
	'0'  --modificar_claves
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'515', --col_id
	'usuario', --columna
	'C', --tipo
	'1', --pk
	NULL, --secuencia
	'20', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'516', --col_id
	'clave', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'128', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'517', --col_id
	'nombre', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'80', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'518', --col_id
	'email', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'80', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'519', --col_id
	'vencimiento', --columna
	'F', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'520', --col_id
	'parametro_a', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'100', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'521', --col_id
	'parametro_b', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'100', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'522', --col_id
	'parametro_c', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'100', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa) VALUES (
	'toba_instancia', --objeto_proyecto
	'1890', --objeto
	'523', --col_id
	'autentificacion', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	'10', --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL  --externa
);
--- FIN Grupo de desarrollo 0
