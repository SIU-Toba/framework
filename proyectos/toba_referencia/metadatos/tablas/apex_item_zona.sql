
------------------------------------------------------------
-- apex_item_zona
------------------------------------------------------------
INSERT INTO apex_item_zona (proyecto, zona, nombre, clave_editable, archivo, descripcion, consulta_archivo, consulta_clase, consulta_metodo, punto_montaje) VALUES (
	'toba_referencia', --proyecto
	'personas', --zona
	'Utilidades de personas', --nombre
	NULL, --clave_editable
	NULL, --archivo
	NULL, --descripcion
	'operaciones_simples/consultas.php', --consulta_archivo
	'consultas', --consulta_clase
	'get_persona_datos_zona', --consulta_metodo
	'12000003'  --punto_montaje
);
INSERT INTO apex_item_zona (proyecto, zona, nombre, clave_editable, archivo, descripcion, consulta_archivo, consulta_clase, consulta_metodo, punto_montaje) VALUES (
	'toba_referencia', --proyecto
	'zona_cis', --zona
	'Zona de prueba para Cis', --nombre
	NULL, --clave_editable
	NULL, --archivo
	NULL, --descripcion
	'componentes/ci/ci_wizard.php', --consulta_archivo
	'ci_wizard', --consulta_clase
	'get_info_zona', --consulta_metodo
	'12000003'  --punto_montaje
);
INSERT INTO apex_item_zona (proyecto, zona, nombre, clave_editable, archivo, descripcion, consulta_archivo, consulta_clase, consulta_metodo, punto_montaje) VALUES (
	'toba_referencia', --proyecto
	'zona_tutorial', --zona
	'Zona del Tutorial', --nombre
	NULL, --clave_editable
	'tutorial/zona_tutorial.php', --archivo
	NULL, --descripcion
	NULL, --consulta_archivo
	NULL, --consulta_clase
	NULL, --consulta_metodo
	'12000003'  --punto_montaje
);
