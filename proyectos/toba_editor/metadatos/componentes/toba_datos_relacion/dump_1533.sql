------------------------------------------------------------
--[1533]--  Comp. datos_tabla 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'12', --punto_montaje
	'odr_datos_tabla', --subclase
	'db/odr_datos_tabla.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Comp. datos_tabla', --nombre
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
	'2005-08-28 03:40:38', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'0', --debug
	NULL, --clave
	'3', --ap
	'12', --punto_montaje
	'ap_relacion_datos_tabla', --ap_clase
	'db/ap_relacion_datos_tabla.php', --ap_archivo
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
	'103', --dep_id
	'1533', --objeto_consumidor
	'1501', --objeto_proveedor
	'base', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'104', --dep_id
	'1533', --objeto_consumidor
	'1528', --objeto_proveedor
	'columnas', --identificador
	'1', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'1000100', --dep_id
	'1533', --objeto_consumidor
	'1000232', --objeto_proveedor
	'externas', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'4'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'1000101', --dep_id
	'1533', --objeto_consumidor
	'1000233', --objeto_proveedor
	'externas_col', --identificador
	'', --parametros_a
	'', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'5'  --orden
);
--- FIN Grupo de desarrollo 1

--- INICIO Grupo de desarrollo 12
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'12000098', --dep_id
	'1533', --objeto_consumidor
	'12000115', --objeto_proveedor
	'fks', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'7'  --orden
);
--- FIN Grupo de desarrollo 12

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'105', --dep_id
	'1533', --objeto_consumidor
	'1527', --objeto_proveedor
	'prop_basicas', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'916', --dep_id
	'1533', --objeto_consumidor
	'1973', --objeto_proveedor
	'valores_unicos', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'6'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'7', --asoc_id
	'base -> prop_basicas', --identificador
	'toba_editor', --padre_proyecto
	'1501', --padre_objeto
	'base', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1527', --hijo_objeto
	'prop_basicas', --hijo_id
	NULL, --hijo_clave
	'0', --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'8', --asoc_id
	'base -> columnas', --identificador
	'toba_editor', --padre_proyecto
	'1527', --padre_objeto
	'prop_basicas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1528', --hijo_objeto
	'columnas', --hijo_id
	NULL, --hijo_clave
	'0', --cascada
	'2'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'32', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1527', --padre_objeto
	'prop_basicas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1973', --hijo_objeto
	'valores_unicos', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'6'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000001', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1527', --padre_objeto
	'prop_basicas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1000232', --hijo_objeto
	'externas', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000002', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1000232', --padre_objeto
	'externas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1000233', --hijo_objeto
	'externas_col', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'4'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000003', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1528', --padre_objeto
	'columnas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1000233', --hijo_objeto
	'externas_col', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'5'  --orden
);
--- FIN Grupo de desarrollo 1

--- INICIO Grupo de desarrollo 12
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'12000001', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1527', --padre_objeto
	'prop_basicas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'12000115', --hijo_objeto
	'fks', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'7'  --orden
);
--- FIN Grupo de desarrollo 12

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'7', --asoc_id
	'1501', --padre_objeto
	'21', --padre_clave
	'1527', --hijo_objeto
	'169'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'7', --asoc_id
	'1501', --padre_objeto
	'22', --padre_clave
	'1527', --hijo_objeto
	'170'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'7', --asoc_id
	'1501', --padre_objeto
	'35', --padre_clave
	'1527', --hijo_objeto
	'1000229'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'7', --asoc_id
	'1501', --padre_objeto
	'36', --padre_clave
	'1527', --hijo_objeto
	'1000230'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'8', --asoc_id
	'1527', --padre_objeto
	'169', --padre_clave
	'1528', --hijo_objeto
	'178'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'8', --asoc_id
	'1527', --padre_objeto
	'170', --padre_clave
	'1528', --hijo_objeto
	'179'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'32', --asoc_id
	'1527', --padre_objeto
	'169', --padre_clave
	'1973', --hijo_objeto
	'609'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'32', --asoc_id
	'1527', --padre_objeto
	'170', --padre_clave
	'1973', --hijo_objeto
	'610'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000001', --asoc_id
	'1527', --padre_objeto
	'169', --padre_clave
	'1000232', --hijo_objeto
	'1000070'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000001', --asoc_id
	'1527', --padre_objeto
	'170', --padre_clave
	'1000232', --hijo_objeto
	'1000071'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000002', --asoc_id
	'1000232', --padre_objeto
	'1000070', --padre_clave
	'1000233', --hijo_objeto
	'1000081'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000002', --asoc_id
	'1000232', --padre_objeto
	'1000071', --padre_clave
	'1000233', --hijo_objeto
	'1000082'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000002', --asoc_id
	'1000232', --padre_objeto
	'1000072', --padre_clave
	'1000233', --hijo_objeto
	'1000083'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000003', --asoc_id
	'1528', --padre_objeto
	'178', --padre_clave
	'1000233', --hijo_objeto
	'1000081'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000003', --asoc_id
	'1528', --padre_objeto
	'179', --padre_clave
	'1000233', --hijo_objeto
	'1000082'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'1000003', --asoc_id
	'1528', --padre_objeto
	'180', --padre_clave
	'1000233', --hijo_objeto
	'1000084'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'12000001', --asoc_id
	'1527', --padre_objeto
	'169', --padre_clave
	'12000115', --hijo_objeto
	'12000030'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1533', --objeto
	'12000001', --asoc_id
	'1527', --padre_objeto
	'170', --padre_clave
	'12000115', --hijo_objeto
	'12000031'  --hijo_clave
);
