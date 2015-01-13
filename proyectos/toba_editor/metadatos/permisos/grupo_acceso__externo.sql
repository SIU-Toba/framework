
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'toba_editor', --proyecto
	'externo', --usuario_grupo_acc
	'Externo', --nombre
	'20', --nivel_acceso
	'', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'1', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'externo', --usuario_grupo_acc
	NULL, --item_id
	'3280'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'externo', --usuario_grupo_acc
	NULL, --item_id
	'1000242'  --item
);
--- FIN Grupo de desarrollo 1
