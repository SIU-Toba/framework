
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar) VALUES (
	'toba_testing', --proyecto
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
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/componentes'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/prueba_efs'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_arbol_items/rama_profunda/ia'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_arbol_items/rama_profunda/r0/i0b'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_arbol_items/rama_profunda/r0/r01'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_arbol_items/rama_profunda/r0/r02/r021'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_arbol_items/rama_profunda/r0/r02/r021/i021a'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_item/ejemplo_accion'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_item/ejemplo_buffer'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_item/ejemplo_patron'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_item/item_con_dos_grupos'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_item/item_popup'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_objetos'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_objetos/ci_abm'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas_objetos/objeto_formulario_ml'  --item
);
--- FIN Grupo de desarrollo 

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1641'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3277'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3369'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000010'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000015'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000016'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000017'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000018'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000022'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000034'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000035'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000038'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000041'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000046'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000047'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000105'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000107'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000116'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_testing', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000165'  --item
);
--- FIN Grupo de desarrollo 1
