------------------------------------------------------------
--[1732]--  ABM Personas 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_referencia', --proyecto
	'1732', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'ABM Personas', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'toba_referencia', --fuente_datos_proyecto
	'toba_referencia', --fuente_datos
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
	'2005-11-15 03:03:56'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico) VALUES (
	'toba_referencia', --proyecto
	'1732', --objeto
	'0', --debug
	NULL, --clave
	'2', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'0', --sinc_susp_constraints
	'1'  --sinc_orden_automatico
);

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_referencia', --proyecto
	'1732', --objeto
	'1', --asoc_id
	'juegos', --identificador
	'toba_referencia', --padre_proyecto
	'1733', --padre_objeto
	'persona', --padre_id
	'id', --padre_clave
	'toba_referencia', --hijo_proyecto
	'1734', --hijo_objeto
	'juegos', --hijo_id
	'persona', --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_referencia', --proyecto
	'1732', --objeto
	'2', --asoc_id
	'deportes', --identificador
	'toba_referencia', --padre_proyecto
	'1733', --padre_objeto
	'persona', --padre_id
	'id', --padre_clave
	'toba_referencia', --hijo_proyecto
	'1735', --hijo_objeto
	'deportes', --hijo_id
	'persona', --hijo_clave
	NULL, --cascada
	'2'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'704', --dep_id
	'1732', --objeto_consumidor
	'1735', --objeto_proveedor
	'deportes', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'703', --dep_id
	'1732', --objeto_consumidor
	'1734', --objeto_proveedor
	'juegos', --identificador
	'0', --parametros_a
	'2', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'702', --dep_id
	'1732', --objeto_consumidor
	'1733', --objeto_proveedor
	'persona', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
--- FIN Grupo de desarrollo 0
