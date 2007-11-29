
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar) VALUES (
	'toba_referencia', --proyecto
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
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/abm'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/efs/dependencias'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/efs/ef_upload'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/mensajes'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/mensajes/vinculos'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/mensajes_notificaciones'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ci'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ci/ci_anidado'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ci/ci_wizard'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/cuadro'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ei_calendario'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ei_cuadro'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ei_filtro'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ei_formulario'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/ei_formulario_ml'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/eis'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'/objetos/formularios'  --item
);
--- FIN Grupo de desarrollo 

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1240'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'2246'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'2654'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'2656'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'2658'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3270'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3271'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3273'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3289'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3292'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3294'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3296'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3301'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3305'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3308'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3310'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3311'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3313'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3315'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3362'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3363'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3365'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3367'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'3418'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000048'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000063'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000065'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000067'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000069'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000070'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000071'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000072'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000073'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000075'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000076'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000077'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000078'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000083'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000089'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000091'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000092'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000094'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000096'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000097'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000099'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000100'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000102'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000103'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000112'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000113'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000115'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1000138'  --item
);
--- FIN Grupo de desarrollo 1

--- INICIO Grupo de desarrollo 5
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'5000003'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'5000005'  --item
);
--- FIN Grupo de desarrollo 5

--- INICIO Grupo de desarrollo 10
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'10000022'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'10000024'  --item
);
--- FIN Grupo de desarrollo 10

--- INICIO Grupo de desarrollo 
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'__raiz__'  --item
);
--- FIN Grupo de desarrollo 

------------------------------------------------------------
-- apex_permiso_grupo_acc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_permiso_grupo_acc (proyecto, usuario_grupo_acc, permiso) VALUES (
	'toba_referencia', --proyecto
	'admin', --usuario_grupo_acc
	'12'  --permiso
);
--- FIN Grupo de desarrollo 0
