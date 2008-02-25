
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, usuario, clave, base, administrador, link_instancia, instancia_id, subclase_archivo, subclase_nombre, orden) VALUES (
	'toba_usuarios', --proyecto
	'toba_usuarios', --fuente_datos
	'Fuente toba_usuarios', --descripcion
	'toba_usuarios', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	NULL, --usuario
	NULL, --clave
	NULL, --base
	NULL, --administrador
	'1', --link_instancia
	'toba_usuarios', --instancia_id
	'customizacion_toba/fuente.php', --subclase_archivo
	'fuente', --subclase_nombre
	NULL  --orden
);
