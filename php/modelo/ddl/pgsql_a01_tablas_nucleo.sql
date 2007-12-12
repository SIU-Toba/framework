--**************************************************************************************************
--**************************************************************************************************
--*******************************************	General	*******************************************
--**************************************************************************************************
--**************************************************************************************************

--#################################################################################################

CREATE TABLE	apex_menu
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: menu
--: zona: general
--: desc: Tipos de menues
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	menu						varchar(40)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	archivo						varchar(255)	NOT NULL,
	soporta_frames				smallint		NULL,
	CONSTRAINT	"apex_menu_pk" PRIMARY	KEY ("menu")
);


--#################################################################################################

CREATE TABLE			apex_estilo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: proyecto, estilo
--: zona: general
--: desc: Skins
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	estilo					varchar(40)		NOT NULL,
	descripcion				varchar(255)	NOT NULL,
	proyecto				varchar(15)		NOT NULL,
	paleta					varchar			NULL,		--Campo serializado de colores
	CONSTRAINT	"apex_estilo_pk" PRIMARY KEY ("estilo"),
	CONSTRAINT	"apex_estilo_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE	
);

--#################################################################################################

CREATE TABLE apex_log_sistema_tipo 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: log_sistema_tipo
--: zona: solicitud
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_sistema_tipo			varchar(20)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	CONSTRAINT	"apex_log_sistema_tipo_pk" PRIMARY KEY ("log_sistema_tipo")
);

--#################################################################################################

CREATE TABLE apex_fuente_datos_motor
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: fuente_datos_motor
--: zona: general
--: desc: DBMS	soportados
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	fuente_datos_motor			varchar(30)		NOT NULL,
	nombre						varchar(255)	NOT NULL,
	version						varchar(30)		NOT NULL,
	CONSTRAINT	"apex_fuente_datos_motor_pk" PRIMARY KEY ("fuente_datos_motor") 
);
--#################################################################################################

CREATE TABLE apex_fuente_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: fuente_datos
--: zona: general
--: desc: Bases de datos a	las que se puede acceder
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto					varchar(15)		NOT NULL,
	fuente_datos				varchar(20)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	descripcion_corta			varchar(40)		NULL,	--	NOT NULL,
	fuente_datos_motor			varchar(30)		NULL,
	host						varchar(60)		NULL,
	usuario						varchar(60)		NULL,
	clave						varchar(60)		NULL,
	base						varchar(60)		NULL,	--	NOT? ODBC e	instancia no la utilizan...
	administrador				varchar(60)		NULL,
	link_instancia				smallint		NULL,	--	En	vez de abrir una conexion,	utilizar	la	conexion	a la intancia
	instancia_id				varchar(60)	NULL,
	subclase_archivo			varchar(255) 	NULL,
	subclase_nombre				varchar(60) 	NULL,
	orden						smallint		NULL,
	CONSTRAINT	"apex_fuente_datos_pk" PRIMARY KEY ("proyecto","fuente_datos"),
	CONSTRAINT	"apex_fuente_datos_fk_motor" FOREIGN KEY ("fuente_datos_motor") REFERENCES	"apex_fuente_datos_motor" ("fuente_datos_motor") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_fuente_datos_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_grafico
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: grafico
--: zona: general
--: desc: Tipo	de	grafico
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	grafico						varchar(30)			NOT NULL,
	descripcion_corta			varchar(40)			NULL,	--NOT
	descripcion					varchar(255)		NOT NULL,
	parametros					varchar				NULL,
	CONSTRAINT	"apex_tipo_grafico_pk" PRIMARY KEY ("grafico") 
);
--#################################################################################################--

CREATE TABLE apex_recurso_origen
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: recurso_origen 
--: zona: general
--: desc: Origen del	recurso:	apex o proyecto
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	recurso_origen				varchar(30)			NOT NULL,
	descripcion					varchar(255)		NOT NULL,
	CONSTRAINT	"apex_rec_origen_pk"	PRIMARY KEY	("recurso_origen") 
);
--#################################################################################################--

CREATE TABLE apex_nivel_acceso
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: nivel_acceso
--: zona: general
--: desc: Categoria organizadora	de	niveles de seguridad	(redobla	la	cualificaciond	e elementos	para fortalecer chequeos)
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	nivel_acceso					smallint			NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar			NULL,
	CONSTRAINT	"apex_nivel_acceso_pk" PRIMARY KEY ("nivel_acceso")
);
--#################################################################################################

CREATE TABLE apex_solicitud_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: solicitud_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_tipo					varchar(20)		NOT NULL,
	descripcion						varchar(255)	NOT NULL,
	descripcion_corta				varchar(40)		NULL,	--	NOT NULL,
	icono								varchar(30)		NULL,
	CONSTRAINT	"apex_sol_tipo_pk" PRIMARY	KEY ("solicitud_tipo")
);
--#################################################################################################

