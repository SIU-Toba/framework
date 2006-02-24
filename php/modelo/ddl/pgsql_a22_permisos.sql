--#################################################################################################
--##						PERMISOS particulares de grupos de acceso
--#################################################################################################

CREATE SEQUENCE apex_permiso_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
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
	permiso						int4	DEFAULT nextval('"apex_permiso_seq"'::text) NOT NULL, 
	proyecto							varchar(15)		NOT NULL,
	nombre								varchar(100)	NOT NULL,
	descripcion							varchar(255)	NULL,
	mensaje_particular					varchar			NULL,
	CONSTRAINT	"apex_per_pk" 			PRIMARY	KEY ("permiso", "proyecto"),
	CONSTRAINT	"apex_per_uq_nombre" 	UNIQUE	("proyecto","nombre")
);

CREATE TABLE apex_permiso_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: permiso, usuario_grupo_acc
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(20)		NOT NULL,
	permiso								int4			NOT NULL,
	CONSTRAINT	"apex_per_grupo_acc_pk" 		PRIMARY	KEY ("usuario_grupo_acc","permiso","proyecto"),
	CONSTRAINT	"apex_per_grupo_acc_grupo_fk"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_per_grupo_acc_per_fk"		FOREIGN KEY ("permiso","proyecto") REFERENCES "apex_permiso" ("permiso","proyecto") ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
