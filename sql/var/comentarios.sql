-- Me quedaron algunas cosistas...
-- 
--    Tanto para los mensajes de error como para el objeto cuadro que extendí para mostrar el contenido de cualquier tabla, se utilizan los comentarios que se le pueden agregar a las tablas y campos en postgresql. Básicamente las sentencias para agregar los comentarios son:
-- 
--     COMMENT ON TABLE tabla_x IS 'comentario'; 
--     COMMENT ON COLUMN tabla_x.campo_x IS 'comentario'; 
-- 
--   Para automatizar un poco la generación de comentarios de todas las tablas y campos de una base de datos, construí unas consultas que transforman un poco los nombres de campos y tablas, y retornan una serie de comandos sql como los de arriba para guardarlos en un archivo y luego editarlos bien. Ahí van ...
-- 
-- PARA LAS TABLAS:

SELECT 'COMMENT ON TABLE ' || c.relname || ' IS \'' || 
       COALESCE(obj_description(c.oid, 'pg_class'), 
           replace( 
               replace(
                   initcap(
                       replace(  
                           replace(
                                      replace(c.relname, 'mca_', ''),
                                      'con_', ''  
                                   ),
                                   '_', ' '
                               )
                           ),
                           ' De ', ' de ' 
                       ),
                       ' Por ', ' por '
                   )
               ) || '\';' as comentario
FROM pg_tables t, pg_class c
WHERE t.tablename NOT LIKE 'pg_%'
AND t.tablename NOT LIKE 'sql_%'
AND c.relkind = 'r'
AND c.relname = t.tablename
ORDER BY tablename;

--PARA LOS CAMPOS DE CADA TABLA:

SELECT 'COMMENT ON COLUMN ' || c.relname || '.' || a.attname || ' IS \'' || 
       COALESCE(col_description(c.oid, a.attnum), 
           replace( 
               replace(
                   initcap(
                       replace(  
                           replace(
                                      a.attname, 'cod_', 'Cod. '
                                   ),
                                   '_', ' '
                               )
                           ),
                           ' De ', ' de ' 
                       ),
                       ' Por ', ' por '
                   )
               ) || '\';' as comentario
FROM pg_class c, pg_attribute a, pg_type t
WHERE c.relkind = 'r'
AND c.relname NOT LIKE 'pg_%'
AND c.relname NOT LIKE 'sql_%'
AND a.attnum > 0
AND a.atttypid = t.oid
AND a.attrelid = c.oid
ORDER BY c.relname, a.attnum;