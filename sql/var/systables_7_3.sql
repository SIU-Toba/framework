
SELECT 	c.attnum as			num_col,
		c.attname as		columna, 
		c.attnotnull as		not_null,
		d.adsrc as			default
FROM 	pg_attribute c 
		LEFT OUTER JOIN pg_attrdef d
			ON  adrelid = c.attrelid
			AND adnum = c.attnum
WHERE 	c.attrelid = (SELECT oid FROM pg_class 
						WHERE relname = 'apex_objeto')
AND 	c.attnum > 0
ORDER BY 1;


SELECT conname, contype, conkey
FROM pg_constraint
WHERE conrelid = (SELECT oid FROM pg_class 
				WHERE relname = 'apex_objeto');



---- Acceso a las systables postgres > 7.3
--
------------------------------- Tabla 
--SELECT oid, relname, relchecks
--FROM pg_class 
--WHERE relname = 'apex_objeto';
--
------------------------------- Columnas
--SELECT 	attnum,
--		attname, 
--		attnotnull,
--		atthasdef
--FROM pg_attribute
--WHERE attrelid = (SELECT oid FROM pg_class 
--				WHERE relname = 'apex_objeto')
--AND attnum > 0;
--
------------------------------- DEFAULT de las columnas
--SELECT adnum, adsrc
--FROM pg_attrdef 
--WHERE adrelid = (SELECT oid FROM pg_class 
--				WHERE relname = 'apex_objeto')
--;--AND adnum = 2;
--
------------------------------- CONSTRAINTS (contype: p,u,f)
--SELECT conname, contype, conkey
--FROM pg_constraint
--WHERE conrelid = (SELECT oid FROM pg_class 
--				WHERE relname = 'apex_objeto');
--
