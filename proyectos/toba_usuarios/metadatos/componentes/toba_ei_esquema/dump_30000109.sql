------------------------------------------------------------
--[30000109]--  Mantenimiento de Perfiles Funcionales - esquema 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_usuarios', --proyecto
	'30000109', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ei_esquema', --clase
	'12000004', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Mantenimiento de Perfiles Funcionales - esquema', --nombre
	'Membresía de Perfiles', --titulo
	'0', --colapsable
	NULL, --descripcion
	NULL, --fuente_datos_proyecto
	NULL, --fuente_datos
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
	'2009-10-19 13:47:41', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_esquema
------------------------------------------------------------
INSERT INTO apex_objeto_esquema (objeto_esquema_proyecto, objeto_esquema, parser, descripcion, dot, debug, formato, modelo_ejecucion, modelo_ejecucion_cache, tipo_incrustacion, ancho, alto, dirigido, sql) VALUES (
	'toba_usuarios', --objeto_esquema_proyecto
	'30000109', --objeto_esquema
	NULL, --parser
	NULL, --descripcion
	NULL, --dot
	NULL, --debug
	'png', --formato
	NULL, --modelo_ejecucion
	'0', --modelo_ejecucion_cache
	NULL, --tipo_incrustacion
	NULL, --ancho
	NULL, --alto
	'1', --dirigido
	NULL  --sql
);
