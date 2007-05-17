
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, usuario, clave, base, administrador, link_instancia, instancia_id, subclase_archivo, subclase_nombre, orden) VALUES (
	'toba_instancia', --proyecto
	'toba_instancia', --fuente_datos
	'Fuente toba_instancia', --descripcion
	'toba_instancia', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	NULL, --usuario
	NULL, --clave
	NULL, --base
	NULL, --administrador
	'1', --link_instancia
	NULL, --instancia_id
	'customizacion_toba/fuente.php', --subclase_archivo
	'fuente', --subclase_nombre
	NULL  --orden
);
