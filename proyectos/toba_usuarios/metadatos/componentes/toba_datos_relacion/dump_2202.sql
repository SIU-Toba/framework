------------------------------------------------------------
--[2202]--  Mantenimiento de Perfiles Funcionales - datos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'12000004', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Mantenimiento de Perfiles Funcionales - datos', --nombre
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
	'2008-03-17 18:06:43', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'0', --debug
	NULL, --clave
	'3', --ap
	'12000004', --punto_montaje
	'datos_relacion_perfiles', --ap_clase
	'perfiles/perfil_funcional/datos_relacion_perfiles.php', --ap_archivo
	'0', --sinc_susp_constraints
	'1', --sinc_orden_automatico
	'1'  --sinc_lock_optimista
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_usuarios', --proyecto
	'1114', --dep_id
	'2202', --objeto_consumidor
	'2206', --objeto_proveedor
	'accesos', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_usuarios', --proyecto
	'30000053', --dep_id
	'2202', --objeto_consumidor
	'30000107', --objeto_proveedor
	'membresia', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 30

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_usuarios', --proyecto
	'1113', --dep_id
	'2202', --objeto_consumidor
	'2205', --objeto_proveedor
	'permisos', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_usuarios', --proyecto
	'1112', --dep_id
	'2202', --objeto_consumidor
	'2204', --objeto_proveedor
	'restricciones', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'38', --asoc_id
	NULL, --identificador
	'toba_usuarios', --padre_proyecto
	'2206', --padre_objeto
	'accesos', --padre_id
	NULL, --padre_clave
	'toba_usuarios', --hijo_proyecto
	'2205', --hijo_objeto
	'permisos', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'39', --asoc_id
	NULL, --identificador
	'toba_usuarios', --padre_proyecto
	'2206', --padre_objeto
	'accesos', --padre_id
	NULL, --padre_clave
	'toba_usuarios', --hijo_proyecto
	'2204', --hijo_objeto
	'restricciones', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'2'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'30000002', --asoc_id
	NULL, --identificador
	'toba_usuarios', --padre_proyecto
	'2206', --padre_objeto
	'accesos', --padre_id
	NULL, --padre_clave
	'toba_usuarios', --hijo_proyecto
	'30000107', --hijo_objeto
	'membresia', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'38', --asoc_id
	'2206', --padre_objeto
	'742', --padre_clave
	'2205', --hijo_objeto
	'736'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'38', --asoc_id
	'2206', --padre_objeto
	'743', --padre_clave
	'2205', --hijo_objeto
	'737'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'39', --asoc_id
	'2206', --padre_objeto
	'742', --padre_clave
	'2204', --hijo_objeto
	'739'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'39', --asoc_id
	'2206', --padre_objeto
	'743', --padre_clave
	'2204', --hijo_objeto
	'740'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'30000002', --asoc_id
	'2206', --padre_objeto
	'742', --padre_clave
	'30000107', --hijo_objeto
	'30000018'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'30000002', --asoc_id
	'2206', --padre_objeto
	'743', --padre_clave
	'30000107', --hijo_objeto
	'30000019'  --hijo_clave
);
