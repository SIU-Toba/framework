--UPDATE "pg_class" SET "reltriggers" = 0 WHERE "relname" = 'apex_item_objeto';
--UPDATE "pg_class" SET "reltriggers" = 0 WHERE "relname" = 'apex_item';                               

--UPDATE pg_class SET reltriggers = (SELECT count(*) FROM pg_trigger where pg_class.oid = tgrelid) WHERE relname = 'apex_objeto';



--SELECT * FROM apex_mod_datos_tabla_columna WHERE columna = 'fuente_datos';




--UPDATE "pg_class" SET "reltriggers" = 0 WHERE "relname" = 'apex_dimension';
--UPDATE pg_class SET reltriggers = (SELECT count(*) FROM pg_trigger where pg_class.oid = tgrelid) WHERE relname = 'apex_dimension';

--UPDATE "pg_class" SET "reltriggers" = 0 WHERE "relname" = 'apex_fuente_datos';
--UPDATE pg_class SET reltriggers = (SELECT count(*) FROM pg_trigger where pg_class.oid = tgrelid) WHERE relname = 'apex_fuente_datos';


--UPDATE apex_fuente_datos SET fuente_datos = 'instancia' WHERE fuente_datos = 'toba';
--UPDATE apex_objeto SET fuente_datos = 'instancia' WHERE fuente_datos = 'toba';
--UPDATE apex_dimension SET fuente_datos = 'instancia' WHERE fuente_datos = 'toba';