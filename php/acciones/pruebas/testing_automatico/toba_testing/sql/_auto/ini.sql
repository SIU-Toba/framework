-- Creo el proyecto
INSERT INTO apex_proyecto (proyecto, estilo,descripcion,descripcion_corta,listar_multiproyecto) VALUES ('toba_testing','naranja1','toba_testing','toba_testing',1);

-- Le agrego los items basicos
INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('toba_testing','','toba_testing','','1','0','browser','toba','NO','Raiz PROYECTO','','toba','0','toba','especifico');
INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('toba_testing','/autovinculo','toba_testing','','0','0','fantasma','toba','NO','Autovinculo','','toba','0','toba','especifico');
INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('toba_testing','/vinculos','toba_testing','','0','0','fantasma','toba','NO','Vinculador','','toba','0','toba','especifico');

-- Creo un grupo de acceso
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion) VALUES ('toba_testing','admin','Administrador','0','Accede a toda la funcionalidad');

-- Creo un perfil de datos
INSERT INTO apex_usuario_perfil_datos (proyecto, usuario_perfil_datos, nombre, descripcion) VALUES ('toba_testing','no','No posee','');
