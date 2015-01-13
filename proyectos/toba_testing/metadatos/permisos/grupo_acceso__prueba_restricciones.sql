
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'toba_testing', --proyecto
	'prueba_restricciones', --usuario_grupo_acc
	'Prueba de restricciones', --nombre
	NULL, --nivel_acceso
	NULL, --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'1', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_grupo_acc_restriccion_funcional
------------------------------------------------------------
INSERT INTO apex_grupo_acc_restriccion_funcional (proyecto, usuario_grupo_acc, restriccion_funcional) VALUES (
	'toba_testing', --proyecto
	'prueba_restricciones', --usuario_grupo_acc
	'30000001'  --restriccion_funcional
);
