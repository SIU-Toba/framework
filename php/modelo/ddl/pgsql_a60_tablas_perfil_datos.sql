--*******************************************************************************************
--*******************************************************************************************
--************************************** PERFIL de DATOS ************************************
--*******************************************************************************************
--*******************************************************************************************

CREATE SEQUENCE apex_usuario_perfil_datos_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
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
	-- Solo para que se actualize la secuencia, se preserva el campo varchar por compatibilidad hacia atras...
	usuario_perfil_datos_id			int4		NOT NULL DEFAULT nextval('"apex_usuario_perfil_datos_seq"'::text) NOT NULL, 
	proyecto						varchar(15)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL DEFAULT nextval('"apex_usuario_perfil_datos_seq"'::text) NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar			NULL,
	listar							smallint		NULL,
	CONSTRAINT	"apex_usuario_perfil_datos_pk" PRIMARY	KEY ("proyecto","usuario_perfil_datos"),
	CONSTRAINT	"apex_usuario_perfil_datos_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE SEQUENCE apex_usuario_perfil_datos_dims_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_usuario_perfil_datos_dims
--------------------------------------------------------------------------------------------------
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
	elemento						int4			DEFAULT nextval('"apex_usuario_perfil_datos_dims_seq"'::text) NOT NULL,
	clave							varchar			NULL,
	CONSTRAINT	"apex_usuario_perfil_datos_dims_pk" PRIMARY	KEY ("elemento"),
	CONSTRAINT	"apex_usuario_perfil_datos_dims_fk_perfda"	FOREIGN KEY	("proyecto","usuario_perfil_datos") REFERENCES	"apex_usuario_perfil_datos" ("proyecto","usuario_perfil_datos") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usuario_perfil_datos_dims_fk_dim"	FOREIGN KEY	("proyecto","dimension") REFERENCES	"apex_dimension" ("proyecto","dimension") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
