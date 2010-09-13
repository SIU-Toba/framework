------------------------------------------------------------
--[12000138]--  Ejemplo 2 - grafico 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 12
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_referencia', --proyecto
	'12000138', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ei_grafico', --clase
	'12000003', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Ejemplo 2 - grafico', --nombre
	NULL, --titulo
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
	'2010-07-08 22:18:47', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 12

------------------------------------------------------------
-- apex_objeto_grafico
------------------------------------------------------------
INSERT INTO apex_objeto_grafico (objeto_grafico_proyecto, objeto_grafico, descripcion, grafico, ancho, alto) VALUES (
	'toba_referencia', --objeto_grafico_proyecto
	'12000138', --objeto_grafico
	NULL, --descripcion
	'bar', --grafico
	NULL, --ancho
	NULL  --alto
);
