
------------------------------------------------------------
-- apex_relacion_tablas
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos', --fuente_datos
	'toba_testing', --proyecto
	'6', --relacion_tablas
	'cargo', --tabla_1
	'categoria_1,categoria_2', --tabla_1_cols
	'categoria', --tabla_2
	'categoria_1,categoria_2'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos', --fuente_datos
	'toba_testing', --proyecto
	'7', --relacion_tablas
	'persona', --tabla_1
	'persona', --tabla_1_cols
	'cargo', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos', --fuente_datos
	'toba_testing', --proyecto
	'10', --relacion_tablas
	'persona', --tabla_1
	'persona', --tabla_1_cols
	'persona_extra', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos', --fuente_datos
	'toba_testing', --proyecto
	'19', --relacion_tablas
	'categoria', --tabla_1
	'escalafon_1,escalafon_2', --tabla_1_cols
	'escalafon', --tabla_2
	'escalafon_1,escalafon_2'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos', --fuente_datos
	'toba_testing', --proyecto
	'20', --relacion_tablas
	'dependencia', --tabla_1
	'dependencia', --tabla_1_cols
	'cargo', --tabla_2
	'dependencia'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'perfil_datos', --fuente_datos
	'toba_testing', --proyecto
	'21', --relacion_tablas
	'persona_extra', --tabla_1
	'dependencia', --tabla_1_cols
	'dependencia', --tabla_2
	'dependencia'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'referencia', --fuente_datos
	'toba_testing', --proyecto
	'22', --relacion_tablas
	'ref_persona', --tabla_1
	'id', --tabla_1_cols
	'ref_persona_deportes', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'referencia', --fuente_datos
	'toba_testing', --proyecto
	'23', --relacion_tablas
	'ref_persona', --tabla_1
	'id', --tabla_1_cols
	'ref_persona_juegos', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'referencia', --fuente_datos
	'toba_testing', --proyecto
	'24', --relacion_tablas
	'ref_persona', --tabla_1
	'id', --tabla_1_cols
	'log_persona', --tabla_2
	'persona'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'referencia', --fuente_datos
	'toba_testing', --proyecto
	'25', --relacion_tablas
	'ref_juegos', --tabla_1
	'id', --tabla_1_cols
	'ref_persona_juegos', --tabla_2
	'juego'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'referencia', --fuente_datos
	'toba_testing', --proyecto
	'26', --relacion_tablas
	'ref_deportes', --tabla_1
	'id', --tabla_1_cols
	'ref_persona_deportes', --tabla_2
	'deporte'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'referencia', --fuente_datos
	'toba_testing', --proyecto
	'27', --relacion_tablas
	'ref_juegos', --tabla_1
	'id', --tabla_1_cols
	'ref_juegos_oferta', --tabla_2
	'juego'  --tabla_2_cols
);
INSERT INTO apex_relacion_tablas (fuente_datos_proyecto, fuente_datos, proyecto, relacion_tablas, tabla_1, tabla_1_cols, tabla_2, tabla_2_cols) VALUES (
	'toba_testing', --fuente_datos_proyecto
	'referencia', --fuente_datos
	'toba_testing', --proyecto
	'28', --relacion_tablas
	'ref_juegos', --tabla_1
	'id', --tabla_1_cols
	'log_juegos', --tabla_2
	'juego'  --tabla_2_cols
);
--- FIN Grupo de desarrollo 0
