--**************************************************************************************************
--**************************************************************************************************
--*******************************************	General	*******************************************
--**************************************************************************************************
--**************************************************************************************************

--#################################################################################################

CREATE TABLE	apex_menu_tipos
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
	descripcion					TEXT	NOT NULL,
	archivo						TEXT	NOT NULL,
	soporta_frames				smallint		NULL,
	CONSTRAINT	"apex_menu_tipos_pk" PRIMARY	KEY ("menu")
);


--#################################################################################################

CREATE TABLE			apex_estilo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: proyecto, estilo
--: clave_proyecto: proyecto
--: clave_elemento: estilo
--: zona: general
--: desc: Skins
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	estilo					varchar(40)		NOT NULL,
	descripcion				TEXT	NOT NULL,
	proyecto				varchar(15)		NOT NULL,
	es_css3					smallint		NOT NULL DEFAULT 0,
	paleta					TEXT			NULL,		--Campo serializado de colores
	CONSTRAINT	"apex_estilo_pk" PRIMARY KEY ("estilo", "proyecto"),
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
	descripcion					TEXT	NOT NULL,
	CONSTRAINT	"apex_log_sistema_tipo_pk" PRIMARY KEY ("log_sistema_tipo")
);

--#################################################################################################
CREATE SEQUENCE apex_puntos_montaje_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;
CREATE TABLE apex_puntos_montaje
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: id
--: clave_proyecto: proyecto
--: clave_elemento: id
--: zona: general
--: desc: tabla de puntos de montaje
--: version: 1.6
---------------------------------------------------------------------------------------------------
(
	id									int8				DEFAULT nextval('"apex_puntos_montaje_seq"'::text)	NOT NULL,
	etiqueta							varchar(50)			NOT NULL,
	proyecto							varchar(15)			NOT NULL,
	proyecto_ref						varchar(15)			NULL,
	descripcion							TEXT				NULL,
	path_pm								TEXT				NOT NULL,
	tipo								varchar(20)			NOT NULL,

	UNIQUE								("etiqueta","proyecto"),
	CONSTRAINT	"apex_punto_montaje_pk"	PRIMARY KEY ("id", "proyecto"),
	CONSTRAINT	"apex_proyecto_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
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
	nombre						TEXT	NOT NULL,
	version						varchar(30)		NOT NULL,
	CONSTRAINT	"apex_fuente_datos_motor_pk" PRIMARY KEY ("fuente_datos_motor") 
);
--#################################################################################################

CREATE TABLE apex_fuente_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: fuente_datos
--: clave_proyecto: proyecto
--: clave_elemento: fuente_datos
--: zona: general
--: desc: Bases de datos a	las que se puede acceder
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto					varchar(15)		NOT NULL,
	fuente_datos				varchar(20)		NOT NULL,
	descripcion					TEXT	NOT NULL,
	descripcion_corta			varchar(40)		NULL,	--	NOT NULL,
	fuente_datos_motor			varchar(30)		NULL,
	host						varchar(60)		NULL,
	punto_montaje					int8 NULL,
	subclase_archivo			TEXT 	NULL,
	subclase_nombre				varchar(60) 	NULL,
	orden						smallint		NULL,
	schema						varchar(60)		NULL,	-- Schema postgres por defecto (si aplica)						
	instancia_id				varchar			NULL,
	administrador				varchar			NULL,
	link_instancia				smallint		NULL,
	tiene_auditoria			SMALLINT  NOT NULL  DEFAULT 0,
	parsea_errores		 SMALLINT  NOT NULL  DEFAULT 0,
	permisos_por_tabla			smallint 		NOT NULL DEFAULT 0,
	--- test perfiles (ex db-junk!) ---
	usuario						varchar			NULL,
	clave						varchar			NULL,
	base						varchar			NULL,
	CONSTRAINT	"apex_fuente_datos_pk" PRIMARY KEY ("proyecto","fuente_datos"),
	CONSTRAINT	"apex_fuente_datos_fk_motor" FOREIGN KEY ("fuente_datos_motor") REFERENCES	"apex_fuente_datos_motor" ("fuente_datos_motor") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_fuente_datos_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT "apex_fuente_datos_fk_punto_montaje" FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje"	("proyecto","id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_fuente_datos_schemas
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: fuente_datos, nombre
--: clave_proyecto: proyecto
--: clave_elemento: fuente_datos, nombre
--: zona: general
--: desc: Esquemas pertenecientes a la BD
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto			VARCHAR(15)	NOT NULL,
	fuente_datos		VARCHAR(20)	NOT NULL, 
	nombre			TEXT		NOT NULL,
	principal			SMALLINT	NOT NULL DEFAULT 0,
	CONSTRAINT	"apex_fuente_datos_schemas_pk" PRIMARY KEY ("proyecto", "fuente_datos", "nombre"),
	CONSTRAINT	"apex_fuente_datos_schemas_fk_fuente" FOREIGN KEY ("proyecto", "fuente_datos") REFERENCES "apex_fuente_datos" ("proyecto", "fuente_datos") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

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
	descripcion					TEXT		NOT NULL,
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
	descripcion						TEXT			NULL,
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
	descripcion						TEXT	NOT NULL,
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
--: clave_proyecto: proyecto
--: clave_elemento: elemento_formulario
--: zona: general
--: desc: Elementos de formulario soportados
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	elemento_formulario				varchar(50)		NOT NULL,
	padre							varchar(30)		NULL,
	descripcion						TEXT			NOT NULL,
	parametros						TEXT			NULL,	--	Lista de los parametros	que recibe este EF
	proyecto						varchar(15)		NOT NULL,
	exclusivo_toba					smallint		NULL,
	obsoleto						smallint		NULL DEFAULT 0,
	es_seleccion					smallint		NULL DEFAULT 0,
	es_seleccion_multiple			smallint		NULL DEFAULT 0,
	CONSTRAINT	"apex_elform_pk" PRIMARY KEY ("elemento_formulario"),
	CONSTRAINT	"apex_elform_fk_padre" FOREIGN KEY ("padre") REFERENCES "apex_elemento_formulario"	("elemento_formulario") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_elform_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_solicitud_obs_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: clave_proyecto: proyecto
