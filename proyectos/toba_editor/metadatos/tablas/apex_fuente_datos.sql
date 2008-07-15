
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, usuario, clave, base, administrador, link_instancia, instancia_id, subclase_archivo, subclase_nombre, orden, schema) VALUES (
	'toba_editor', --proyecto
	'instancia', --fuente_datos
	'Instancia', --descripcion
	'Instancia', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	NULL, --usuario
	NULL, --clave
	NULL, --base
	NULL, --administrador
	'1', --link_instancia
	NULL, --instancia_id
	'customizacion_toba/fuente_editor.php', --subclase_archivo
	'fuente_editor', --subclase_nombre
	NULL, --orden
	NULL  --schema
);
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, usuario, clave, base, administrador, link_instancia, instancia_id, subclase_archivo, subclase_nombre, orden, schema) VALUES (
	'toba_editor', --proyecto
	'test', --fuente_datos
	'Fuente de testeo', --descripcion
	NULL, --descripcion_corta
	NULL, --fuente_datos_motor
	NULL, --host
	NULL, --usuario
	NULL, --clave
	NULL, --base
	'0,2,6', --administrador
	NULL, --link_instancia
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
SELECT * FROM ref_persona_juegos WHERE persona = 1;', --instancia_id
	NULL, --subclase_archivo
	NULL, --subclase_nombre
	NULL, --orden
	NULL  --schema
);
