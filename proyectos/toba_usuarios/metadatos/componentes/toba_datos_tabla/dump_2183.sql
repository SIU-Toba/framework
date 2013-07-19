------------------------------------------------------------
--[2183]--  Usuario - Proyecto 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_usuarios', --proyecto
	'2183', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'12000004', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Usuario - Proyecto', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'toba_usuarios', --fuente_datos_proyecto
	'toba_usuarios', --fuente_datos
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
	'2008-02-25 17:56:35', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'12000004', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'apex_usuario_proyecto', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'toba_usuarios', --fuente_datos_proyecto
	'toba_usuarios', --fuente_datos
	'1', --permite_actualizacion_automatica
	NULL, --esquema
	NULL  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'723', --col_id
	'grupo_acceso', --columna
	'C', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'1', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'724', --col_id
	'grupo_acceso_desc', --columna
	'C', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'1', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'725', --col_id
	'proyecto', --columna
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
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'726', --col_id
	'proyecto_nombre', --columna
	'C', --tipo
	'0', --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'1', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'727', --col_id
	'usuario', --columna
	'C', --tipo
	'1', --pk
	NULL, --secuencia
	'20', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'728', --col_id
	'usuario_grupo_acc', --columna
	'C', --tipo
	'1', --pk
	NULL, --secuencia
	'20', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros_ext
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_ext (objeto_proyecto, objeto, externa_id, tipo, sincro_continua, metodo, clase, include, punto_montaje, sql, dato_estricto, carga_dt, carga_consulta_php, permite_carga_masiva, metodo_masivo) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'5', --externa_id
	'dao', --tipo
	'1', --sincro_continua
	'get_descripcion_grupo_acceso', --metodo
	'consultas_instancia', --clase
	'lib/consultas_instancia.php', --include
	NULL, --punto_montaje
	NULL, --sql
	'1', --dato_estricto
	NULL, --carga_dt
	NULL, --carga_consulta_php
	'0', --permite_carga_masiva
	NULL  --metodo_masivo
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_db_registros_ext_col
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'5', --externa_id
	'723', --col_id
	'1'  --es_resultado
);
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'5', --externa_id
	'724', --col_id
	'1'  --es_resultado
);
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'5', --externa_id
	'725', --col_id
	'0'  --es_resultado
);
INSERT INTO apex_objeto_db_registros_ext_col (objeto_proyecto, objeto, externa_id, col_id, es_resultado) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'5', --externa_id
	'728', --col_id
	'0'  --es_resultado
);

------------------------------------------------------------
-- apex_objeto_db_registros_uniq
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_db_registros_uniq (objeto_proyecto, objeto, uniq_id, columnas) VALUES (
	'toba_usuarios', --objeto_proyecto
	'2183', --objeto
	'9', --uniq_id
	'proyecto,usuario_grupo_acc'  --columnas
);
--- FIN Grupo de desarrollo 0
