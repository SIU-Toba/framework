SELECT relname FROM pg_class WHERE relkind = 'r' AND relowner <> 1 ORDER BY 1;

SELECT attname
FROM pg_attribute
WHERE attrelid = (	SELECT relfilenode FROM pg_class
							WHERE relname = 'apex_item'
							ORDER BY 1 )
AND attstattarget=10;

