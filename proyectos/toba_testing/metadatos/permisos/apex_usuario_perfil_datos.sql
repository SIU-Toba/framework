
------------------------------------------------------------
-- apex_usuario_perfil_datos
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_perfil_datos (proyecto, usuario_perfil_datos, nombre, descripcion, listar) VALUES (
	'toba_testing', --proyecto
	'2', --usuario_perfil_datos
	'Perfil A', --nombre
	'Escalafones A,B - Dependencias A,B', --descripcion
	NULL  --listar
);
INSERT INTO apex_usuario_perfil_datos (proyecto, usuario_perfil_datos, nombre, descripcion, listar) VALUES (
	'toba_testing', --proyecto
	'3', --usuario_perfil_datos
	'Perfil B', --nombre
	'Escalafon y dependencia D', --descripcion
	NULL  --listar
);
INSERT INTO apex_usuario_perfil_datos (proyecto, usuario_perfil_datos, nombre, descripcion, listar) VALUES (
	'toba_testing', --proyecto
	'4', --usuario_perfil_datos
	'Perfil C (multi FUENTE)', --nombre
	'Posee elementos de dos fuentes', --descripcion
	NULL  --listar
);
--- FIN Grupo de desarrollo 0
