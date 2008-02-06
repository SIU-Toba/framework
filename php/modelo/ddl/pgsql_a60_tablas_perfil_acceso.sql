--*******************************************************************************************
--*******************************************************************************************
--************************************** PERFIL de ACCESO ***********************************
--*******************************************************************************************
--*******************************************************************************************

CREATE TABLE apex_usuario_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: usuario_grupo_acc
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	usuario_grupo_acc				varchar(30)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	nivel_acceso					smallint		NULL,
	descripcion						varchar			NULL,
	vencimiento						date			NULL,
	dias							smallint		NULL,
	hora_entrada					time(0) without time	zone NULL,
	hora_salida						time(0) without time	zone NULL,
	listar							smallint			NULL,
	CONSTRAINT	"apex_usu_g_acc_pk" PRIMARY KEY ("proyecto","usuario_grupo_acc")
	--CONSTRAINT	"apex_usu_g_acc_fk_niv"	FOREIGN KEY	("nivel_acceso") REFERENCES "apex_nivel_acceso"	("nivel_acceso") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
);
--#################################################################################################

CREATE TABLE apex_usuario_grupo_acc_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: usuario_grupo_acc, item
--: zona: usuario, item
--: desc:
--: columna_grupo_desarrollo: item
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)		NOT NULL,
	usuario_grupo_acc				varchar(30)		NOT NULL,
	item_id							int4				NULL,	
	item							varchar(60)		NOT NULL,
	CONSTRAINT	"apex_usu_item_pk" PRIMARY	KEY ("proyecto","usuario_grupo_acc","item"),
	CONSTRAINT	"apex_usu_item_fk_us_gru_acc"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usu_item_fk_item"	 FOREIGN KEY	("proyecto","item") 
			REFERENCES "apex_item" ("proyecto","item")	
					ON	DELETE CASCADE ON UPDATE	CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_permiso_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: permiso, usuario_grupo_acc
--: zona: usuario
--: desc:
--: columna_grupo_desarrollo: permiso
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(30)		NOT NULL,
	permiso								int4			NOT NULL,
	CONSTRAINT	"apex_per_grupo_acc_pk" 		PRIMARY	KEY ("usuario_grupo_acc","permiso","proyecto"),
	CONSTRAINT	"apex_per_grupo_acc_grupo_fk"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);