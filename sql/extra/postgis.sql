-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
-- 
-- $Id: postgis.sql,v 1.1 2004/12/05 00:16:34 cvs Exp $
--
-- PostGIS - Spatial Types for PostgreSQL
-- http://postgis.refractions.net
-- Copyright 2001-2003 Refractions Research Inc.
--
-- This is free software; you can redistribute and/or modify it under
-- the terms of the GNU General Public Licence. See the COPYING file.
--  
-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -





BEGIN TRANSACTION;

-- You might have to define the PL/PgSQL language usually done with the
-- changelang script.

-- Here's some hokey code to test to see if PL/PgSQL is installed
-- if it is, you get a message "PL/PgSQL is installed" 
-- otherwise it will give a big error message.

(select 'PL/PgSQL is installed.' as message from pg_language where lanname='plpgsql') union (select 'You must install PL/PgSQL before running this SQL file,\nor you will get an error. To install PL/PgSQL run:\n\tcreatelang plpgsql <dbname>'::text as message) order by message limit 1;


-------------------------------------------------------------------
--  HISTOGRAM2D TYPE
-------------------------------------------------------------------






CREATE OR REPLACE FUNCTION histogram2d_in(cstring)
	RETURNS histogram2d
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION histogram2d_out(histogram2d)
	RETURNS cstring
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE TYPE histogram2d (
	alignment = double,
	internallength = variable,
	input = histogram2d_in,
	output = histogram2d_out,
	storage = main
);

-------------------------------------------------------------------
--  BOX3D TYPE
-------------------------------------------------------------------






CREATE OR REPLACE FUNCTION box3d_in(cstring)
	RETURNS box3d
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION box3d_out(box3d)
	RETURNS cstring
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE TYPE box3d (
	alignment = double,
	internallength = 48,
	input = box3d_in,
	output = box3d_out
);

-------------------------------------------------------------------
--  SPHEROID TYPE
-------------------------------------------------------------------






CREATE OR REPLACE FUNCTION spheroid_in(cstring)
	RETURNS spheroid
	AS '$libdir/libpostgis.dll','ellipsoid_in'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION spheroid_out(spheroid)
	RETURNS cstring
	AS '$libdir/libpostgis.dll','ellipsoid_out'
	LANGUAGE 'C' WITH (isstrict);

CREATE TYPE spheroid (
	alignment = double,
	internallength = 65,
	input = spheroid_in,
	output = spheroid_out
);

-------------------------------------------------------------------
--  WKB TYPE
-------------------------------------------------------------------






CREATE OR REPLACE FUNCTION wkb_in(cstring)
	RETURNS wkb
	AS '$libdir/libpostgis.dll','WKB_in'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION wkb_out(wkb)
	RETURNS cstring
	AS '$libdir/libpostgis.dll','WKB_out'
	LANGUAGE 'C' WITH (isstrict);


CREATE OR REPLACE FUNCTION wkb_recv(internal)
	RETURNS wkb
	AS '$libdir/libpostgis.dll','WKB_recv'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION wkb_send(wkb)
	RETURNS bytea
	AS '$libdir/libpostgis.dll','WKBtoBYTEA'
	LANGUAGE 'C' WITH (iscachable,isstrict);


CREATE TYPE wkb (
	internallength = variable,
	input = wkb_in,
	output = wkb_out,

	send = wkb_send,
	receive = wkb_recv,

	storage = extended
);

-------------------------------------------------------------------
--  CHIP TYPE
-------------------------------------------------------------------






CREATE OR REPLACE FUNCTION chip_in(cstring)
	RETURNS chip
	AS '$libdir/libpostgis.dll','CHIP_in'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION chip_out(chip)
	RETURNS cstring
	AS '$libdir/libpostgis.dll','CHIP_out'
	LANGUAGE 'C' WITH (isstrict);

CREATE TYPE chip (
	alignment = double,
	internallength = variable,
	input = chip_in,
	output = chip_out,
	storage = extended
);

-------------------------------------------------------------------
--  GEOMETRY TYPE
-------------------------------------------------------------------






CREATE OR REPLACE FUNCTION geometry_in(cstring)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_out(geometry)
	RETURNS cstring
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);


CREATE OR REPLACE FUNCTION geometry_analyze(internal)
	RETURNS bool
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);


CREATE TYPE geometry (
	alignment = double,
	internallength = variable,
	input = geometry_in,
	output = geometry_out,

	analyze = geometry_analyze,

	storage = main
);


-------------------------------------------------------------------
-- Workaround for old user defined variable length datatype 
-- default value bug. Should not be necessary > 7.2
-------------------------------------------------------------------




-------------------------------------------------------------------
-- GiST Selectivity Function
-------------------------------------------------------------------

CREATE OR REPLACE FUNCTION postgis_gist_sel (internal, oid, internal, int4)

	RETURNS float8
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

-------------------------------------------------------------------
-- SPATIAL_REF_SYS
-------------------------------------------------------------------
CREATE TABLE spatial_ref_sys (
	 srid integer not null primary key,
	 auth_name varchar(256), 
	 auth_srid integer, 
	 srtext varchar(2048),
	 proj4text varchar(2048) 
);

-------------------------------------------------------------------
-- GEOMETRY_COLUMNS
-------------------------------------------------------------------
CREATE TABLE geometry_columns (
	f_table_catalog varchar(256) not null,
	f_table_schema varchar(256) not null,
	f_table_name varchar(256) not null,
	f_geometry_column varchar(256) not null,
	coord_dimension integer not null,
	srid integer not null,
	type varchar(30) not null,

	CONSTRAINT geometry_columns_pk primary key ( 
		f_table_catalog, 
		f_table_schema, 
		f_table_name, 
		f_geometry_column ) );

-----------------------------------------------------------------------
-- POSTGIS_VERSION()
-----------------------------------------------------------------------

CREATE OR REPLACE FUNCTION postgis_version() RETURNS text
AS 'SELECT \'0.9 USE_GEOS=1 USE_PROJ=1 USE_STATS=1\'::text AS version'
LANGUAGE 'sql';

CREATE OR REPLACE FUNCTION postgis_lib_version() RETURNS text
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION postgis_geos_version() RETURNS text
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION postgis_proj_version() RETURNS text
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION postgis_scripts_installed() RETURNS text
AS 'SELECT \'0.0.1\'::text AS version'
LANGUAGE 'sql';

CREATE OR REPLACE FUNCTION postgis_scripts_released() RETURNS text
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION postgis_uses_stats() RETURNS bool
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION postgis_full_version() RETURNS text
AS '
DECLARE
	libver text;
	projver text;
	geosver text;
	usestats bool;
	dbproc text;
	relproc text;
	fullver text;
