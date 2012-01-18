
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, punto_montaje, subclase_archivo, subclase_nombre, orden, schema, instancia_id, administrador, link_instancia, tiene_auditoria, parsea_errores, permisos_por_tabla, usuario, clave, base) VALUES (
	'toba_editor', --proyecto
	'instancia', --fuente_datos
	'Instancia', --descripcion
	'Instancia', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	NULL, --punto_montaje
	'customizacion_toba/fuente_editor.php', --subclase_archivo
	'fuente_editor', --subclase_nombre
	NULL, --orden
	NULL, --schema
	NULL, --instancia_id
	NULL, --administrador
	'1', --link_instancia
	'0', --tiene_auditoria
	'0', --parsea_errores
	'0', --permisos_por_tabla
	NULL, --usuario
	NULL, --clave
	NULL  --base
);
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, punto_montaje, subclase_archivo, subclase_nombre, orden, schema, instancia_id, administrador, link_instancia, tiene_auditoria, parsea_errores, permisos_por_tabla, usuario, clave, base) VALUES (
	'toba_editor', --proyecto
	'test', --fuente_datos
	'Fuente de testeo', --descripcion
	NULL, --descripcion_corta
	NULL, --fuente_datos_motor
	NULL, --host
	NULL, --punto_montaje
	NULL, --subclase_archivo
	NULL, --subclase_nombre
	NULL, --orden
	NULL, --schema
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