CREATE TABLE apex_elemento_formulario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: elemento_formulario
--: zona: general
--: desc: Elementos de formulario soportados
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	elemento_formulario				varchar(50)		NOT NULL,
	padre							varchar(30)		NULL,
	descripcion						text			NOT NULL,
	parametros						varchar			NULL,	--	Lista de los parametros	que recibe este EF
	proyecto						varchar(15)		NOT NULL,
	exclusivo_toba					smallint		NULL,
	obsoleto						smallint		NULL,
	CONSTRAINT	"apex_elform_pk" PRIMARY KEY ("elemento_formulario"),
	CONSTRAINT	"apex_elform_fk_padre" FOREIGN KEY ("padre") REFERENCES "apex_elemento_formulario"	("elemento_formulario") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_elform_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_solicitud_obs_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: solicitud_obs_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	solicitud_obs_tipo				varchar(20)		NOT NULL,
	descripcion						varchar(255)	NOT NULL,
	criterio						varchar(20)		NOT NULL,
	CONSTRAINT	"apex_sol_obs_tipo_pk" PRIMARY KEY ("proyecto","solicitud_obs_tipo"),
	CONSTRAINT	"apex_sol_obs_tipo_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_pagina_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: pagina_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	pagina_tipo							varchar(20)		NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	clase_nombre						varchar(40)		NULL,
	clase_archivo						varchar(255)	NULL,
	include_arriba						varchar(100)	NULL,
	include_abajo						varchar(100)	NULL,
	exclusivo_toba						smallint		NULL,
	contexto							varchar(255)	NULL,	--	Establece variables de CONTEXTO?	Cuales?
	CONSTRAINT	"apex_pagina_tipo_pk" PRIMARY	KEY ("proyecto","pagina_tipo"),
	CONSTRAINT	"apex_pagina_tipo_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_columna_estilo_seq INCREMENT 1 MINVALUE 0	MAXVALUE	9223372036854775807 CACHE 1;
CREATE TABLE apex_columna_estilo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: columna_estilo
--: zona: general
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	columna_estilo						int4				DEFAULT nextval('"apex_columna_estilo_seq"'::text)	NOT NULL, 
	css									varchar(40)		NOT NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)	  NULL,
	CONSTRAINT	"apex_columna_estilo_pk" PRIMARY	KEY ("columna_estilo") 
);
--###################################################################################################

CREATE SEQUENCE apex_columna_formato_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_columna_formato
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: columna_formato
--: zona: general
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	columna_formato					int4				DEFAULT nextval('"apex_columna_formato_seq"'::text) NOT NULL, 
	funcion								varchar(60)		NOT NULL,
	archivo								varchar(80)		NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)		NULL,
	parametros							varchar(255)	NULL,
	estilo_defecto						int4			NOT NULL,
	CONSTRAINT	"apex_columna_formato_pk" PRIMARY KEY ("columna_formato"),
	CONSTRAINT "apex_columna_formato_fk_estilo" FOREIGN KEY ("estilo_defecto") REFERENCES "apex_columna_estilo"("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################
--################# PUNTOS de CONTROL #############################################################
--#################################################################################################

CREATE TABLE apex_ptos_control 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  proyecto VARCHAR(15) NOT NULL,
  pto_control          VARCHAR(30) NOT NULL,
  descripcion          VARCHAR(255) NULL,
  CONSTRAINT "apex_ptos_control__pk" PRIMARY KEY("proyecto", "pto_control")
);

--#################################################################################################
CREATE TABLE apex_ptos_control_param
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  proyecto VARCHAR(15) NOT NULL,
  pto_control              VARCHAR(30) NOT NULL,
  parametro                VARCHAR(60) NULL,
  CONSTRAINT "apex_ptos_ctrl_param__pk" PRIMARY KEY("proyecto", "pto_control", "parametro"),
  CONSTRAINT "apex_ptos_ctrl_param_fk_ptos_ctrl" FOREIGN KEY ("proyecto", "pto_control") REFERENCES "apex_ptos_control"("proyecto", "pto_control") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################
CREATE TABLE apex_ptos_control_ctrl
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  proyecto VARCHAR(15)  NOT NULL,
  pto_control             VARCHAR(30)  NOT NULL,
  clase                   VARCHAR(60)  NOT NULL,
  archivo                 VARCHAR(255) NULL,
  actua_como              CHAR(1)      DEFAULT 'M' NOT NULL CHECK (actua_como IN ('E','A','M')),
  CONSTRAINT "apex_ptos_ctrl_ctrl__pk" PRIMARY KEY("proyecto", "pto_control", "clase"),
  CONSTRAINT "apex_ptos_ctrl_ctrl_fk_ptos_ctrl" FOREIGN KEY ("proyecto", "pto_control") REFERENCES "apex_ptos_control"("proyecto", "pto_control") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE SEQUENCE apex_consulta_php_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE	apex_consulta_php
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  	proyecto 					VARCHAR(15)  	NOT NULL,
	consulta_php				int4			DEFAULT nextval('"apex_consulta_php_seq"'::text) NOT NULL, 
  	clase                   	VARCHAR(60)  	NOT NULL,
  	archivo                 	VARCHAR(255) 	NOT NULL,
  	descripcion                	VARCHAR(255) 	NULL,
  	CONSTRAINT "apex_consulta_php_pk" PRIMARY KEY("consulta_php","proyecto"),
  	CONSTRAINT "apex_consulta_php_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