BEGIN
	SELECT postgis_lib_version() INTO libver;
	SELECT postgis_proj_version() INTO projver;
	SELECT postgis_geos_version() INTO geosver;
	SELECT postgis_uses_stats() INTO usestats;
	SELECT postgis_scripts_installed() INTO dbproc;
	SELECT postgis_scripts_released() INTO relproc;

	fullver = \'POSTGIS="\' || libver || \'"\';

	IF  geosver IS NOT NULL THEN
		fullver = fullver || \' GEOS="\' || geosver || \'"\';
	END IF;

	IF  projver IS NOT NULL THEN
		fullver = fullver || \' PROJ="\' || projver || \'"\';
	END IF;

	IF usestats THEN
		fullver = fullver || \' USE_STATS\';
	END IF;

	fullver = fullver || \' DBPROC="\' || dbproc || \'"\';
	fullver = fullver || \' RELPROC="\' || relproc || \'"\';

	IF dbproc != relproc THEN
		fullver = fullver || \' (needs proc upgrade)\';
	END IF;

	RETURN fullver;
END
' LANGUAGE 'plpgsql';

-----------------------------------------------------------------------
-- FIND_SRID( <schema>, <table>, <geom col> )
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION find_srid(varchar,varchar,varchar) RETURNS int4 AS
'DECLARE
   schem text;
   tabl text;
   sr int4;
BEGIN
   IF $1 IS NULL THEN
      RAISE EXCEPTION ''find_srid() - schema is NULL!'';
   END IF;
   IF $2 IS NULL THEN
      RAISE EXCEPTION ''find_srid() - table name is NULL!'';
   END IF;
   IF $3 IS NULL THEN
      RAISE EXCEPTION ''find_srid() - column name is NULL!'';
   END IF;
   schem = $1;
   tabl = $2;
