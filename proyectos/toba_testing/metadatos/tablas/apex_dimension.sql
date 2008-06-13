
------------------------------------------------------------
-- apex_dimension
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_dimension (proyecto, dimension, nombre, descripcion, schema, tabla, col_id, col_desc, col_desc_separador, multitabla_col_tabla, multitabla_id_tabla, fuente_datos_proyecto, fuente_datos) VALUES (
	'toba_testing', --proyecto
	'7', --dimension
	'deportes', --nombre
	NULL, --descripcion
	NULL, --schema
	'ref_deportes', --tabla
	'id', --col_id
	'nombre', --col_desc
	NULL, --col_desc_separador
	NULL, --multitabla_col_tabla
	NULL, --multitabla_id_tabla
	'toba_testing', --fuente_datos_proyecto
	'referencia'  --fuente_datos
);
INSERT INTO apex_dimension (proyecto, dimension, nombre, descripcion, schema, tabla, col_id, col_desc, col_desc_separador, multitabla_col_tabla, multitabla_id_tabla, fuente_datos_proyecto, fuente_datos) VALUES (
	'toba_testing', --proyecto
	'8', --dimension
	'juegos', --nombre
	NULL, --descripcion
	NULL, --schema
	'ref_juegos', --tabla
	'id', --col_id
	'nombre', --col_desc
	NULL, --col_desc_separador
	NULL, --multitabla_col_tabla
	NULL, --multitabla_id_tabla
	'toba_testing', --fuente_datos_proyecto
	'referencia'  --fuente_datos
);
INSERT INTO apex_dimension (proyecto, dimension, nombre, descripcion, schema, tabla, col_id, col_desc, col_desc_separador, multitabla_col_tabla, multitabla_id_tabla, fuente_datos_proyecto, fuente_datos) VALUES (
	'toba_testing', --proyecto
	'10', --dimension
	'escalafon', --nombre
	NULL, --descripcion
	NULL, --schema
	'escalafon', --tabla
	'escalafon_1, escalafon_2', --col_id
	'descripcion', --col_desc
	NULL, --col_desc_separador
	NULL, --multitabla_col_tabla
	NULL, --multitabla_id_tabla
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos'  --fuente_datos
);
INSERT INTO apex_dimension (proyecto, dimension, nombre, descripcion, schema, tabla, col_id, col_desc, col_desc_separador, multitabla_col_tabla, multitabla_id_tabla, fuente_datos_proyecto, fuente_datos) VALUES (
	'toba_testing', --proyecto
	'11', --dimension
	'dependencia', --nombre
	NULL, --descripcion
	NULL, --schema
	'dependencia', --tabla
	'dependencia', --col_id
	'descripcion', --col_desc
	NULL, --col_desc_separador
	NULL, --multitabla_col_tabla
	NULL, --multitabla_id_tabla
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos'  --fuente_datos
);
--- FIN Grupo de desarrollo 0