--: clave_elemento: solicitud_obs_tipo
--: dump_order_by: solicitud_obs_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	solicitud_obs_tipo				varchar(20)		NOT NULL,
	descripcion						TEXT	NOT NULL,
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
--: clave_proyecto: proyecto
--: clave_elemento: pagina_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	pagina_tipo							varchar(20)		NOT NULL,
	descripcion							TEXT	NOT NULL,
	clase_nombre						varchar(40)		NULL,
	clase_archivo						TEXT	NULL,
	include_arriba						TEXT	NULL,
	include_abajo						TEXT	NULL,
	exclusivo_toba						smallint		NULL,
	contexto							TEXT	NULL,	--	Establece variables de CONTEXTO?	Cuales?
	punto_montaje						int8			NULL,
	CONSTRAINT	"apex_pagina_tipo_pk" PRIMARY	KEY ("proyecto","pagina_tipo"),
	CONSTRAINT	"apex_pagina_tipo_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_puntos_montaje" FOREIGN KEY ("proyecto", "punto_montaje")	REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
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
	columna_estilo						int8				DEFAULT nextval('"apex_columna_estilo_seq"'::text)	NOT NULL, 
	css									varchar(40)		NOT NULL,
	descripcion							TEXT	NULL,
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
	columna_formato					int8				DEFAULT nextval('"apex_columna_formato_seq"'::text) NOT NULL, 
	funcion								varchar(60)		NOT NULL,
	archivo								varchar(80)		NULL,
	descripcion							TEXT	NULL,
	descripcion_corta					varchar(40)		NULL,
	parametros							TEXT	NULL,
	estilo_defecto						int8			NOT NULL,
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
  descripcion          TEXT NULL,
  CONSTRAINT "apex_ptos_control__pk" PRIMARY KEY("proyecto", "pto_control")
);

--#################################################################################################
CREATE TABLE apex_ptos_control_param
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: clave_proyecto: proyecto
--: clave_elemento: pto_control
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
--: clave_proyecto: proyecto
--: clave_elemento: pto_control, clase
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  proyecto VARCHAR(15)  NOT NULL,
  pto_control             VARCHAR(30)  NOT NULL,
  clase                   VARCHAR(60)  NOT NULL,
  archivo                 TEXT NULL,
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
--: clave_proyecto: proyecto
--: clave_elemento: consulta_php
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  	proyecto 					VARCHAR(15)  	NOT NULL,
	consulta_php				int8			DEFAULT nextval('"apex_consulta_php_seq"'::text) NOT NULL, 
  	clase                   	VARCHAR(60)  	NOT NULL,
  	archivo_clase              	VARCHAR(60)  	NULL,
  	archivo                 	TEXT			NOT NULL,
  	descripcion                	TEXT			NULL,
	punto_montaje				int8			NULL,
  	CONSTRAINT "apex_consulta_php_pk" PRIMARY KEY("consulta_php","proyecto"),
  	CONSTRAINT "apex_consulta_php_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_puntos_montaje" FOREIGN KEY ("proyecto", "punto_montaje")	REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);


--#################################################################################################

CREATE SEQUENCE apex_tarea_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE	apex_tarea
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto, tarea
--: dump_where: (	proyecto =	'%%' )
--: clave_proyecto: proyecto
--: clave_elemento: tarea
--: zona: nucleo
--: instancia:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  	proyecto 					VARCHAR(15)  	NOT NULL,
	tarea						int8			DEFAULT nextval('"apex_tarea_seq"'::text) NOT NULL, 
	nombre						TEXT		NULL,
	tarea_clase					varchar(120)	NOT NULL,
	tarea_objeto				bytea			NOT NULL,	
	ejecucion_proxima			timestamp		NOT NULL,	
	intervalo_repeticion		interval		NULL,				
  	CONSTRAINT "apex_tarea_pk" PRIMARY KEY("tarea","proyecto"),
  	CONSTRAINT "apex_tarea_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
CREATE INDEX index_apex_tarea_proxima_ejecucion ON apex_tarea(ejecucion_proxima);

--#################################################################################################

CREATE TABLE apex_perfil_datos_set_prueba
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: fuente_datos
--: clave_proyecto: proyecto
--: clave_elemento: fuente_datos, proyecto
--: zona: general
--: desc: Lote de pruebas para los perfiles de datos de la fuente
--: version: 1.0
---------------------------------------------------------------------------------------------------
(		
	proyecto					varchar(15)		NOT NULL,
	fuente_datos				varchar(20)		NOT NULL,
	lote						TEXT			NULL,
	seleccionados				TEXT			NULL, 
	parametros				TEXT			NULL,
	CONSTRAINT	"apex_perfil_datos_set_prueba_pk" PRIMARY KEY ("proyecto","fuente_datos"),
	CONSTRAINT	"apex_perfil_datos_set_prueba_fk_fuente" FOREIGN KEY ("proyecto", "fuente_datos") REFERENCES "apex_fuente_datos" ("proyecto", "fuente_datos") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