-- if the table contains a . and the schema is empty
-- split the table into a schema and a table
-- otherwise drop through to default behavior
   IF ( schem = '''' and tabl LIKE ''%.%'' ) THEN
     schem = substr(tabl,1,strpos(tabl,''.'')-1);
     tabl = substr(tabl,length(schem)+2);
   ELSE
     schem = schem || ''%'';
   END IF;

   select SRID into sr from geometry_columns where f_table_schema like schem and f_table_name = tabl and f_geometry_column = $3;
   IF NOT FOUND THEN
       RAISE EXCEPTION ''find_srid() - couldnt find the corresponding SRID - is the geometry registered in the GEOMETRY_COLUMNS table?  Is there an uppercase/lowercase missmatch?'';
   END IF;
  return sr;
END;
'
LANGUAGE 'plpgsql' WITH (iscachable); 

-----------------------------------------------------------------------
-- GET_PROJ4_FROM_SRID( <srid> )
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION get_proj4_from_srid(integer) RETURNS text AS
'SELECT proj4text::text FROM spatial_ref_sys WHERE srid= $1' 
LANGUAGE 'sql' WITH (iscachable,isstrict);


-----------------------------------------------------------------------
-- RENAME_GEOMETRY_TABLE_CONSTRAINTS()
-----------------------------------------------------------------------
-- This function renames geometrytype and srid constraints
-- applied to spatial tables by old AddGeometryColumn to
-- new meaningful name 'enforce_geotype_<geomcolname>'
-- and 'enforce_srid_<geomcolname>'
-- Needs to be called only when upgrading from postgis < 0.8.3
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION rename_geometry_table_constraints() RETURNS text
AS 
'
UPDATE pg_constraint 
	SET conname = textcat(''enforce_geotype_'', a.attname)
	FROM pg_attribute a
	WHERE
		a.attrelid = conrelid
		AND a.attnum = conkey[1]
		AND consrc LIKE ''((geometrytype(%) = %'';

UPDATE pg_constraint
	SET conname = textcat(''enforce_srid_'', a.attname)
	FROM pg_attribute a
	WHERE
		a.attrelid = conrelid
		AND a.attnum = conkey[1]
		AND consrc LIKE ''(srid(% = %)'';

SELECT ''spatial table constraints renamed''::text;

' LANGUAGE 'SQL';

-----------------------------------------------------------------------
-- FIX_GEOMETRY_COLUMNS() 
-----------------------------------------------------------------------
-- This function will:
--
--	o try to fix the schema of records with an invalid one
--		(for PG>=73)
--
--	o link records to system tables through attrelid and varattnum
--		(for PG<80)
--
--	o delete all records for which no linking was possible
--		(for PG<80)
--	
-- 
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION fix_geometry_columns() RETURNS text
AS 
'
DECLARE
	result text;
	linked integer;
	deleted integer;

	foundschema integer;

BEGIN


	-- Since 7.3 schema support has been added.
	-- Previous postgis versions used to put the database name in
	-- the schema column. This needs to be fixed, so we try to 
	-- set the correct schema for each geometry_colums record
	-- looking at table, column, type and srid.
	UPDATE geometry_columns SET f_table_schema = n.nspname
		FROM pg_namespace n, pg_class c, pg_attribute a,
			pg_constraint sridcheck, pg_constraint typecheck
                WHERE ( f_table_schema is NULL
		OR f_table_schema = ''''
                OR f_table_schema NOT IN (
                        SELECT nspname::varchar
                        FROM pg_namespace nn, pg_class cc, pg_attribute aa
                        WHERE cc.relnamespace = nn.oid
                        AND cc.relname = f_table_name::name
                        AND aa.attrelid = cc.oid
                        AND aa.attname = f_geometry_column::name))
                AND f_table_name::name = c.relname
                AND c.oid = a.attrelid
                AND c.relnamespace = n.oid
                AND f_geometry_column::name = a.attname
                AND sridcheck.conrelid = c.oid
                --AND sridcheck.conname = ''$1''
		AND sridcheck.consrc LIKE ''(srid(% = %)''
                AND typecheck.conrelid = c.oid
                --AND typecheck.conname = ''$2''
		AND typecheck.consrc LIKE
	''((geometrytype(%) = ''''%''''::text) OR (% IS NULL))''
                AND sridcheck.consrc ~ textcat('' = '', srid::text)
                AND typecheck.consrc ~ textcat('' = '''''', type::text)
                AND NOT EXISTS (
                        SELECT oid FROM geometry_columns gc
                        WHERE c.relname::varchar = gc.f_table_name

                        AND n.nspname::varchar = gc.f_table_schema

                        AND a.attname::varchar = gc.f_geometry_column
                );

	GET DIAGNOSTICS foundschema = ROW_COUNT;



	-- no linkage to system table needed
	return ''fixed:''||foundschema::text;


	-- fix linking to system tables
	UPDATE geometry_columns SET
		attrelid = NULL,
		varattnum = NULL,
		stats = NULL;

	UPDATE geometry_columns SET
		attrelid = c.oid,
		varattnum = a.attnum

		FROM pg_class c, pg_attribute a, pg_namespace n
		WHERE n.nspname = f_table_schema::name
		AND c.relname = f_table_name::name
		AND c.relnamespace = n.oid

		AND a.attname = f_geometry_column::name
		AND a.attrelid = c.oid;
	
	GET DIAGNOSTICS linked = ROW_COUNT;

	-- remove stale records
	DELETE FROM geometry_columns WHERE attrelid IS NULL;

	GET DIAGNOSTICS deleted = ROW_COUNT;

	result = 

		''fixed:'' || foundschema::text ||

		'' linked:'' || linked::text || 
		'' deleted:'' || deleted::text;

	return result;

END;
'
LANGUAGE 'plpgsql' ;

-----------------------------------------------------------------------
-- PROBE_GEOMETRY_COLUMNS() 
-----------------------------------------------------------------------
-- Fill the geometry_columns table with values probed from the system
-- catalogues. 3d flag can not be probed, it defaults to 2
--
-- Note that bogus records already in geometry_columns are not
-- overridden (a check for schema.table.column is performed), so
-- to have a fresh probe backup your geometry_column, delete from
-- it and probe.
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION probe_geometry_columns() RETURNS text AS
'
DECLARE
	inserted integer;
	oldcount integer;
	probed integer;
	stale integer;
BEGIN

	SELECT count(*) INTO oldcount FROM geometry_columns;

	SELECT count(*) INTO probed
		FROM pg_class c, pg_attribute a, pg_type t, 

			pg_namespace n,

			pg_constraint sridcheck, pg_constraint typecheck
		WHERE t.typname = ''geometry''
		AND a.atttypid = t.oid
		AND a.attrelid = c.oid

		AND c.relnamespace = n.oid
		AND sridcheck.connamespace = n.oid
		AND typecheck.connamespace = n.oid

		AND sridcheck.conrelid = c.oid
		--AND sridcheck.conname = ''$1''
		AND sridcheck.consrc LIKE ''(srid(% = %)''
		AND typecheck.conrelid = c.oid
		--AND typecheck.conname = ''$2'';
		AND typecheck.consrc LIKE
	''((geometrytype(%) = ''''%''''::text) OR (% IS NULL))''
		;

	INSERT INTO geometry_columns SELECT
		''''::varchar as f_table_catalogue,

		n.nspname::varchar as f_table_schema,

		c.relname::varchar as f_table_name,
		a.attname::varchar as f_geometry_column,
		2 as coord_dimension,
		trim(both  '' =)'' from substr(sridcheck.consrc,
			strpos(sridcheck.consrc, ''='')))::integer as srid,
		trim(both '' =)'''''' from substr(typecheck.consrc, 
			strpos(typecheck.consrc, ''=''),
			strpos(typecheck.consrc, ''::'')-
			strpos(typecheck.consrc, ''='')
			))::varchar as type,

		FROM pg_class c, pg_attribute a, pg_type t, 

			pg_namespace n,

			pg_constraint sridcheck, pg_constraint typecheck
		WHERE t.typname = ''geometry''
		AND a.atttypid = t.oid
		AND a.attrelid = c.oid

		AND c.relnamespace = n.oid
		AND sridcheck.connamespace = n.oid
		AND typecheck.connamespace = n.oid

		AND sridcheck.conrelid = c.oid
		--AND sridcheck.conname = ''$1''
		AND sridcheck.consrc LIKE ''(srid(% = %)''
		AND typecheck.conrelid = c.oid
		--AND typecheck.conname = ''$2''
		AND typecheck.consrc LIKE
	''((geometrytype(%) = ''''%''''::text) OR (% IS NULL))''

                AND NOT EXISTS (
                        SELECT oid FROM geometry_columns gc
                        WHERE c.relname::varchar = gc.f_table_name

                        AND n.nspname::varchar = gc.f_table_schema

                        AND a.attname::varchar = gc.f_geometry_column
                );

	GET DIAGNOSTICS inserted = ROW_COUNT;

	IF oldcount > probed THEN
		stale = oldcount-probed;
	ELSE
		stale = 0;
	END IF;

        RETURN ''probed:''||probed||
		'' inserted:''||inserted||
		'' conflicts:''||probed-inserted||
		'' stale:''||stale;
END

' LANGUAGE 'plpgsql';

-----------------------------------------------------------------------
-- FIND_EXTENT( <schema name>, <table name>, <column name> )
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION find_extent(text,text,text) RETURNS box3d AS
'
DECLARE
	schemaname alias for $1;
	tablename alias for $2;
	columnname alias for $3;
	okay boolean;
 myrec RECORD;

BEGIN
	FOR myrec IN EXECUTE ''SELECT extent("''||columnname||''") FROM "''||schemaname||''"."''||tablename||''"'' LOOP
		return myrec.extent;
	END LOOP; 
END;
'
LANGUAGE 'plpgsql' WITH (isstrict);

-----------------------------------------------------------------------
-- FIND_EXTENT( <table name>, <column name> )
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION find_extent(text,text) RETURNS box3d AS
'
DECLARE
	tablename alias for $1;
	columnname alias for $2;
	okay boolean;
 myrec RECORD;

BEGIN
	FOR myrec IN EXECUTE ''SELECT extent("''||columnname||''") FROM "''||tablename||''"'' LOOP
		return myrec.extent;
	END LOOP; 
END;
'
LANGUAGE 'plpgsql' WITH (isstrict);


-----------------------------------------------------------------------
-- TRANSFORM ( <geometry>, <srid> )
-----------------------------------------------------------------------
--
-- Test:
--
-- trans=# select * from spatial_ref_sys ;
--
--  srid |   auth_name   | auth_srid | srtext | proj4text 
-- ------+---------------+-----------+--------+--------------------------------------------------------------------------
--     1 | latlong WGS84 |         1 |        | +proj=longlat +datum=WGS84
--     2 | BC albers     |         2 |        | proj=aea ellps=GRS80 lon_0=-126 lat_0=45 lat_1=50 lat_2=58.5 x_0=1000000
--
-- select transform( 'SRID=1;POINT(-120.8 50.3)', 2);
--      -> 'SRID=2;POINT(1370033.37046971 600755.810968684)'
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION transform_geometry(geometry,text,text,int)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','transform_geom'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION transform(geometry,integer) RETURNS geometry AS
'BEGIN
 RETURN transform_geometry( $1 , get_proj4_from_srid(SRID( $1 ) ), get_proj4_from_srid( $2 ), $2 );
 END;'
LANGUAGE 'plpgsql' WITH (iscachable,isstrict);



-----------------------------------------------------------------------
-- COMMON FUNCTIONS
-----------------------------------------------------------------------

CREATE OR REPLACE FUNCTION srid(chip)
	RETURNS int4
	AS '$libdir/libpostgis.dll','srid_chip'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION height(chip)
	RETURNS int4
	AS '$libdir/libpostgis.dll','height_chip'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION factor(chip)
	RETURNS FLOAT4
	AS '$libdir/libpostgis.dll','factor_chip'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION width(chip)
	RETURNS int4
	AS '$libdir/libpostgis.dll','width_chip'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION datatype(chip)
	RETURNS int4
	AS '$libdir/libpostgis.dll','datatype_chip'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION compression(chip)
	RETURNS int4
	AS '$libdir/libpostgis.dll','compression_chip'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION setSRID(chip,int4)
	RETURNS chip
	AS '$libdir/libpostgis.dll','setsrid_chip'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION setfactor(chip,float4)
	RETURNS chip
	AS '$libdir/libpostgis.dll','setfactor_chip'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION geometry(CHIP)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','CHIP_to_geom'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION box3d(geometry)
	RETURNS box3d
	AS '$libdir/libpostgis.dll','get_bbox_of_geometry'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION box(geometry)
	RETURNS BOX
	AS '$libdir/libpostgis.dll','geometry2box'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION geometry(box3d)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','get_geometry_of_bbox'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION geometry(text)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_text'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION expand(box3d,float8)
	RETURNS box3d
	AS '$libdir/libpostgis.dll','expand_bbox'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION expand(geometry,float8)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','expand_geometry'
	LANGUAGE 'C' WITH (iscachable,isstrict);

--
-- Functions for converting to WKB
--

CREATE OR REPLACE FUNCTION asbinary(geometry)
	RETURNS wkb
	AS '$libdir/libpostgis.dll','asbinary_simple'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION asbinary(geometry,TEXT)
	RETURNS wkb
	AS '$libdir/libpostgis.dll','asbinary_specify'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION bytea(wkb)
	RETURNS bytea
	AS '$libdir/libpostgis.dll','WKBtoBYTEA'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
CREATE OR REPLACE FUNCTION geometry(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','geometryfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
CREATE OR REPLACE FUNCTION GeomFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','geometryfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION GeomFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','geometryfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
CREATE OR REPLACE FUNCTION PointFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','PointfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION PointFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','PointfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
CREATE OR REPLACE FUNCTION LineFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','LinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION LineFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','LinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);


CREATE OR REPLACE FUNCTION LinestringFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','LinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION LinestringFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','LinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
CREATE OR REPLACE FUNCTION PolyFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','PolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION PolyFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','PolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
CREATE OR REPLACE FUNCTION PolygonFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','PolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION PolygonFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','PolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);


CREATE OR REPLACE FUNCTION MPointFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPointfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MPointFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPointfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);


CREATE OR REPLACE FUNCTION MultiPointFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPointfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MultiPointFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPointfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MultiLineFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MLinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MultiLineFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MLinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
	
CREATE OR REPLACE FUNCTION MLineFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MLinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MLineFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MLinefromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MPolyFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MPolyFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
CREATE OR REPLACE FUNCTION MultiPolyFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION MultiPolyFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','MPolyfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);


	
CREATE OR REPLACE FUNCTION GeomCollFromWKB(wkb,int)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','GCfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);

CREATE OR REPLACE FUNCTION GeomCollFromWKB(wkb)
	RETURNS GEOMETRY
	AS '$libdir/libpostgis.dll','GCfromWKB_SRID'
	LANGUAGE 'C' WITH (iscachable,isstrict);
	
	
-- CREATE OR REPLACE FUNCTION index_thing(geometry)
-- RETURNS BOOL
-- AS '$libdir/libpostgis.dll'
-- LANGUAGE 'C' WITH (isstrict);

--
-- Debugging functions
--

CREATE OR REPLACE FUNCTION npoints(geometry)
	RETURNS int4
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION nrings(geometry)
	RETURNS int4
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict) ;

CREATE OR REPLACE FUNCTION mem_size(geometry)
	RETURNS int4
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);


CREATE OR REPLACE FUNCTION summary(geometry)
	RETURNS text
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION translate(geometry,float8,float8,float8)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict) ;

CREATE OR REPLACE FUNCTION dimension(geometry)
	RETURNS int4
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict) ;

CREATE OR REPLACE FUNCTION geometrytype(geometry)
	RETURNS text
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION envelope(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION x(geometry)
	RETURNS float8
	AS '$libdir/libpostgis.dll','x_point'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION y(geometry)
	RETURNS float8
	AS '$libdir/libpostgis.dll','y_point'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION z(geometry)
	RETURNS float8
	AS '$libdir/libpostgis.dll','z_point'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION numpoints(geometry)
	RETURNS integer
	AS '$libdir/libpostgis.dll','numpoints_linestring'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION pointn(geometry,integer)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','pointn_linestring'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION exteriorring(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','exteriorring_polygon'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION numinteriorrings(geometry)
	RETURNS integer
	AS '$libdir/libpostgis.dll','numinteriorrings_polygon'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION interiorringn(geometry,integer)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','interiorringn_polygon'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION numgeometries(geometry)
	RETURNS integer
	AS '$libdir/libpostgis.dll','numgeometries_collection'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometryn(geometry,integer)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometryn_collection'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION max_distance(geometry,geometry)
	RETURNS float8
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION optimistic_overlap(geometry,geometry,FLOAT8)
	RETURNS BOOL
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION segmentize(geometry,FLOAT8)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION distance(geometry,geometry)
	RETURNS float8
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION astext(geometry)
	RETURNS TEXT
	AS '$libdir/libpostgis.dll','astext_geometry'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION srid(geometry)
	RETURNS int4
	AS '$libdir/libpostgis.dll','srid_geom'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometryfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text'
	LANGUAGE 'C' WITH (isstrict,iscachable);
	
	
CREATE OR REPLACE FUNCTION geometryfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION geomfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION geomfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION polyfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_poly'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION polygonfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_poly'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION polygonfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_poly'
	LANGUAGE 'C' WITH (isstrict,iscachable);
	
CREATE OR REPLACE FUNCTION mpolyfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoly'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION linefromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_line'
	LANGUAGE 'C' WITH (isstrict,iscachable);

	
CREATE OR REPLACE FUNCTION mlinefromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mline'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION multilinestringfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mline'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION multilinestringfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mline'
	LANGUAGE 'C' WITH (isstrict,iscachable);
	
CREATE OR REPLACE FUNCTION pointfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_point'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION mpointfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoint'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION multipointfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoint'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION multipointfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoint'
	LANGUAGE 'C' WITH (isstrict,iscachable);
	
CREATE OR REPLACE FUNCTION geomcollfromtext(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_gc'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION setSRID(geometry,int4)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION polyfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_poly'
	LANGUAGE 'C' WITH (isstrict,iscachable);


CREATE OR REPLACE FUNCTION mpolyfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoly'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION multipolygonfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoly'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION multipolygonfromtext(geometry,int)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoly'
	LANGUAGE 'C' WITH (isstrict,iscachable);
	
CREATE OR REPLACE FUNCTION linefromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_line'
	LANGUAGE 'C' WITH (isstrict,iscachable);
	
CREATE OR REPLACE FUNCTION linestringfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_line'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION linestringfromtext(geometry,int)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_line'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION mlinefromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mline'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION pointfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_point'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION mpointfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_mpoint'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION geomcollfromtext(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geometry_from_text_gc'
	LANGUAGE 'C' WITH (isstrict,iscachable);


CREATE OR REPLACE FUNCTION isempty(geometry)
	RETURNS boolean
	AS '$libdir/libpostgis.dll','isempty'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION issimple(geometry)
	RETURNS boolean
	AS '$libdir/libpostgis.dll','issimple'
	LANGUAGE 'C' WITH (isstrict,iscachable);
	

CREATE OR REPLACE FUNCTION equals(geometry,geometry)
	RETURNS boolean
	AS '$libdir/libpostgis.dll','geomequals'
	LANGUAGE 'C' WITH (isstrict,iscachable);


--
-- Special spheroid functions
--

CREATE OR REPLACE FUNCTION length_spheroid(geometry,spheroid)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','length_ellipsoid'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION length3d_spheroid(geometry,spheroid)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','length3d_ellipsoid'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION distance_spheroid(geometry,geometry,spheroid)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','distance_ellipsoid'
	LANGUAGE 'C' WITH (isstrict);

--
-- Generic operations
--

CREATE OR REPLACE FUNCTION multi(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','fluffType'
	LANGUAGE 'C' WITH (isstrict);
	
CREATE OR REPLACE FUNCTION length3d(geometry)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION length(geometry)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','length2d'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION area2d(geometry)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION area(geometry)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','area2d'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION perimeter3d(geometry)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION perimeter(geometry)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','perimeter2d'
	LANGUAGE 'C' WITH (isstrict);

---CREATE OR REPLACE FUNCTION truly_inside(geometry,geometry)
---	RETURNS bool
---	AS '$libdir/libpostgis.dll'
---	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION point_inside_circle(geometry,float8,float8,float8)
	RETURNS bool
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION startpoint(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION endpoint(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION isclosed(geometry)
	RETURNS boolean
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION centroid(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);
	
CREATE OR REPLACE FUNCTION isring(geometry)
	RETURNS boolean
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION pointonsurface(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C' WITH (isstrict);
	

--
-- BBox operations
--

CREATE OR REPLACE FUNCTION xmin(box3d)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','box3d_xmin'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION ymin(box3d)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','box3d_ymin'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION zmin(box3d)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','box3d_zmin'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION xmax(box3d)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','box3d_xmax'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION ymax(box3d)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','box3d_ymax'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION zmax(box3d)
	RETURNS FLOAT8
	AS '$libdir/libpostgis.dll','box3d_zmax'
	LANGUAGE 'C' WITH (isstrict,iscachable);

CREATE OR REPLACE FUNCTION box3dtobox(box3d)
	RETURNS BOX
	AS '$libdir/libpostgis.dll','box3dtobox'
	LANGUAGE 'C' WITH (isstrict,iscachable);

--
-- Aggregate functions
--

CREATE OR REPLACE FUNCTION geom_accum (geometry[],geometry)
	RETURNS geometry[]
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION combine_bbox(box3d,geometry)
	RETURNS box3d
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE AGGREGATE extent(
	sfunc = combine_bbox,
	basetype = geometry,
	stype = box3d
	);

CREATE OR REPLACE FUNCTION collector(geometry,geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE AGGREGATE memcollect(
	sfunc = collector,
	basetype = geometry,
	stype = geometry
	);

CREATE OR REPLACE FUNCTION collect_garray (geometry[])
        RETURNS geometry
        AS '$libdir/libpostgis.dll'
        LANGUAGE 'C';

CREATE AGGREGATE collect (
	sfunc = geom_accum,
	basetype = geometry,
	stype = geometry[],
	finalfunc = collect_garray
	);


--
-- Operator definitions
--

CREATE OR REPLACE FUNCTION geometry_overleft(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_overright(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_left(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_right(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_contain(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_contained(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_overlap(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_same(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

--
-- Sorting functions
--

CREATE OR REPLACE FUNCTION geometry_lt(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_le(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_gt(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_ge(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_eq(geometry, geometry) 
	RETURNS bool
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geometry_cmp(geometry, geometry) 
	RETURNS integer
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

--
-- Two dimensional to three dimensional forces
-- 

CREATE OR REPLACE FUNCTION force_2d(geometry) 
	RETURNS geometry
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION force_3d(geometry) 
	RETURNS geometry
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

--
-- Force collection
--

CREATE OR REPLACE FUNCTION force_collection(geometry) 
	RETURNS geometry
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C' WITH (isstrict);

-- 
-- Operator definitions
--

CREATE OPERATOR << (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_left,
   COMMUTATOR = '>>',
   RESTRICT = positionsel, JOIN = positionjoinsel
);

CREATE OPERATOR &< (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_overleft,
   COMMUTATOR = '&>',
   RESTRICT = positionsel, JOIN = positionjoinsel
);

CREATE OPERATOR && (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_overlap,
   COMMUTATOR = '&&',
   RESTRICT = postgis_gist_sel, JOIN = positionjoinsel
);

CREATE OPERATOR &> (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_overright,
   COMMUTATOR = '&<',
   RESTRICT = positionsel, JOIN = positionjoinsel
);

CREATE OPERATOR >> (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_right,
   COMMUTATOR = '<<',
   RESTRICT = positionsel, JOIN = positionjoinsel
);

CREATE OPERATOR ~= (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_same,
   COMMUTATOR = '~=', 
   RESTRICT = eqsel, JOIN = eqjoinsel
);

CREATE OPERATOR @ (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_contained,
   COMMUTATOR = '~',
   RESTRICT = contsel, JOIN = contjoinsel
);

CREATE OPERATOR ~ (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_contain,
   COMMUTATOR = '@',
   RESTRICT = contsel, JOIN = contjoinsel
);

--
-- Sorting operators for Btree
--

CREATE OPERATOR < (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_lt,
   COMMUTATOR = '>', NEGATOR = '>=',
   RESTRICT = contsel, JOIN = contjoinsel
);

CREATE OPERATOR <= (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_le,
   COMMUTATOR = '>=', NEGATOR = '>',
   RESTRICT = contsel, JOIN = contjoinsel
);

CREATE OPERATOR = (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_eq,
   COMMUTATOR = '=', -- we might implement a faster negator here
   RESTRICT = contsel, JOIN = contjoinsel
);

CREATE OPERATOR >= (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_ge,
   COMMUTATOR = '<=', NEGATOR = '<',
   RESTRICT = contsel, JOIN = contjoinsel
);
CREATE OPERATOR > (
   LEFTARG = GEOMETRY, RIGHTARG = GEOMETRY, PROCEDURE = geometry_gt,
   COMMUTATOR = '<', NEGATOR = '<=',
   RESTRICT = contsel, JOIN = contjoinsel
);

--
-- GEOS Functions
--


CREATE OR REPLACE FUNCTION intersection(geometry,geometry)
   RETURNS geometry
   AS '$libdir/libpostgis.dll','intersection'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION buffer(geometry,float8)
   RETURNS geometry
   AS '$libdir/libpostgis.dll','buffer'
   LANGUAGE 'C' WITH (isstrict);
   
CREATE OR REPLACE FUNCTION convexhull(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','convexhull'
	LANGUAGE 'C' WITH (isstrict);
  
  
CREATE OR REPLACE FUNCTION difference(geometry,geometry)
	RETURNS geometry
        AS '$libdir/libpostgis.dll','difference'
	LANGUAGE 'C' WITH (isstrict);
   
CREATE OR REPLACE FUNCTION boundary(geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','boundary'
	LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION symdifference(geometry,geometry)
        RETURNS geometry
        AS '$libdir/libpostgis.dll','symdifference'
   LANGUAGE 'C' WITH (isstrict);


CREATE OR REPLACE FUNCTION symmetricdifference(geometry,geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','symdifference'
	LANGUAGE 'C' WITH (isstrict);


CREATE OR REPLACE FUNCTION GeomUnion(geometry,geometry)
	RETURNS geometry
	AS '$libdir/libpostgis.dll','geomunion'
	LANGUAGE 'C' WITH (isstrict);

CREATE AGGREGATE MemGeomUnion (
	basetype = geometry,
	sfunc = geomunion,
	stype = geometry
	);

CREATE OR REPLACE FUNCTION unite_garray (geometry[])
	RETURNS geometry
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C'; 

CREATE AGGREGATE GeomUnion (
	sfunc = geom_accum,
	basetype = geometry,
	stype = geometry[],
	finalfunc = unite_garray
	);


CREATE OR REPLACE FUNCTION relate(geometry,geometry)
   RETURNS text
   AS '$libdir/libpostgis.dll','relate_full'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION relate(geometry,geometry,text)
   RETURNS boolean
   AS '$libdir/libpostgis.dll','relate_pattern'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION disjoint(geometry,geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION touches(geometry,geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION intersects(geometry,geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION crosses(geometry,geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION within(geometry,geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION contains(geometry,geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION overlaps(geometry,geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION isvalid(geometry)
   RETURNS boolean
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION geosnoop(geometry)
   RETURNS geometry
   AS '$libdir/libpostgis.dll', 'GEOSnoop'
   LANGUAGE 'C' WITH (isstrict);
   

--
-- Algorithms
--

CREATE OR REPLACE FUNCTION simplify(geometry, float8)
   RETURNS geometry
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

CREATE OR REPLACE FUNCTION line_interpolate_point(geometry, float8)
   RETURNS geometry
   AS '$libdir/libpostgis.dll'
   LANGUAGE 'C' WITH (isstrict);

-------------------------------------------------------------------
-- GiST support functions
-------------------------------------------------------------------





CREATE OR REPLACE FUNCTION ggeometry_consistent(internal,geometry,int4) 
	RETURNS bool 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION ggeometry_compress(internal) 
	RETURNS internal 
	AS '$libdir/libpostgis.dll'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION rtree_decompress(internal) 
	RETURNS internal
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';


CREATE OR REPLACE FUNCTION gbox_penalty(internal,internal,internal) 

	RETURNS internal 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';


CREATE OR REPLACE FUNCTION gbox_picksplit(internal, internal) 

	RETURNS internal 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';


CREATE OR REPLACE FUNCTION gbox_union(bytea, internal) 

	RETURNS internal 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';


CREATE OR REPLACE FUNCTION gbox_same(box, box, internal) 

	RETURNS internal 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';

-------------------------------------------------------------------
-- R-Tree support functions
-------------------------------------------------------------------

CREATE OR REPLACE FUNCTION geometry_union(geometry,geometry) 
	RETURNS geometry 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION geometry_inter(geometry,geometry) 
	RETURNS geometry 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION geometry_size(geometry,opaque) 
	RETURNS float4 
	AS '$libdir/libpostgis.dll' 
	LANGUAGE 'C';




--
-- Create opclass index bindings 
--

CREATE OPERATOR CLASS gist_geometry_ops
	DEFAULT FOR TYPE geometry USING gist AS
	OPERATOR	1	<<	RECHECK,
	OPERATOR	2	&<	RECHECK,
	OPERATOR	3	&&	RECHECK,
	OPERATOR	4	&>	RECHECK,
	OPERATOR	5	>>	RECHECK,
	OPERATOR	6	~=	RECHECK,
	OPERATOR	7	~	RECHECK,
	OPERATOR	8	@	RECHECK,
	FUNCTION	1	ggeometry_consistent (internal, geometry, int4),
	FUNCTION	2	gbox_union (bytea, internal),
	FUNCTION	3	ggeometry_compress (internal),
	FUNCTION	4	rtree_decompress (internal),
	FUNCTION	5	gbox_penalty (internal, internal, internal),
	FUNCTION	6	gbox_picksplit (internal, internal),
	FUNCTION	7	gbox_same (box, box, internal);

UPDATE pg_opclass 
	SET opckeytype = (select oid from pg_type where typname = 'box') 
	WHERE opcname = 'gist_geometry_ops';



CREATE OPERATOR CLASS btree_geometry_ops
	DEFAULT FOR TYPE geometry USING btree AS
	OPERATOR	1	< ,
	OPERATOR	2	<= ,
	OPERATOR	3	= ,
	OPERATOR	4	>= ,
	OPERATOR	5	> ,
	FUNCTION	1	geometry_cmp (geometry, geometry);







-----------------------------------------------------------------------
-- 7.3+ explicit casting definitions
-----------------------------------------------------------------------

--CREATE CAST ( chip AS geometry ) WITH FUNCTION geometry(chip) AS IMPLICIT;
CREATE CAST ( geometry AS box3d ) WITH FUNCTION box3d(geometry) AS IMPLICIT;
CREATE CAST ( geometry AS box ) WITH FUNCTION box(geometry) AS IMPLICIT;
CREATE CAST ( box3d AS geometry ) WITH FUNCTION geometry(box3d) AS IMPLICIT;
CREATE CAST ( text AS geometry) WITH FUNCTION geometry(text) AS IMPLICIT;
CREATE CAST ( wkb AS bytea ) WITH FUNCTION bytea(wkb) AS IMPLICIT;
CREATE CAST ( box3d AS box ) WITH FUNCTION box3dtobox(box3d);
CREATE CAST ( geometry AS text ) WITH FUNCTION astext(geometry);


-----------------------------------------------------------------------
-- ADDGEOMETRYCOLUMN
--   <catalogue>, <schema>, <table>, <column>, <srid>, <type>, <dim>
-----------------------------------------------------------------------
--
-- Type can be one of geometry, GEOMETRYCOLLECTION, POINT, MULTIPOINT, POLYGON,
-- MULTIPOLYGON, LINESTRING, or MULTILINESTRING.
--
-- Types (except geometry) are checked for consistency using a CHECK constraint
-- uses SQL ALTER TABLE command to add the geometry column to the table.
-- Addes a row to geometry_columns.
-- Addes a constraint on the table that all the geometries MUST have the same 
-- SRID. Checks the coord_dimension to make sure its between 0 and 3.
-- Should also check the precision grid (future expansion).
-- Calls fix_geometry_columns() at the end.
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION AddGeometryColumn(varchar,varchar,varchar,varchar,integer,varchar,integer)
	RETURNS text
	AS 
'
DECLARE
	catalog_name alias for $1;
	schema_name alias for $2;
	table_name alias for $3;
	column_name alias for $4;
	new_srid alias for $5;
	new_type alias for $6;
	new_dim alias for $7;

	rec RECORD;
	schema_ok bool;
	real_schema name;

	fixgeomres text;

BEGIN

	IF ( not ( (new_type =''GEOMETRY'') or
		   (new_type =''GEOMETRYCOLLECTION'') or
		   (new_type =''POINT'') or 
		   (new_type =''MULTIPOINT'') or
		   (new_type =''POLYGON'') or
		   (new_type =''MULTIPOLYGON'') or
		   (new_type =''LINESTRING'') or
		   (new_type =''MULTILINESTRING'')) )
	THEN
		RAISE EXCEPTION ''Invalid type name - valid ones are: 
			GEOMETRY, GEOMETRYCOLLECTION, POINT, 
			MULTIPOINT, POLYGON, MULTIPOLYGON, 
			LINESTRING, or MULTILINESTRING '';
		return ''fail'';
	END IF;

	IF ( (new_dim >3) or (new_dim <0) ) THEN
		RAISE EXCEPTION ''invalid dimension'';
		return ''fail'';
	END IF;


	IF ( schema_name != '''' ) THEN
		schema_ok = ''f'';
		FOR rec IN SELECT nspname FROM pg_namespace WHERE text(nspname) = schema_name LOOP
			schema_ok := ''t'';
		END LOOP;

		if ( schema_ok <> ''t'' ) THEN
			RAISE NOTICE ''Invalid schema name - using current_schema()'';
			SELECT current_schema() into real_schema;
		ELSE
			real_schema = schema_name;
		END IF;

	ELSE
		SELECT current_schema() into real_schema;
	END IF;



	-- Add geometry column

	EXECUTE ''ALTER TABLE '' ||

		quote_ident(real_schema) || ''.'' || quote_ident(table_name)

		|| '' ADD COLUMN '' || quote_ident(column_name) || 
		'' geometry '';


	-- Delete stale record in geometry_column (if any)

	EXECUTE ''DELETE FROM geometry_columns WHERE
		f_table_catalog = '' || quote_literal('''') || 
		'' AND f_table_schema = '' ||

		quote_literal(real_schema) || 

		'' AND f_table_name = '' || quote_literal(table_name) ||
		'' AND f_geometry_column = '' || quote_literal(column_name);


	-- Add record in geometry_column 

	EXECUTE ''INSERT INTO geometry_columns VALUES ('' ||
		quote_literal('''') || '','' ||

		quote_literal(real_schema) || '','' ||

		quote_literal(table_name) || '','' ||
		quote_literal(column_name) || '','' ||
		new_dim || '','' || new_srid || '','' ||
		quote_literal(new_type) || '')'';

	-- Add table checks

	EXECUTE ''ALTER TABLE '' || 

		quote_ident(real_schema) || ''.'' || quote_ident(table_name)

		|| '' ADD CONSTRAINT "enforce_srid_'' || 
		column_name || ''" CHECK (SRID('' || quote_ident(column_name) ||
		'') = '' || new_srid || '')'' ;

	IF (not(new_type = ''GEOMETRY'')) THEN
		EXECUTE ''ALTER TABLE '' || 

		quote_ident(real_schema) || ''.'' || quote_ident(table_name)

		|| '' ADD CONSTRAINT "enforce_geotype_'' ||
		column_name || ''" CHECK (geometrytype('' ||
		quote_ident(column_name) || '')='' ||
		quote_literal(new_type) || '' OR ('' ||
		quote_ident(column_name) || '') is null)'';
	END IF;

	SELECT fix_geometry_columns() INTO fixgeomres;

	return 

		real_schema || ''.'' || 

		table_name || ''.'' || column_name ||
		'' SRID:'' || new_srid ||
		'' TYPE:'' || new_type || ''\n '' ||
		''geometry_column '' || fixgeomres;
END;
' LANGUAGE 'plpgsql' WITH (isstrict);

----------------------------------------------------------------------------
-- ADDGEOMETRYCOLUMN ( <schema>, <table>, <column>, <srid>, <type>, <dim> )
----------------------------------------------------------------------------
--
-- This is a wrapper to the real AddGeometryColumn, for use
-- when catalogue is undefined
--
----------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION AddGeometryColumn(varchar,varchar,varchar,integer,varchar,integer) RETURNS text AS '
DECLARE
	ret  text;
BEGIN
	SELECT AddGeometryColumn('''',$1,$2,$3,$4,$5,$6) into ret;
	RETURN ret;
END;
' LANGUAGE 'plpgsql' WITH (isstrict);

----------------------------------------------------------------------------
-- ADDGEOMETRYCOLUMN ( <table>, <column>, <srid>, <type>, <dim> )
----------------------------------------------------------------------------
--
-- This is a wrapper to the real AddGeometryColumn, for use
-- when catalogue and schema are undefined
--
----------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION AddGeometryColumn(varchar,varchar,integer,varchar,integer) RETURNS text AS '
DECLARE
	ret  text;
BEGIN
	SELECT AddGeometryColumn('''','''',$1,$2,$3,$4,$5) into ret;
	RETURN ret;
END;
' LANGUAGE 'plpgsql' WITH (isstrict);

-----------------------------------------------------------------------
-- DROPGEOMETRYCOLUMN
--   <catalogue>, <schema>, <table>, <column>
-----------------------------------------------------------------------
--
-- Removes geometry column reference from geometry_columns table.
-- Drops the column with pgsql >= 73.
-- Make some silly enforcements on it for pgsql < 73
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION DropGeometryColumn(varchar, varchar,varchar,varchar)
	RETURNS text
	AS 
'
DECLARE
	catalog_name alias for $1; 
	schema_name alias for $2;
	table_name alias for $3;
	column_name alias for $4;
	myrec RECORD;
	okay boolean;
	real_schema name;

BEGIN



	-- Find, check or fix schema_name
	IF ( schema_name != '''' ) THEN
		okay = ''f'';

		FOR myrec IN SELECT nspname FROM pg_namespace WHERE text(nspname) = schema_name LOOP
			okay := ''t'';
		END LOOP;

		IF ( okay <> ''t'' ) THEN
			RAISE NOTICE ''Invalid schema name - using current_schema()'';
			SELECT current_schema() into real_schema;
		ELSE
			real_schema = schema_name;
		END IF;
	ELSE
		SELECT current_schema() into real_schema;
	END IF;


 	-- Find out if the column is in the geometry_columns table
	okay = ''f'';
	FOR myrec IN SELECT * from geometry_columns where f_table_schema = text(real_schema) and f_table_name = table_name and f_geometry_column = column_name LOOP
		okay := ''t'';
	END LOOP; 
	IF (okay <> ''t'') THEN 
		RAISE EXCEPTION ''column not found in geometry_columns table'';
		RETURN ''f'';
	END IF;

	-- Remove ref from geometry_columns table
	EXECUTE ''delete from geometry_columns where f_table_schema = '' ||
		quote_literal(real_schema) || '' and f_table_name = '' ||
		quote_literal(table_name)  || '' and f_geometry_column = '' ||
		quote_literal(column_name);
	

	-- Remove table column
	EXECUTE ''ALTER TABLE '' || quote_ident(real_schema) || ''.'' ||
		quote_ident(table_name) || '' DROP COLUMN '' ||
		quote_ident(column_name);



	RETURN real_schema || ''.'' || table_name || ''.'' || column_name ||'' effectively removed.'';
	
END;
'
LANGUAGE 'plpgsql' WITH (isstrict);

-----------------------------------------------------------------------
-- DROPGEOMETRYCOLUMN
--   <schema>, <table>, <column>
-----------------------------------------------------------------------
--
-- This is a wrapper to the real DropGeometryColumn, for use
-- when catalogue is undefined
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION DropGeometryColumn(varchar,varchar,varchar)
	RETURNS text
	AS 
'
DECLARE
	ret text;
BEGIN
	SELECT DropGeometryColumn('''',$1,$2,$3) into ret;
	RETURN ret;
END;
' LANGUAGE 'plpgsql' WITH (isstrict);

-----------------------------------------------------------------------
-- DROPGEOMETRYCOLUMN
--   <table>, <column>
-----------------------------------------------------------------------
--
-- This is a wrapper to the real DropGeometryColumn, for use
-- when catalogue and schema is undefined. 
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION DropGeometryColumn(varchar,varchar)
	RETURNS text
	AS 
'
DECLARE
	ret text;
BEGIN
	SELECT DropGeometryColumn('''','''',$1,$2) into ret;
	RETURN ret;
END;
' LANGUAGE 'plpgsql' WITH (isstrict);

-----------------------------------------------------------------------
-- DROPGEOMETRYTABLE
--   <catalogue>, <schema>, <table>
-----------------------------------------------------------------------
--
-- Drop a table and all its references in geometry_columns
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION DropGeometryTable(varchar, varchar,varchar)
	RETURNS text
	AS 
'
DECLARE
	catalog_name alias for $1; 
	schema_name alias for $2;
	table_name alias for $3;
	real_schema name;

BEGIN


	IF ( schema_name = '''' ) THEN
		SELECT current_schema() into real_schema;
	ELSE
		real_schema = schema_name;
	END IF;


	-- Remove refs from geometry_columns table
	EXECUTE ''DELETE FROM geometry_columns WHERE '' ||

		''f_table_schema = '' || quote_literal(real_schema) ||
		'' AND '' ||

		'' f_table_name = '' || quote_literal(table_name);
	
	-- Remove table 
	EXECUTE ''DROP TABLE ''

		|| quote_ident(real_schema) || ''.'' ||

		quote_ident(table_name);

	RETURN

		real_schema || ''.'' ||

		table_name ||'' dropped.'';
	
END;
'
LANGUAGE 'plpgsql' WITH (isstrict);

-----------------------------------------------------------------------
-- DROPGEOMETRYTABLE
--   <schema>, <table>
-----------------------------------------------------------------------
--
-- Drop a table and all its references in geometry_columns
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION DropGeometryTable(varchar,varchar) RETURNS text AS 
'SELECT DropGeometryTable('''',$1,$2)'
LANGUAGE 'sql' WITH (isstrict);

-----------------------------------------------------------------------
-- DROPGEOMETRYTABLE
--   <table>
-----------------------------------------------------------------------
--
-- Drop a table and all its references in geometry_columns
-- For PG>=73 use current_schema()
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION DropGeometryTable(varchar) RETURNS text AS 
'SELECT DropGeometryTable('''','''',$1)'
LANGUAGE 'sql' WITH (isstrict);

-----------------------------------------------------------------------
-- UPDATE_GEOMETRY_STATS()
-----------------------------------------------------------------------
--
-- Only meaningful for PG<80.
-- Gather statisticts about geometry columns for use
-- with cost estimator.
--
-- It is defined also for PG>=80 for back-compatibility
--
-----------------------------------------------------------------------

CREATE OR REPLACE FUNCTION update_geometry_stats() RETURNS text
AS ' SELECT ''update_geometry_stats() has been obsoleted. Statistics are automatically built running the ANALYZE command''::text' LANGUAGE 'sql';


-----------------------------------------------------------------------
-- UPDATE_GEOMETRY_STATS( <table>, <column> )
-----------------------------------------------------------------------
--
-- Only meaningful for PG<80.
-- Gather statisticts about a geometry column for use
-- with cost estimator.
--
-- It is defined also for PG>=80 for back-compatibility
--
-----------------------------------------------------------------------

CREATE OR REPLACE FUNCTION update_geometry_stats(varchar,varchar) RETURNS text
AS 'SELECT update_geometry_stats();' LANGUAGE 'sql' ;


-----------------------------------------------------------------------
-- CREATE_HISTOGRAM2D( <box>, <size> )
-----------------------------------------------------------------------
--
-- Returns a histgram with 0s in all the boxes.
--
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION create_histogram2d(box3d,int)
	RETURNS histogram2d
	AS '$libdir/libpostgis.dll','create_histogram2d'
	LANGUAGE 'C'  with (isstrict);

-----------------------------------------------------------------------
-- BUILD_HISTOGRAM2D( <histogram2d>, <tablename>, <columnname> )
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION build_histogram2d (histogram2d,text,text)
	RETURNS histogram2d
	AS '$libdir/libpostgis.dll','build_histogram2d'
	LANGUAGE 'C'  with (isstrict);


-----------------------------------------------------------------------
-- BUILD_HISTOGRAM2D(<histogram2d>,<schema>,<tablename>,<columnname>)
-----------------------------------------------------------------------
-- This is a wrapper to the omonimous schema unaware function,
-- thanks to Carl Anderson for the idea.
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION build_histogram2d (histogram2d,text,text,text)
RETURNS histogram2d
AS '
BEGIN
	EXECUTE ''SET local search_path = ''||$2||'',public'';
	RETURN public.build_histogram2d($1,$3,$4);
END
' LANGUAGE 'plpgsql'  with (isstrict);


-----------------------------------------------------------------------
-- EXPLODE_HISTOGRAM2D( <histogram2d>, <tablename> )
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION explode_histogram2d (HISTOGRAM2D,text)
	RETURNS histogram2d
	AS '$libdir/libpostgis.dll','explode_histogram2d'
	LANGUAGE 'C'  with (isstrict);

-----------------------------------------------------------------------
-- ESTIMATE_HISTOGRAM2D( <histogram2d>, <box> )
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION estimate_histogram2d(HISTOGRAM2D,box)
	RETURNS float8
	AS '$libdir/libpostgis.dll','estimate_histogram2d'
	LANGUAGE 'C'  with (isstrict);


-----------------------------------------------------------------------
-- SVG OUTPUT
-----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION AsSvg(geometry,int4,int4)
	RETURNS TEXT
	AS '$libdir/libpostgis.dll','assvg_geometry'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION AsSvg(geometry,int4)
	RETURNS TEXT
	AS '$libdir/libpostgis.dll','assvg_geometry'
	LANGUAGE 'C';

CREATE OR REPLACE FUNCTION AsSvg(geometry)
	RETURNS TEXT
	AS '$libdir/libpostgis.dll','assvg_geometry'
	LANGUAGE 'C';

END TRANSACTION;
