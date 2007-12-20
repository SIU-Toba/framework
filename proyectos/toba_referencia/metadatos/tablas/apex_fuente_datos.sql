
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, usuario, clave, base, administrador, link_instancia, instancia_id, subclase_archivo, subclase_nombre, orden) VALUES (
	'toba_referencia', --proyecto
	'toba_referencia', --fuente_datos
	'Datos de prueba', --descripcion
	'toba_referencia', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	NULL, --usuario
	NULL, --clave
	NULL, --base
	NULL, --administrador
	'1', --link_instancia
	'toba_referencia', --instancia_id
	'extension_toba/toba_referencia_fuente_datos.php', --subclase_archivo
	'toba_referencia_fuente_datos', --subclase_nombre
	NULL  --orden
);
