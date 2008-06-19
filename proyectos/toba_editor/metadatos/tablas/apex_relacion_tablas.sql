
------------------------------------------------------------
-- apex_relacion_tablas
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_editor', --fuente_datos_proyecto
	'test', --fuente_datos
	'toba_editor', --proyecto
	'5', --relacion_tablas
	'ref_juegos', --tabla_1
	'id', --tabla_1_cols
	'ref_juegos_oferta', --tabla_2
	'juego'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_editor', --fuente_datos_proyecto
	'test', --fuente_datos
	'toba_editor', --proyecto
	'13', --relacion_tablas
	'ref_persona', --tabla_1
	'id', --tabla_1_cols
	'ref_persona_deportes', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_editor', --fuente_datos_proyecto
	'test', --fuente_datos
	'toba_editor', --proyecto
	'14', --relacion_tablas
	'ref_persona', --tabla_1
	'id', --tabla_1_cols
	'ref_persona_juegos', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_editor', --fuente_datos_proyecto
	'test', --fuente_datos
	'toba_editor', --proyecto
	'15', --relacion_tablas
	'ref_persona_juegos', --tabla_1
	'juego', --tabla_1_cols
	'ref_juegos', --tabla_2
	'id'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_editor', --fuente_datos_proyecto
	'test', --fuente_datos
	'toba_editor', --proyecto
	'16', --relacion_tablas
	'ref_juegos', --tabla_1
	'id', --tabla_1_cols
	'log_juegos', --tabla_2
	'juego'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_editor', --fuente_datos_proyecto
	'test', --fuente_datos
	'toba_editor', --proyecto
	'17', --relacion_tablas
	'ref_persona', --tabla_1
	'id', --tabla_1_cols
	'log_persona', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_editor', --fuente_datos_proyecto
	'test', --fuente_datos
	'toba_editor', --proyecto
	'18', --relacion_tablas
	'ref_deportes', --tabla_1
	'id', --tabla_1_cols
	'ref_persona_deportes', --tabla_2
	'deporte'  --tabla_2_cols
);
--- FIN Grupo de desarrollo 0
