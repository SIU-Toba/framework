
---------------------------------------------------------------------------------
-- 2) Los que tenian tabs, tienen que definir ese tipo de navegacion
---------------------------------------------------------------------------------

UPDATE apex_objeto_mt_me 
SET tipo_navegacion = 'tab_h' 
FROM apex_objeto
WHERE apex_objeto.clase = 'objeto_ci_me_tab'
AND	apex_objeto.proyecto = apex_objeto_mt_me.objeto_mt_me_proyecto
AND	apex_objeto.objeto = apex_objeto_mt_me.objeto_mt_me;

-------------------------------------------------------------------------------
-- 3) Todas las clases CI creadas hasta ahora que no eran ABMs, usaban CNs
-------------------------------------------------------------------------------

UPDATE ONLY apex_objeto
SET clase = 'ci_cn'
--SELECT 	i.actividad_patron, o.clase
FROM 	apex_item_objeto io,
--	apex_objeto o,
	apex_item i
WHERE	apex_objeto.objeto = io.objeto
AND	apex_objeto.proyecto = io.proyecto
AND	i.item = io.item
--AND	i.actividad_patron <> 'generico_ci_cn'
AND	apex_objeto.clase IN ('objeto_ci', 'objeto_ci_me', 'objeto_ci_me_tab');
	
---------------------------------------------------------------------------------
-- 4) Actualizo los parametros de los ITEMs
---------------------------------------------------------------------------------

UPDATE apex_item SET parametro_a = 'ci_cn' WHERE parametro_a IN ('objeto_ci_me','objeto_ci_me_tab');
UPDATE apex_item SET parametro_b = 'ci_cn' WHERE parametro_b IN ('objeto_ci_me','objeto_ci_me_tab');
UPDATE apex_item SET parametro_c = 'ci_cn' WHERE parametro_c IN ('objeto_ci_me','objeto_ci_me_tab');

---------------------------------------------------------------------------------
