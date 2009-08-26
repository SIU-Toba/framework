--#################################################################################################
--##						PERMISOS particulares de grupos de acceso
--#################################################################################################

CREATE SEQUENCE apex_permiso_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_permiso
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: permiso
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	permiso						int8	DEFAULT nextval('"apex_permiso_seq"'::text) NOT NULL, 
	proyecto							varchar(15)		NOT NULL,
	nombre								TEXT	NOT NULL,
	descripcion							TEXT	NULL,
	mensaje_particular					TEXT			NULL,
	CONSTRAINT	"apex_per_pk" 			PRIMARY	KEY ("permiso", "proyecto"),
	CONSTRAINT	"apex_per_uq_nombre" 	UNIQUE	("proyecto","nombre")
);
