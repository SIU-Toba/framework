
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
	'SELECT * FROM ref_deportes;
SELECT * FROM ref_deportes WHERE id < 5;
SELECT * FROM ref_deportes d, ref_persona_deportes p 
WHERE p.deporte = d.id;
SELECT * FROM ref_deportes d, ref_persona_deportes 
WHERE ref_persona_deportes.deporte = d.id;
SELECT * FROM ref_persona_deportes, ref_deportes d
WHERE ref_persona_deportes.deporte = d.id;
SELECT * FROM ref_juegos ORDER BY id;
SELECT * FROM ref_deportes WHERE id > 2;
SELECT * FROM ref_persona_deportes WHERE persona = 1;
SELECT * FROM ref_persona_juegos;
SELECT * FROM ref_persona_juegos WHERE persona = 1;', --usuario
	'0,1,2,3,4,5,6,7,8,9', --clave
	'a:9:{s:12:\\\"perfil_datos\\\";s:1:\\\"5\\\";s:12:\\\"sql_original\\\";s:1:\\\"1\\\";s:19:\\\"omitir_no_afectados\\\";s:1:\\\"1\\\";s:7:\\\"detalle\\\";N;s:16:\\\"info_dimensiones\\\";s:1:\\\"0\\\";s:9:\\\"sql_where\\\";s:1:\\\"0\\\";s:14:\\\"sql_modificado\\\";s:1:\\\"1\\\";s:11:\\\"datos_filas\\\";s:1:\\\"1\\\";s:12:\\\"datos_listar\\\";s:1:\\\"0\\\";}'  --base
);
