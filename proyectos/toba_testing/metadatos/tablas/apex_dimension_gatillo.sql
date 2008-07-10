
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
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'10', --dimension
	'9', --gatillo
	'directo', --tipo
	'1', --orden
	'escalafon', --tabla_rel_dim
	'escalafon_1, escalafon_2', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'10', --dimension
	'10', --gatillo
	'directo', --tipo
	'2', --orden
	'categoria', --tabla_rel_dim
	'escalafon_1, escalafon_2', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'11', --dimension
	'11', --gatillo
	'directo', --tipo
	'1', --orden
	'dependencia', --tabla_rel_dim
	'dependencia', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'11', --dimension
	'12', --gatillo
	'directo', --tipo
	'2', --orden
	'persona_extra', --tabla_rel_dim
	'dependencia', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'10', --dimension
	'13', --gatillo
	'indirecto', --tipo
	'1', --orden
	'persona_extra', --tabla_rel_dim
	NULL, --columnas_rel_dim
	'categoria', --tabla_gatillo
	'cargo,persona'  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_testing', --proyecto
	'7', --dimension
	'15', --gatillo
	'indirecto', --tipo
	'1', --orden
	'ref_persona', --tabla_rel_dim
	NULL, --columnas_rel_dim
	'ref_persona_deportes', --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
--- FIN Grupo de desarrollo 0
