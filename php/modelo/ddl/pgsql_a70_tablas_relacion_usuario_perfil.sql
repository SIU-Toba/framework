--*******************************************************************************************
--*******************************************************************************************
--*****************  Relaciones entre los usuarios y el esquema de perfiles *****************
--*******************************************************************************************
--*******************************************************************************************

CREATE TABLE apex_usuario_proyecto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario
--: zona: usuario
--: instancia:	1
--: usuario:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)			NOT NULL,
	usuario_grupo_acc					varchar(30)			NOT NULL,
	usuario								varchar(60)			NOT NULL,
	usuario_perfil_datos				TEXT				NULL,
	CONSTRAINT	"apex_usu_proy_pk"  PRIMARY KEY ("proyecto", "usuario_grupo_acc", "usuario"),
	CONSTRAINT	"apex_usu_proy_fk_usuario"	FOREIGN KEY	("usuario")	REFERENCES "apex_usuario" ("usuario") ON DELETE	CASCADE ON UPDATE	CASCADE DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_usu_proy_fk_grupo_acc" FOREIGN KEY ("proyecto","usuario_grupo_acc") REFERENCES "apex_usuario_grupo_acc" ("proyecto","usuario_grupo_acc") ON DELETE	CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_usuario_proyecto_perfil_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario
--: zona: usuario
--: instancia:	1
--: usuario:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)			NOT NULL,
	usuario_perfil_datos				int8				NULL,	
	usuario								varchar(60)			NOT NULL,
	CONSTRAINT	"apex_usu_proy_pd_pk"  PRIMARY KEY ("proyecto", "usuario_perfil_datos", "usuario"),
	CONSTRAINT	"apex_usu_proy_pd_fk_usuario"	FOREIGN KEY	("usuario")	REFERENCES "apex_usuario" ("usuario") ON DELETE	CASCADE ON UPDATE	CASCADE DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_usu_proy_pd_fk_perf_dat" FOREIGN	KEY ("proyecto","usuario_perfil_datos") REFERENCES	"apex_usuario_perfil_datos" ("proyecto","usuario_perfil_datos") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################
