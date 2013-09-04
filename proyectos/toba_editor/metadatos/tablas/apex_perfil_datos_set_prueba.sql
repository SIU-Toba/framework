
------------------------------------------------------------
-- apex_perfil_datos_set_prueba
------------------------------------------------------------
INSERT INTO apex_perfil_datos_set_prueba (proyecto, fuente_datos, lote, seleccionados, parametros) VALUES (
	'toba_editor', --proyecto
	'test', --fuente_datos
	'SELECT * FROM ref_deportes;
SELECT * FROM ref_deportes WHERE id < 5;
SELECT * FROM ref_deportes d, ref_persona_deportes p 
WHERE p.deporte = d.id;
SELECT * FROM ref_deportes d, ref_persona_deportes 
WHERE ref_persona_deportes.deporte = d.id;
SELECT * FROM ref_persona_deportes, ref_deportes d
WHERE ref_persona_deportes.deporte = d.id;
SELECT * FROM ref_juegos ORDER BY id;
SELECT * FROM ref_deportes WHERE id > 2;
SELECT * FROM ref_persona_deportes WHERE persona = 1;
SELECT * FROM ref_persona_juegos;
SELECT * FROM ref_persona_juegos WHERE persona = 1;', --lote
	'0,1,2,3,4,5,6,7,8,9', --seleccionados
	'a:9:{s:12:\"perfil_datos\";s:1:\"5\";s:12:\"sql_original\";s:1:\"1\";s:19:\"omitir_no_afectados\";s:1:\"1\";s:7:\"detalle\";N;s:16:\"info_dimensiones\";s:1:\"0\";s:9:\"sql_where\";s:1:\"0\";s:14:\"sql_modificado\";s:1:\"1\";s:11:\"datos_filas\";s:1:\"1\";s:12:\"datos_listar\";s:1:\"0\";}'  --parametros
);
