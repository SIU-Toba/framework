
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	'Administrador', --nombre
	'0', --nivel_acceso
	'Accede a toda la funcionalidad', --descripcion
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
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/inicio'  --item
);
--- FIN Grupo de desarrollo 

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3424'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3426'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3428'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3430'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3432'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3438'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3443'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3445'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3447'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3448'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000195'  --item
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_permiso_grupo_acc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_permiso_grupo_acc (proyecto, usuario_grupo_acc, permiso) VALUES (
	'toba_usuarios', --proyecto
	'admin', --usuario_grupo_acc
	'14'  --permiso
);
--- FIN Grupo de desarrollo 0
