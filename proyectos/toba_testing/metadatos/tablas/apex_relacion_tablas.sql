
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
--- FIN Grupo de desarrollo 0
