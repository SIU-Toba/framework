
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, subclase_archivo, subclase_nombre, orden, schema, instancia_id, administrador, link_instancia, tiene_auditoria, parsea_errores, usuario, clave, base) VALUES (
	'toba_referencia', --proyecto
	'toba_referencia', --fuente_datos
	'Datos de prueba', --descripcion
	'toba_referencia', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	'extension_toba/toba_referencia_fuente_datos.php', --subclase_archivo
	'toba_referencia_fuente_datos', --subclase_nombre
	NULL, --orden
	'referencia', --schema
	'toba_referencia', --instancia_id
	NULL, --administrador
	'1', --link_instancia
	'1', --tiene_auditoria
	'0', --parsea_errores
	NULL, --usuario
	NULL, --clave
	NULL  --base
);
