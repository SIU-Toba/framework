
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, punto_montaje, subclase_archivo, subclase_nombre, orden, schema, instancia_id, administrador, link_instancia, tiene_auditoria, parsea_errores, permisos_por_tabla, usuario, clave, base) VALUES (
	'toba_referencia', --proyecto
	'fuente_gis', --fuente_datos
	'Fuente de Datos para GIS', --descripcion
	NULL, --descripcion_corta
	NULL, --fuente_datos_motor
	NULL, --host
	'12000003', --punto_montaje
	NULL, --subclase_archivo
	NULL, --subclase_nombre
	NULL, --orden
	'public', --schema
	NULL, --instancia_id
	NULL, --administrador
	NULL, --link_instancia
	'0', --tiene_auditoria
	'0', --parsea_errores
	'0', --permisos_por_tabla
	NULL, --usuario
	NULL, --clave
	NULL  --base
);
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, punto_montaje, subclase_archivo, subclase_nombre, orden, schema, instancia_id, administrador, link_instancia, tiene_auditoria, parsea_errores, permisos_por_tabla, usuario, clave, base) VALUES (
	'toba_referencia', --proyecto
	'toba_referencia', --fuente_datos
	'Datos de prueba', --descripcion
	'toba_referencia', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	NULL, --punto_montaje
	'extension_toba/toba_referencia_fuente_datos.php', --subclase_archivo
	'toba_referencia_fuente_datos', --subclase_nombre
	NULL, --orden
	'referencia', --schema
	'toba_referencia', --instancia_id
	NULL, --administrador
	'1', --link_instancia
	'1', --tiene_auditoria
	'0', --parsea_errores
	'0', --permisos_por_tabla
	NULL, --usuario
	NULL, --clave
	NULL  --base
);
