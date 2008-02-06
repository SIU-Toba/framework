--*******************************************************************************************
--*******************************************************************************************
--************************************** PERFIL de DATOS ************************************
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
	schema							varchar(60)		NOT NULL,
	tabla							varchar(80)		NOT NULL,
	col_id							varchar			NOT NULL,
	col_desc						varchar(250)	NOT NULL,
	col_desc_separador				varchar(40)		NOT NULL,
	multitabla_col_tabla			varchar(80)		NOT NULL,
	multitabla_id_tabla				varchar(40)		NOT NULL,
	subclase						varchar(80)		NULL,
	subclase_archivo				varchar(120)	NULL,
	CONSTRAINT	"apex_dimension_pk" PRIMARY	KEY ("proyecto","dimension"),
	CONSTRAINT	"apex_dimension_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_usuario_perfil_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario_perfil_datos
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar			NULL,
	listar							smallint		NULL,
	CONSTRAINT	"apex_usuario_perfil_datos_pk" PRIMARY	KEY ("proyecto","usuario_perfil_datos"),
	CONSTRAINT	"apex_usuario_perfil_datos_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_usuario_perfil_datos_dims
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario_perfil_datos, dimension
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL,
	dimension						int4			NOT NULL,
	CONSTRAINT	"apex_usuario_perfil_datos_dims_pk" PRIMARY	KEY ("proyecto","usuario_perfil_datos","dimension"),
	CONSTRAINT	"apex_usuario_perfil_datos_dims_fk_perfda"	FOREIGN KEY	("proyecto","usuario_perfil_datos") REFERENCES	"apex_usuario_perfil_datos" ("proyecto","usuario_perfil_datos") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usuario_perfil_datos_dims_fk_dim"	FOREIGN KEY	("proyecto","dimension") REFERENCES	"apex_dimension" ("proyecto","dimension") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE SEQUENCE apex_usuario_perfil_datos_dims_elemento_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_usuario_perfil_datos_dims_elemento
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: elemento
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL,
	dimension						int4			NOT NULL,
	elemento						int4			DEFAULT nextval('"apex_usuario_perfil_datos_dims_elemento_seq"'::text) NOT NULL,
	clave							varchar			NULL,
	CONSTRAINT	"apex_usuario_perfdadims_elemento_pk" PRIMARY	KEY ("elemento"),
	CONSTRAINT	"apex_usuario_perfdadims_elemento_fk_perfdadim"	
		FOREIGN KEY	("proyecto","usuario_perfil_datos","dimension") 
			REFERENCES	"apex_usuario_perfil_datos_dims" ("proyecto","usuario_perfil_datos","dimension") 
				ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
