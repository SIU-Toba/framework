
---- Borro permisos
--DELETE FROM apex_usuario_grupo_acc_item WHERE proyecto = 'sipefco';
--
---- Cambio el ID de los items para la asociacion de objetos
--
--UPDATE "pg_class" SET "reltriggers" = 0 WHERE "relname" = 'apex_item_objeto';
--
--UPDATE apex_item_objeto
--SET item_id = x.item_id
--FROM apex_item x
--WHERE apex_item_objeto.item = x.item
--AND apex_item_objeto.proyecto = x.proyecto;
--
--UPDATE apex_item_objeto SET item = item_id WHERE proyecto = 'sipefco';
--

--UPDATE pg_class SET reltriggers = (SELECT count(*) FROM pg_trigger where pg_class.oid = tgrelid) WHERE relname = 'apex_item_objeto';


--UPDATE "pg_class" SET "reltriggers" = 0 WHERE "relname" = 'apex_item';    
--
--UPDATE apex_item
--SET padre_id = x.item_id
--FROM apex_item x
--WHERE apex_item.padre = x.item
--AND apex_item.padre_proyecto = x.proyecto;


--UPDATE apex_item SET padre = padre_id WHERE proyecto = 'sipefco';
--UPDATE apex_item SET padre = '' WHERE padre = 273 AND proyecto = 'sipefco';

UPDATE apex_item SET item = item_id WHERE proyecto = 'sipefco' AND item NOT IN ('/autovinculo','/vinculos');
UPDATE apex_item SET item = '' WHERE item = 273 AND proyecto = 'sipefco';
