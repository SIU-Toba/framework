
------------------------------------------------------------
-- apex_perfil_datos_set_prueba
------------------------------------------------------------
INSERT INTO apex_perfil_datos_set_prueba (proyecto, fuente_datos, lote, seleccionados, parametros) VALUES (
	'toba_testing', --proyecto
	'perfil_datos', --fuente_datos
	'SELECT categoria_1, categoria_2, c.descripcion
									FROM 	escalafon e,
											categoria c
									WHERE c.escalafon_1 = e.escalafon_1
									AND c.escalafon_2 = e.escalafon_2;', --lote
	'0', --seleccionados
	'a:9:{s:12:\"perfil_datos\";s:1:\"2\";s:12:\"sql_original\";s:1:\"0\";s:19:\"omitir_no_afectados\";s:1:\"0\";s:7:\"detalle\";N;s:16:\"info_dimensiones\";s:1:\"1\";s:9:\"sql_where\";s:1:\"0\";s:14:\"sql_modificado\";s:1:\"1\";s:11:\"datos_filas\";s:1:\"0\";s:12:\"datos_listar\";s:1:\"1\";}'  --parametros
);
