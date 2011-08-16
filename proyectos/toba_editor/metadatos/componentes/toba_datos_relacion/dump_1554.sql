------------------------------------------------------------
--[1554]--  Item 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'12', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Item', --nombre
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
	'2005-09-06 16:33:27', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'0', --debug
	'item, proyecto', --clave
	'3', --ap
	'12', --punto_montaje
	'ap_relacion_item', --ap_clase
	'db/ap_relacion_item.php', --ap_archivo
	'0', --sinc_susp_constraints
	'1', --sinc_orden_automatico
	'1'  --sinc_lock_optimista
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'123', --dep_id
	'1554', --objeto_consumidor
	'1553', --objeto_proveedor
	'base', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'124', --dep_id
	'1554', --objeto_consumidor
	'1558', --objeto_proveedor
	'objetos', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'125', --dep_id
	'1554', --objeto_consumidor
	'1555', --objeto_proveedor
	'permisos', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'30000050', --dep_id
	'1554', --objeto_consumidor
	'30000103', --objeto_proveedor
	'permisos_tablas', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'1', --asoc_id
	'base -> objetos', --identificador
	'toba_editor', --padre_proyecto
	'1553', --padre_objeto
	'base', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1558', --hijo_objeto
	'objetos', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'2', --asoc_id
	'base -> permisos', --identificador
	'toba_editor', --padre_proyecto
	'1553', --padre_objeto
	'base', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1555', --hijo_objeto
	'permisos', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'2'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'30000001', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1553', --padre_objeto
	'base', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'30000103', --hijo_objeto
	'permisos_tablas', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'1', --asoc_id
	'1553', --padre_objeto
	'229', --padre_clave
	'1558', --hijo_objeto
	'269'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'1', --asoc_id
	'1553', --padre_objeto
	'230', --padre_clave
	'1558', --hijo_objeto
	'270'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'2', --asoc_id
	'1553', --padre_objeto
	'229', --padre_clave
	'1555', --hijo_objeto
	'266'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'2', --asoc_id
	'1553', --padre_objeto
	'230', --padre_clave
	'1555', --hijo_objeto
	'268'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'30000001', --asoc_id
	'1553', --padre_objeto
	'229', --padre_clave
	'30000103', --hijo_objeto
	'30000012'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1554', --objeto
	'30000001', --asoc_id
	'1553', --padre_objeto
	'230', --padre_clave
	'30000103', --hijo_objeto
	'30000013'  --hijo_clave
);
