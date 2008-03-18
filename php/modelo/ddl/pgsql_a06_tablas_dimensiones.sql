--*******************************************************************************************
--*******************************************************************************************
--*****************************		DIMENSIONES		 ****************************************
--*******************************************************************************************
--*******************************************************************************************

CREATE SEQUENCE apex_dimension_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_dimension
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: dimension
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	dimension						int4			DEFAULT nextval('"apex_dimension_seq"'::text) NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar(255)	NULL,
	schema							varchar(60)		NULL,
	tabla							varchar(80)		NOT NULL,
	col_id							varchar			NOT NULL,
	col_desc						varchar(250)	NOT NULL,
	col_desc_separador				varchar(40)		NULL,
	multitabla_col_tabla			varchar(80)		NULL,
	multitabla_id_tabla				varchar(40)		NULL,
	subclase						varchar(80)		NULL,
	subclase_archivo				varchar(120)	NULL,
	fuente_datos_proyecto			varchar(15)		NOT NULL,
	fuente_datos					varchar(20)		NOT NULL,
	CONSTRAINT	"apex_objeto_fk_fuente_datos"	FOREIGN KEY	("fuente_datos_proyecto","fuente_datos") REFERENCES "apex_fuente_datos"	("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_dimension_pk" PRIMARY	KEY ("proyecto","dimension"),
	CONSTRAINT	"apex_dimension_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);