------------------------------------------------------------
--[2191]--  Usuario - editar - editor - datos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_usuarios', --proyecto
	'2191', --objeto
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
	'Usuario - editar - editor - datos', --nombre
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
	'2008-02-26 15:55:03', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'toba_usuarios', --proyecto
	'2191', --objeto
	'0', --debug
	NULL, --clave
	'2', --ap
	'12000004', --punto_montaje
	NULL, --ap_clase
	NULL, --ap_archivo
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
	'1097', --dep_id
	'2191', --objeto_consumidor
	'2182', --objeto_proveedor
	'basica', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 33
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_usuarios', --proyecto
	'33000040', --dep_id
	'2191', --objeto_consumidor
	'33000064', --objeto_proveedor
	'pregunta_secreta', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 33

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_usuarios', --proyecto
	'1098', --dep_id
	'2191', --objeto_consumidor
	'2183', --objeto_proveedor
	'proyecto', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_usuarios', --proyecto
	'1154', --dep_id
	'2191', --objeto_consumidor
	'2260', --objeto_proveedor
	'proyecto_pd', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
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
	'2191', --objeto
	'37', --asoc_id
	NULL, --identificador
	'toba_usuarios', --padre_proyecto
	'2182', --padre_objeto
	'basica', --padre_id
	NULL, --padre_clave
	'toba_usuarios', --hijo_proyecto
	'2183', --hijo_objeto
	'proyecto', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_usuarios', --proyecto
	'2191', --objeto
	'42', --asoc_id
	NULL, --identificador
	'toba_usuarios', --padre_proyecto
	'2182', --padre_objeto
	'basica', --padre_id
	NULL, --padre_clave
	'toba_usuarios', --hijo_proyecto
	'2260', --hijo_objeto
	'proyecto_pd', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'2'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 33
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_usuarios', --proyecto
	'2191', --objeto
	'33000011', --asoc_id
	NULL, --identificador
	'toba_usuarios', --padre_proyecto
	'2182', --padre_objeto
	'basica', --padre_id
	NULL, --padre_clave
	'toba_usuarios', --hijo_proyecto
	'33000064', --hijo_objeto
	'pregunta_secreta', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
--- FIN Grupo de desarrollo 33

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2191', --objeto
	'37', --asoc_id
	'2182', --padre_objeto
	'721', --padre_clave
	'2183', --hijo_objeto
	'727'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2191', --objeto
	'42', --asoc_id
	'2182', --padre_objeto
	'721', --padre_clave
	'2260', --hijo_objeto
	'787'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_usuarios', --proyecto
	'2191', --objeto
	'33000011', --asoc_id
	'2182', --padre_objeto
	'721', --padre_clave
	'33000064', --hijo_objeto
	'33000059'  --hijo_clave
);
