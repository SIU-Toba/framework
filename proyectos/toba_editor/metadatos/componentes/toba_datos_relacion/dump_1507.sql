------------------------------------------------------------
--[1507]--  Comp. ci 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'12', --punto_montaje
	'odr_ci', --subclase
	'db/odr_ci.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Comp. ci', --nombre
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
	'2005-08-19 17:28:22', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'0', --debug
	'objeto, proyecto', --clave
	'3', --ap
	'12', --punto_montaje
	'ap_relacion_objeto', --ap_clase
	'db/ap_relacion_objeto.php', --ap_archivo
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
	'82', --dep_id
	'1507', --objeto_consumidor
	'1501', --objeto_proveedor
	'base', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'5'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'83', --dep_id
	'1507', --objeto_consumidor
	'1502', --objeto_proveedor
	'dependencias', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'4'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'84', --dep_id
	'1507', --objeto_consumidor
	'1505', --objeto_proveedor
	'eventos', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 33
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'33000012', --dep_id
	'1507', --objeto_consumidor
	'33000011', --objeto_proveedor
	'eventos_pantalla', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'33000011', --dep_id
	'1507', --objeto_consumidor
	'33000010', --objeto_proveedor
	'objetos_pantalla', --identificador
	'0', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 33

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'85', --dep_id
	'1507', --objeto_consumidor
	'1504', --objeto_proveedor
	'pantallas', --identificador
	'1', --parametros_a
	'0', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'86', --dep_id
	'1507', --objeto_consumidor
	'1503', --objeto_proveedor
	'prop_basicas', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 10
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_editor', --proyecto
	'10000058', --dep_id
	'1507', --objeto_consumidor
	'10000033', --objeto_proveedor
	'puntos_control', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'6'  --orden
);
--- FIN Grupo de desarrollo 10

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'1', --asoc_id
	'base -> dependencias', --identificador
	'toba_editor', --padre_proyecto
	'1501', --padre_objeto
	'base', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1502', --hijo_objeto
	'dependencias', --hijo_id
	NULL, --hijo_clave
	'0', --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'2', --asoc_id
	'base -> eventos', --identificador
	'toba_editor', --padre_proyecto
	'1501', --padre_objeto
	'base', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1505', --hijo_objeto
	'eventos', --hijo_id
	NULL, --hijo_clave
	'0', --cascada
	'2'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'3', --asoc_id
	'base -> prop_basicas', --identificador
	'toba_editor', --padre_proyecto
	'1501', --padre_objeto
	'base', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1503', --hijo_objeto
	'prop_basicas', --hijo_id
	NULL, --hijo_clave
	'0', --cascada
	'3'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'4', --asoc_id
	'base -> pantalla', --identificador
	'toba_editor', --padre_proyecto
	'1503', --padre_objeto
	'prop_basicas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'1504', --hijo_objeto
	'pantallas', --hijo_id
	NULL, --hijo_clave
	'0', --cascada
	'4'  --orden
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 10
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'10000009', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1505', --padre_objeto
	'eventos', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'10000033', --hijo_objeto
	'puntos_control', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'5'  --orden
);
--- FIN Grupo de desarrollo 10

--- INICIO Grupo de desarrollo 33
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000004', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1502', --padre_objeto
	'dependencias', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'33000010', --hijo_objeto
	'objetos_pantalla', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'6'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000005', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1504', --padre_objeto
	'pantallas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'33000010', --hijo_objeto
	'objetos_pantalla', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'7'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000006', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1505', --padre_objeto
	'eventos', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'33000011', --hijo_objeto
	'eventos_pantalla', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'8'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000007', --asoc_id
	NULL, --identificador
	'toba_editor', --padre_proyecto
	'1504', --padre_objeto
	'pantallas', --padre_id
	NULL, --padre_clave
	'toba_editor', --hijo_proyecto
	'33000011', --hijo_objeto
	'eventos_pantalla', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'9'  --orden
);
--- FIN Grupo de desarrollo 33

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'1', --asoc_id
	'1501', --padre_objeto
	'21', --padre_clave
	'1502', --hijo_objeto
	'48'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'1', --asoc_id
	'1501', --padre_objeto
	'22', --padre_clave
	'1502', --hijo_objeto
	'49'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'2', --asoc_id
	'1501', --padre_objeto
	'21', --padre_clave
	'1505', --hijo_objeto
	'87'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'2', --asoc_id
	'1501', --padre_objeto
	'22', --padre_clave
	'1505', --hijo_objeto
	'88'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'3', --asoc_id
	'1501', --padre_objeto
	'21', --padre_clave
	'1503', --hijo_objeto
	'56'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'3', --asoc_id
	'1501', --padre_objeto
	'22', --padre_clave
	'1503', --hijo_objeto
	'57'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'4', --asoc_id
	'1503', --padre_objeto
	'56', --padre_clave
	'1504', --hijo_objeto
	'75'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'4', --asoc_id
	'1503', --padre_objeto
	'57', --padre_clave
	'1504', --hijo_objeto
	'76'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'10000009', --asoc_id
	'1505', --padre_objeto
	'87', --padre_clave
	'10000033', --hijo_objeto
	'10000010'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'10000009', --asoc_id
	'1505', --padre_objeto
	'88', --padre_clave
	'10000033', --hijo_objeto
	'544'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'10000009', --asoc_id
	'1505', --padre_objeto
	'335', --padre_clave
	'10000033', --hijo_objeto
	'10000012'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000004', --asoc_id
	'1502', --padre_objeto
	'48', --padre_clave
	'33000010', --hijo_objeto
	'33000010'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000004', --asoc_id
	'1502', --padre_objeto
	'49', --padre_clave
	'33000010', --hijo_objeto
	'33000012'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000004', --asoc_id
	'1502', --padre_objeto
	'332', --padre_clave
	'33000010', --hijo_objeto
	'33000014'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000005', --asoc_id
	'1504', --padre_objeto
	'75', --padre_clave
	'33000010', --hijo_objeto
	'33000010'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000005', --asoc_id
	'1504', --padre_objeto
	'76', --padre_clave
	'33000010', --hijo_objeto
	'33000012'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000005', --asoc_id
	'1504', --padre_objeto
	'77', --padre_clave
	'33000010', --hijo_objeto
	'33000011'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000006', --asoc_id
	'1505', --padre_objeto
	'87', --padre_clave
	'33000011', --hijo_objeto
	'33000019'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000006', --asoc_id
	'1505', --padre_objeto
	'335', --padre_clave
	'33000011', --hijo_objeto
	'33000018'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000007', --asoc_id
	'1504', --padre_objeto
	'75', --padre_clave
	'33000011', --hijo_objeto
	'33000019'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000007', --asoc_id
	'1504', --padre_objeto
	'76', --padre_clave
	'33000011', --hijo_objeto
	'33000017'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_editor', --proyecto
	'1507', --objeto
	'33000007', --asoc_id
	'1504', --padre_objeto
	'77', --padre_clave
	'33000011', --hijo_objeto
	'33000016'  --hijo_clave
);
