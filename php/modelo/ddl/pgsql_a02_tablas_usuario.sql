--**************************************************************************************************
--**************************************************************************************************
--*********************************************	 Usuario	 ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_usuario_tipodoc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: usuario_tipodoc
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	usuario_tipodoc				varchar(10)		NOT NULL,
	descripcion						varchar(40)		NOT NULL,
	CONSTRAINT	"apex_usuario_tipodoc_pk"	 PRIMARY	KEY ("usuario_tipodoc")
);
--#################################################################################################

CREATE TABLE apex_usuario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: usuario
--: zona: usuario
--: desc:
--: instancia:	1
--: usuario:	1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	usuario							varchar(60)		NOT NULL,
	clave							varchar(128)	NOT NULL,
	nombre							varchar(255)	NULL,
	usuario_tipodoc					varchar(10)		NULL,
	pre								varchar(2)		NULL,
	ciu								varchar(18)		NULL,
	suf								varchar(1)		NULL,
	email							varchar(80)		NULL,
	telefono						varchar(18)		NULL,
	vencimiento						date				NULL,
	dias							smallint			NULL,
	hora_entrada					time(0) without time	zone NULL,
	hora_salida						time(0) without time	zone NULL,
	ip_permitida					varchar(20)		NULL,
	solicitud_registrar				smallint			NULL,
	solicitud_obs_tipo_proyecto		varchar(15)		NULL,
	solicitud_obs_tipo				varchar(20)		NULL,
	solicitud_observacion			varchar(255)	NULL,
	parametro_a						varchar(100)	NULL,
	parametro_b						varchar(100)	NULL,
	parametro_c						varchar(100)	NULL,
	autentificacion					varchar(10)		NULL DEFAULT('plano'),
	CONSTRAINT	"apex_usuario_pk"	 PRIMARY	KEY ("usuario"),
	--CONSTRAINT	"apex_usuario_fk_sol_ot" FOREIGN	KEY ("solicitud_obs_tipo_proyecto","solicitud_obs_tipo")	REFERENCES "apex_solicitud_obs_tipo" ("proyecto","solicitud_obs_tipo") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usuario_fk_tipodoc" FOREIGN KEY ("usuario_tipodoc") REFERENCES	"apex_usuario_tipodoc" ("usuario_tipodoc") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
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
	proyecto							varchar(15)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar			NULL,
	listar							smallint			NULL,
	CONSTRAINT	"apex_usuario_perfil_datos_pk" PRIMARY	KEY ("proyecto","usuario_perfil_datos")
	--CONSTRAINT	"apex_usuario_perfil_da_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--#################################################################################################

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
	usuario_grupo_acc				varchar(20)		NOT NULL,
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
	proyecto							varchar(15)		NOT NULL,
	usuario							varchar(60)			NOT NULL,
	usuario_grupo_acc				varchar(20)			NOT NULL,
	usuario_perfil_datos			varchar(20)			NULL,
	CONSTRAINT	"apex_usu_proy_pk"  PRIMARY KEY ("proyecto","usuario"),
	CONSTRAINT	"apex_usu_proy_fk_usuario"	FOREIGN KEY	("usuario")	REFERENCES "apex_usuario" ("usuario") ON DELETE	CASCADE ON UPDATE	CASCADE DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_usu_proy_fk_grupo_acc" FOREIGN KEY ("proyecto","usuario_grupo_acc") REFERENCES "apex_usuario_grupo_acc" ("proyecto","usuario_grupo_acc") ON DELETE	CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE
	--CONSTRAINT	"apex_usu_proy_fk_perf_dat" FOREIGN	KEY ("proyecto","usuario_perfil_datos") REFERENCES	"apex_usuario_perfil_datos" ("proyecto","usuario_perfil_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);

CREATE TABLE apex_usuario_grupo_acc_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: usuario_grupo_acc, item
--: zona: usuario, item
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto								varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(20)		NOT NULL,
	item_id								int4				NULL,	
	item									varchar(60)		NOT NULL,
	CONSTRAINT	"apex_usu_item_pk" PRIMARY	KEY ("proyecto","usuario_grupo_acc","item"),
	CONSTRAINT	"apex_usu_item_fk_us_gru_acc"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
  
--#################################################################################################

CREATE TABLE apex_permiso_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
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
	CONSTRAINT	"apex_per_grupo_acc_grupo_fk"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);