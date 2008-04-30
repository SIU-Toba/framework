
------------------------------------------------------------
-- apex_dimension_gatillo
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'7', --dimension
	'5', --gatillo
	'directo', --tipo
	'1', --orden
	'ref_deportes', --tabla_rel_dim
	'id', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'7', --dimension
	'6', --gatillo
	'directo', --tipo
	'2', --orden
	'ref_persona_deportes', --tabla_rel_dim
	'deporte', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'8', --dimension
	'7', --gatillo
	'directo', --tipo
	'1', --orden
	'ref_juegos', --tabla_rel_dim
	'id', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'8', --dimension
	'8', --gatillo
	'directo', --tipo
	'2', --orden
	'ref_persona_juegos', --tabla_rel_dim
	'juego', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
--- FIN Grupo de desarrollo 0
