------------------------------------------------------------
--[1625]--  Prueba relacion 1 ->N  1 -> N 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'12000005', --punto_montaje
	'dr_prueba', --subclase
	'varios/extension_php/dr_prueba.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Prueba relacion 1 ->N  1 -> N', --nombre
	NULL, --titulo
	NULL, --colapsable
	'En 1 departamento trabajan N empleados, 
En 1 departamento se realizan N tareas asociadas a él.
1 empleado tiene N tareas.', --descripcion
	'toba_testing', --fuente_datos_proyecto
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
	'2005-10-04 03:01:13', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'0', --debug
	NULL, --clave
	'3', --ap
	'12000005', --punto_montaje
	'dr_ap_prueba', --ap_clase
	'varios/extension_php/dr_ap_prueba.php', --ap_archivo
	'0', --sinc_susp_constraints
	'1', --sinc_orden_automatico
	'1'  --sinc_lock_optimista
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_testing', --proyecto
	'712', --dep_id
	'1625', --objeto_consumidor
	'1743', --objeto_proveedor
	'depto', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'5'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_testing', --proyecto
	'716', --dep_id
	'1625', --objeto_consumidor
	'1748', --objeto_proveedor
	'depto_tareas', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_testing', --proyecto
	'713', --dep_id
	'1625', --objeto_consumidor
	'1744', --objeto_proveedor
	'empleado', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'4'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_testing', --proyecto
	'714', --dep_id
	'1625', --objeto_consumidor
	'1745', --objeto_proveedor
	'empleado_tareas', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'3'  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_testing', --proyecto
	'715', --dep_id
	'1625', --objeto_consumidor
	'1747', --objeto_proveedor
	'tarea', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'1', --asoc_id
	'depto - empleados', --identificador
	'toba_testing', --padre_proyecto
	'1743', --padre_objeto
	'depto', --padre_id
	NULL, --padre_clave
	'toba_testing', --hijo_proyecto
	'1744', --hijo_objeto
	'empleado', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'2', --asoc_id
	'empleado - tareas', --identificador
	'toba_testing', --padre_proyecto
	'1744', --padre_objeto
	'empleado', --padre_id
	NULL, --padre_clave
	'toba_testing', --hijo_proyecto
	'1745', --hijo_objeto
	'empleado_tareas', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'2'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'4', --asoc_id
	'depto - tareas depto', --identificador
	'toba_testing', --padre_proyecto
	'1743', --padre_objeto
	'depto', --padre_id
	NULL, --padre_clave
	'toba_testing', --hijo_proyecto
	'1748', --hijo_objeto
	'depto_tareas', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'4'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'6', --asoc_id
	'tarea - tareas depto', --identificador
	'toba_testing', --padre_proyecto
	'1747', --padre_objeto
	'tarea', --padre_id
	NULL, --padre_clave
	'toba_testing', --hijo_proyecto
	'1748', --hijo_objeto
	'depto_tareas', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'5'  --orden
);
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'19', --asoc_id
	NULL, --identificador
	'toba_testing', --padre_proyecto
	'1747', --padre_objeto
	'tarea', --padre_id
	NULL, --padre_clave
	'toba_testing', --hijo_proyecto
	'1745', --hijo_objeto
	'empleado_tareas', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'3'  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'1', --asoc_id
	'1743', --padre_objeto
	'360', --padre_clave
	'1744', --hijo_objeto
	'363'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'2', --asoc_id
	'1744', --padre_objeto
	'362', --padre_clave
	'1745', --hijo_objeto
	'365'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'4', --asoc_id
	'1743', --padre_objeto
	'360', --padre_clave
	'1748', --hijo_objeto
	'372'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'6', --asoc_id
	'1747', --padre_objeto
	'369', --padre_clave
	'1748', --hijo_objeto
	'371'  --hijo_clave
);
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'toba_testing', --proyecto
	'1625', --objeto
	'19', --asoc_id
	'1747', --padre_objeto
	'369', --padre_clave
	'1745', --hijo_objeto
	'366'  --hijo_clave
);
