
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	'Documentador', --nombre
	'10', --nivel_acceso
	'Usuario encargado de documentar el sistema', --descripcion
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
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/acceso'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/apex/elementos/ef'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/apex/elementos/error'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/apex/elementos/observaciones_solicitud'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/apex/elementos/pagina_tipo'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/apex/elementos/zona'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/crear'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/ci'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/db_registros'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/db_tablas'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/ei_archivos'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/ei_cuadro'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/ei_filtro'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/ei_formulario'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/editores/ei_formulario_ml'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/objetos_toba/selector_archivo'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/proyectos/organizador'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/proyectos/propiedades'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/usuarios'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/usuarios/grupo'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/admin/utilidades'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/basicos/cronometro'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/configuracion'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/inicio'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/items'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas/testing_automatico_consola'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'/pruebas/testing_automatico_web'  --item
);
--- FIN Grupo de desarrollo 

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1240'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1241'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1242'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'2045'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'2447'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'2865'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'3276'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'3278'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'3280'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'3287'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'3288'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'3316'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1000003'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1000020'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1000021'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1000043'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1000045'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'1000058'  --item
);
--- FIN Grupo de desarrollo 1

--- INICIO Grupo de desarrollo 
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'toba_editor', --proyecto
	'documentador', --usuario_grupo_acc
	NULL, --item_id
	'__raiz__'  --item
);
--- FIN Grupo de desarrollo 
