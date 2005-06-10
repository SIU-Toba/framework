----------------------------------------------------------------------------------
-- 1) Este query genera los INSERTS que permiten agregar pantallas a todos los CI de una pantalla
----------------------------------------------------------------------------------
  
UPDATE apex_objeto_mt_me SET ev_procesar = 0 WHERE ev_procesar IS NULL;
UPDATE apex_objeto_mt_me SET ev_cancelar = 0 WHERE ev_cancelar IS NULL;
UPDATE apex_objeto_mt_me SET objetos = '' WHERE objetos IS NULL;

SELECT 	'INSERT INTO apex_objeto_mt_me_etapa (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar, ev_cancelar, objetos, posicion ) VALUES (\''  || trim(o.proyecto) || '\',\'' || trim(o.objeto) || '\',\'' || trim(o_ci.ev_procesar) || '\',\'' || trim(o_ci.ev_cancelar) || '\',\'' || trim(o_ci.objetos) || '\',0);' as sql_a_ejecutar
--		o.proyecto, 
--		o.objeto,
--		o_ci.ev_procesar,
--		o_ci.ev_cancelar,
--		o_ci.objetos
FROM 	apex_objeto 			o,
		apex_objeto_mt_me		o_ci
WHERE 	o.proyecto = o_ci.objeto_mt_me_proyecto
AND		o.objeto = o_ci.objeto_mt_me
AND		o.proyecto <> 'toba'
AND		o.clase = 'objeto_ci';

