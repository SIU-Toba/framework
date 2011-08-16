
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, punto_montaje, subclase_archivo, subclase_nombre, orden, schema, instancia_id, administrador, link_instancia, tiene_auditoria, parsea_errores, permisos_por_tabla, usuario, clave, base) VALUES (
	'toba_usuarios', --proyecto
	'toba_usuarios', --fuente_datos
	'Fuente toba_usuarios', --descripcion
	'toba_usuarios', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	NULL, --punto_montaje
	'extension_toba/fuente.php', --subclase_archivo
	'fuente', --subclase_nombre
	NULL, --orden
	NULL, --schema
	'toba_usuarios', --instancia_id
	NULL, --administrador
	'1', --link_instancia
	'0', --tiene_auditoria
	'0', --parsea_errores
	'0', --permisos_por_tabla
	NULL, --usuario
	NULL, --clave
	NULL  --base
);
