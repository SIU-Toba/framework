
------------------------------------------------------------
-- apex_servicio_web
------------------------------------------------------------
INSERT INTO apex_servicio_web (proyecto, servicio_web, descripcion, param_to, param_wsa) VALUES (
	'toba_referencia', --proyecto
	'certficado_firmado_codigo', --servicio_web
	NULL, --descripcion
	'http://desarrollos.siu.edu.ar/toba_referencia_trunk/servicios.php/serv_certificado_firmado', --param_to
	'0'  --param_wsa
);
INSERT INTO apex_servicio_web (proyecto, servicio_web, descripcion, param_to, param_wsa) VALUES (
	'toba_referencia', --proyecto
	'certificado', --servicio_web
	NULL, --descripcion
	'http://desarrollos.siu.edu.ar/toba_referencia_trunk/servicios.php/serv_certificado', --param_to
	'1'  --param_wsa
);
INSERT INTO apex_servicio_web (proyecto, servicio_web, descripcion, param_to, param_wsa) VALUES (
	'toba_referencia', --proyecto
	'certificado_firmado_configuracion', --servicio_web
	NULL, --descripcion
	'http://desarrollos.siu.edu.ar/toba_referencia_trunk/servicios.php/serv_certificado_firmado', --param_to
	'1'  --param_wsa
);
INSERT INTO apex_servicio_web (proyecto, servicio_web, descripcion, param_to, param_wsa) VALUES (
	'toba_referencia', --proyecto
	'sin_seguridad', --servicio_web
	NULL, --descripcion
	'http://desarrollos.siu.edu.ar/toba_referencia_trunk/servicios.php/serv_sin_seguridad', --param_to
	'0'  --param_wsa
);
