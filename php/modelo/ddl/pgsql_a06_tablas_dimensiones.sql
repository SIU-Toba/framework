--*******************************************************************************************
--*******************************************************************************************
--*****************************		RELACIONES		 ****************************************
--*******************************************************************************************
--*******************************************************************************************

CREATE SEQUENCE apex_relacion_tablas_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_relacion_tablas
--------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: relacion_tablas
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	fuente_datos_proyecto			varchar(15)		NOT NULL,
	fuente_datos					varchar(20)		NOT NULL,
	proyecto						varchar(15)		NOT NULL,
	relacion_tablas					int4			DEFAULT nextval('"apex_relacion_tablas_seq"'::text) NOT NULL,
	tabla_1							varchar(80)		NOT NULL,
	tabla_1_cols					varchar			NOT NULL,
	tabla_2							varchar(80)		NOT NULL,
	tabla_2_cols					varchar			NOT NULL,
	CONSTRAINT	"apex_relacion_tablas_pk" PRIMARY	KEY ("relacion_tablas"),
	CONSTRAINT	"apex_objeto_fk_fuente_datos"	FOREIGN KEY	("fuente_datos_proyecto","fuente_datos") REFERENCES "apex_fuente_datos"	("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_relacion_tablas_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);

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
	fuente_datos_proyecto			varchar(15)		NOT NULL,
	fuente_datos					varchar(20)		NOT NULL,
	CONSTRAINT	"apex_objeto_fk_fuente_datos"	FOREIGN KEY	("fuente_datos_proyecto","fuente_datos") REFERENCES "apex_fuente_datos"	("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_dimension_pk" PRIMARY	KEY ("proyecto","dimension"),
	CONSTRAINT	"apex_dimension_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_dimension_gatillo_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_dimension_gatillo
--------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: gatillo
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	dimension						int4			NOT NULL,
	gatillo							int4			DEFAULT nextval('"apex_dimension_gatillo_seq"'::text) NOT NULL,
	tipo							varchar(20)		NOT NULL, 		-- 'directo' | 'indirecto'
	orden							int4			NOT NULL,
	tabla_rel_dim					varchar(80)		NOT NULL,		-- Tabla usada como gatillo
	columnas_rel_dim				varchar			NULL,			-- Solo para directos. Si tiene mas de una columna, expresada en el mismo orden que la definicion de la dim
	tabla_gatillo					varchar(80)		NULL,			-- Solo para indirectos. referencia a un gatillo directo
	ruta_tabla_rel_dim				varchar			NULL,			-- Solo para indirectos. Ruta entre la tabla_rel_dim del gatillo indirecto y la tabla_rel_dim del gatillo directo. Si esta vacio es porque las dos tablas tienen entre si un FK en la db, sino, la ruta que las vincula. Estas relaciones tienen que exitir en la tabla de relaciones que esta arriba en el archivo
	CONSTRAINT	"apex_dimension_gatillo_pk" PRIMARY	KEY ("proyecto", "gatillo"),
	CONSTRAINT	"apex_dimension_gatillo_uq_tabla" UNIQUE ("proyecto","dimension","tabla_rel_dim"),
	CONSTRAINT	"apex_dimension_gatillo_fk_dim"	FOREIGN KEY	("proyecto","dimension") REFERENCES	"apex_dimension" ("proyecto","dimension") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);

--###################################################################################################

