-----------  apex_usuario  ------------------------

INSERT INTO apex_usuario (usuario, clave, nombre, usuario_tipodoc, pre, ciu, suf, email, telefono, vencimiento, dias, hora_entrada, hora_salida, ip_permitida, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, autentificacion) VALUES ('toba',md5('cmmcero'),'SIU - Toba',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL, 'md5');

-----------  apex_usuario_proyecto  ------------------------

INSERT INTO apex_usuario_proyecto (proyecto, usuario, usuario_grupo_acc, usuario_perfil_datos) VALUES ('toba','toba','admin','no');

