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
	menu						varchar(15)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	archivo						varchar(255)	NOT NULL,
	soporta_frames				smallint		NULL,
	CONSTRAINT	"apex_menu_pk" PRIMARY	KEY ("menu")
);
--#################################################################################################


CREATE TABLE			apex_proyecto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: proyecto
--: zona: general
--: desc: Tabla maestra	de	proyectos
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	descripcion_corta					varchar(40)		NOT NULL, 
	estilo								varchar(15)		NOT NULL,
	con_frames							smallint		DEFAULT 1 NULL,
	frames_clase						varchar(40)		NULL,
	frames_archivo						varchar(255)	NULL,
	salida_impr_html_c					varchar(40)		NULL,
	salida_impr_html_a					varchar(255)	NULL,
	menu								varchar(15)		NULL,
	path_includes						varchar(255)	NULL,
	path_browser						varchar(255)	NULL,
	administrador						varchar(60)		NULL,
	listar_multiproyecto				smallint		NULL,
	orden								float			NULL,
	palabra_vinculo_std					varchar(30)		NULL,
	version_toba						varchar(15)		NULL,
	requiere_validacion					smallint		NULL,
	usuario_anonimo						varchar(15)		NULL,
	usuario_anonimo_desc				varchar(60)		NULL,
	usuario_anonimo_grupos_acc			varchar(255)	NULL,
	validacion_intentos					smallint		NULL,
	validacion_intentos_min				smallint		NULL,
	validacion_debug					smallint		NULL,
	sesion_tiempo_no_interac_min		smallint		NULL,
	sesion_tiempo_maximo_min			smallint		NULL,
	sesion_subclase						varchar(40)		NULL,
	sesion_subclase_archivo				varchar(255)	NULL,
	contexto_ejecucion_subclase			varchar(40)		NULL,
	contexto_ejecucion_subclase_archivo	varchar(255)	NULL,
	usuario_subclase					varchar(40)		NULL,
	usuario_subclase_archivo			varchar(255)	NULL,
	encriptar_qs						smallint		NULL,
	registrar_solicitud					varchar(1)		NULL,
	registrar_cronometro				varchar(1)		NULL,
	item_inicio_sesion      			varchar(60)		NULL,--NOT
	item_pre_sesion		          		varchar(60)		NULL,--NOT
	item_set_sesion						varchar(60)		NULL,
	log_archivo							smallint		NULL,
	log_archivo_nivel					smallint		NULL,
	fuente_datos						varchar(20)		NULL,--NOT
	version								varchar(20)		NULL,
	version_fecha						date			NULL,
	version_detalle						varchar(255)	NULL,
	version_link						varchar(60)		NULL,
	CONSTRAINT	"apex_proyecto_pk" PRIMARY	KEY ("proyecto"),
	--CONSTRAINT	"apex_proyecto_item_is" FOREIGN	KEY ("proyecto","item_inicio_sesion") REFERENCES	"apex_item"	("proyecto","item") ON DELETE CASCADE ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	--CONSTRAINT	"apex_proyecto_item_ps" FOREIGN	KEY ("proyecto","item_pre_sesion")	REFERENCES "apex_item" ("proyecto","item") ON DELETE CASCADE ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	--CONSTRAINT	"apex_proyecto_fk_fuente" FOREIGN KEY ("proyecto", "fuente_datos") REFERENCES	"apex_fuente_datos" ("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	--CONSTRAINT	"apex_proyecto_fk_estilo" FOREIGN KEY ("estilo") REFERENCES	"apex_estilo" ("estilo") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_proyecto_fk_menu" FOREIGN KEY ("menu") REFERENCES	"apex_menu" ("menu") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE	
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
	estilo					varchar(15)		NOT NULL,
	descripcion				varchar(255)	NOT NULL,
	proyecto				varchar(15)		NOT NULL,
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
	usuario						varchar(30)		NULL,
	clave						varchar(30)		NULL,
	base						varchar(30)		NULL,	--	NOT? ODBC e	instancia no la utilizan...
	administrador				varchar(60)		NULL,
	link_instancia				smallint		NULL,	--	En	vez de abrir una conexion,	utilizar	la	conexion	a la intancia
	instancia_id				varchar(30)	NULL,
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
	recurso_origen				varchar(10)			NOT NULL,
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
	elemento_formulario				varchar(30)		NOT NULL,
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
	exclusivo_toba						smallint			NULL,
	contexto								varchar(255)	NULL,	--	Establece variables de CONTEXTO?	Cuales?
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
	funcion								varchar(40)		NOT NULL,
	archivo								varchar(80)		NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)		NULL,
	parametros							varchar(255)	NULL,
	CONSTRAINT	"apex_columna_formato_pk" PRIMARY KEY ("columna_formato") 
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
  pto_control          VARCHAR(20) NOT NULL,
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
  pto_control              VARCHAR(20) NOT NULL,
  parametro                VARCHAR(60) NULL,
  CONSTRAINT "apex_ptos_ctrl_param__pk" PRIMARY KEY("proyecto", "pto_control", "parametro"),
  CONSTRAINT "apex_ptos_ctrl_param_fk_ptos_ctrl" FOREIGN KEY ("proyecto", "pto_control") REFERENCES "public"."apex_ptos_control"("proyecto", "pto_control") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
  pto_control             VARCHAR(20)  NOT NULL,
  clase                   VARCHAR(60)  NOT NULL,
  archivo                 VARCHAR(255) NULL,
  actua_como              CHAR(1)      DEFAULT 'M' NOT NULL CHECK (actua_como IN ('E','A','M')),
  CONSTRAINT "apex_ptos_ctrl_ctrl__pk" PRIMARY KEY("proyecto", "pto_control", "clase"),
  CONSTRAINT "apex_ptos_ctrl_ctrl_fk_ptos_ctrl" FOREIGN KEY ("proyecto", "pto_control") REFERENCES "public"."apex_ptos_control"("proyecto", "pto_control") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################