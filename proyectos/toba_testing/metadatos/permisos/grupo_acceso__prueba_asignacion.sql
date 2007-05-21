
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar) VALUES (
	'toba_testing', --proyecto
	'prueba_asignacion', --usuario_grupo_acc
	'prueba_asignacion', --nombre
	'10', --nivel_acceso
	'Este grupo es para la prueba de asignación masiva de permisos a un conjunto de items. No utilizarlo en otras circunstancias.', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL  --listar
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'prueba_asignacion', --usuario_grupo_acc
	NULL, --item_id
	'/componentes'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'prueba_asignacion', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_objetos'  --item
);
--- FIN Grupo de desarrollo 
