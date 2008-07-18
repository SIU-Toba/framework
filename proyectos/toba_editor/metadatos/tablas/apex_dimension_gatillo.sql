
------------------------------------------------------------
-- apex_dimension_gatillo
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_editor', --proyecto
	'9', --dimension
	'1', --gatillo
	'directo', --tipo
	'1', --orden
	'ref_deportes', --tabla_rel_dim
	'id', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_editor', --proyecto
	'9', --dimension
	'4', --gatillo
	'directo', --tipo
	'2', --orden
	'ref_persona_deportes', --tabla_rel_dim
	'deporte', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_editor', --proyecto
	'9', --dimension
	'14', --gatillo
	'indirecto', --tipo
	'1', --orden
	'ref_persona', --tabla_rel_dim
	NULL, --columnas_rel_dim
	'ref_persona_deportes', --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_editor', --proyecto
	'12', --dimension
	'16', --gatillo
	'directo', --tipo
	'1', --orden
	'ref_juegos', --tabla_rel_dim
	'id', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_editor', --proyecto
	'12', --dimension
	'17', --gatillo
	'directo', --tipo
	'2', --orden
	'ref_juegos_oferta', --tabla_rel_dim
	'juego', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
INSERT INTO apex_dimension_gatillo (proyecto, dimension, gatillo, tipo, orden, tabla_rel_dim, columnas_rel_dim, tabla_gatillo, ruta_tabla_rel_dim) VALUES (
	'toba_editor', --proyecto
	'12', --dimension
	'18', --gatillo
	'directo', --tipo
	'3', --orden
	'ref_persona_juegos', --tabla_rel_dim
	'juego', --columnas_rel_dim
	NULL, --tabla_gatillo
	NULL  --ruta_tabla_rel_dim
);
--- FIN Grupo de desarrollo 0
