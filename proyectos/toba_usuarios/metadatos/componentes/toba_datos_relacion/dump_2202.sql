------------------------------------------------------------
--[2202]--  Mantenimiento de Perfiles Funcionales - datos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
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
	'2008-03-17 18:06:43'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico) VALUES (
	'toba_usuarios', --proyecto
	'2202', --objeto
	'0', --debug
	NULL, --clave
	'2', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'0', --sinc_susp_constraints
	'1'  --sinc_orden_automatico
);
