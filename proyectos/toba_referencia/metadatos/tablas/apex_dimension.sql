
------------------------------------------------------------
-- apex_dimension
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_dimension (proyecto, dimension, nombre, descripcion, schema, tabla, col_id, col_desc, col_desc_separador, multitabla_col_tabla, multitabla_id_tabla, fuente_datos_proyecto, fuente_datos) VALUES (
	'toba_referencia', --proyecto
	'1000001', --dimension
	'persona', --nombre
	NULL, --descripcion
	NULL, --schema
	'ref_persona', --tabla
	'id', --col_id
	'nombre', --col_desc
	NULL, --col_desc_separador
	NULL, --multitabla_col_tabla
	NULL, --multitabla_id_tabla
	'toba_referencia', --fuente_datos_proyecto
	'toba_referencia'  --fuente_datos
);
--- FIN Grupo de desarrollo 1
