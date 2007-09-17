------------------------------------------------------------
--[1531]--  Comp. ei_cuadro 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_editor', --proyecto
	'1531', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'odr_ei_cuadro', --subclase
	'db/odr_ei_cuadro.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Comp. ei_cuadro', --nombre
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
	'2005-08-28 03:33:09'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico) VALUES (
	'toba_editor', --proyecto
	'1531', --objeto
	'0', --debug
	NULL, --clave
	'3', --ap
	'ap_relacion_objeto', --ap_clase
	'db/ap_relacion_objeto.php', --ap_archivo
	'0', --sinc_susp_constraints
	'1'  --sinc_orden_automatico
);

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1531', --objeto
	'1', --asoc_id
	'base -> cuadro', --identificador
	'toba_editor', --padre_proyecto
	'1501', --padre_objeto
	'base', --padre_id
	'proyecto,objeto', --padre_clave
	'toba_editor', --hijo_proyecto
	'1523', --hijo_objeto
	'prop_basicas', --hijo_id
	'objeto_cuadro_proyecto,objeto_cuadro', --hijo_clave
	'0', --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1531', --objeto
	'2', --asoc_id
	'base -> columna', --identificador
	'toba_editor', --padre_proyecto
	'1523', --padre_objeto
	'prop_basicas', --padre_id
	'objeto_cuadro_proyecto,objeto_cuadro', --padre_clave
	'toba_editor', --hijo_proyecto
	'1524', --hijo_objeto
	'columnas', --hijo_id
	'objeto_cuadro_proyecto,objeto_cuadro', --hijo_clave
	'0', --cascada
	'2'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1531', --objeto
	'3', --asoc_id
	'base -> eventos', --identificador
	'toba_editor', --padre_proyecto
	'1501', --padre_objeto
	'base', --padre_id
	'proyecto,objeto', --padre_clave
	'toba_editor', --hijo_proyecto
	'1505', --hijo_objeto
	'eventos', --hijo_id
	'proyecto,objeto', --hijo_clave
	'0', --cascada
	'3'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1531', --objeto
	'5', --asoc_id
	'base -> corte', --identificador
	'toba_editor', --padre_proyecto
	'1523', --padre_objeto
	'prop_basicas', --padre_id
	'objeto_cuadro_proyecto,objeto_cuadro', --padre_clave
	'toba_editor', --hijo_proyecto
	'1612', --hijo_objeto
	'cortes', --hijo_id
	'objeto_cuadro_proyecto,objeto_cuadro', --hijo_clave
	NULL, --cascada
	'4'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 10
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1531', --objeto
	'10000003', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1505', --padre_objeto
	'eventos', --padre_id
	'proyecto,evento_id', --padre_clave
	'toba_editor', --hijo_proyecto
	'10000033', --hijo_objeto
	'puntos_control', --hijo_id
	'proyecto,evento_id', --hijo_clave
	NULL, --cascada
	'5'  --orden
);
--- FIN Grupo de desarrollo 10

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'94', --dep_id
	'1531', --objeto_consumidor
	'1501', --objeto_proveedor
	'base', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'95', --dep_id
	'1531', --objeto_consumidor
	'1524', --objeto_proveedor
	'columnas', --identificador
	'1', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'96', --dep_id
	'1531', --objeto_consumidor
	'1612', --objeto_proveedor
	'cortes', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'97', --dep_id
	'1531', --objeto_consumidor
	'1505', --objeto_proveedor
	'eventos', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'98', --dep_id
	'1531', --objeto_consumidor
	'1523', --objeto_proveedor
	'prop_basicas', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 10
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'10000039', --dep_id
	'1531', --objeto_consumidor
	'10000033', --objeto_proveedor
	'puntos_control', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 10
