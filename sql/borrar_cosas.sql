-----------------------------------------
-- BORRAR objetos de una CLASE
-----------------------------------------

------------ BORRAR ASOCIACION -------------------------------

DELETE
--SELECT item, objeto
FROM apex_item_objeto 
WHERE proyecto = 'comechingones'
AND objeto IN (
	SELECT ox.objeto
	FROM apex_item_objeto ix,
	apex_objeto ox
	WHERE ix.objeto = ox.objeto
	AND ix.proyecto = 'comechingones'
	AND ox.clase = 'cn_comechingones');

------------ PROTO -------------------------------

DELETE
--SELECT objeto
FROM apex_objeto_proto
WHERE objeto_proyecto = 'comechingones'
AND objeto IN (
	SELECT ox.objeto
	FROM apex_objeto_proto ix,
	apex_objeto ox
	WHERE ix.objeto = ox.objeto
	AND ix.objeto_proyecto = 'comechingones'
	AND ox.clase = 'cn_comechingones');


------------ PROTO -------------------------------

DELETE
--SELECT objeto
FROM apex_objeto_proto_metodo
WHERE objeto_proyecto = 'comechingones'
AND objeto IN (
	SELECT ox.objeto
	FROM apex_objeto_proto_metodo ix,
	apex_objeto ox
	WHERE ix.objeto = ox.objeto
	AND ix.objeto_proyecto = 'comechingones'
	AND ox.clase = 'cn_comechingones');

----------- BORRAR OBJETOS -----------------------------------

DELETE 
--SELECT objeto, nombre
FROM apex_objeto 
WHERE proyecto = 'comechingones' 
AND clase = 'cn_comechingones';

--------------------------------------------------------------