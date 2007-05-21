
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar) VALUES (
	'toba_testing', --proyecto
	'plano', --usuario_grupo_acc
	'Plano', --nombre
	'0', --nivel_acceso
	NULL, --descripcion
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
	'plano', --usuario_grupo_acc
	NULL, --item_id
	'/componentes'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'plano', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_item'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'plano', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_objetos'  --item
);
--- FIN Grupo de desarrollo 

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'plano', --usuario_grupo_acc
	NULL, --item_id
	'1000022'  --item
);
--- FIN Grupo de desarrollo 1

--- INICIO Grupo de desarrollo 
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'plano', --usuario_grupo_acc
	NULL, --item_id
	'__raiz__'  --item
);
--- FIN Grupo de desarrollo 
