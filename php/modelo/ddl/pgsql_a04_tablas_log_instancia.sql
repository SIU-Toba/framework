--##################################################################################################
--##################################################################################################
--################################  Registro de Solicitudes  #######################################
--##################################################################################################
--##################################################################################################

--%%: zona: solicitudes
--%%: descripcion: Log de las solicitudes generadas
--%%: proyecto: toba


CREATE SEQUENCE apex_solicitud_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_solicitud
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto					varchar(15)		NOT NULL,
	solicitud	 				int8			DEFAULT nextval('__toba_logs__."apex_solicitud_seq"'::text) NOT NULL, 
	solicitud_tipo				varchar(20)		NOT NULL,
	item_proyecto				varchar(15)		NOT NULL,
	item 						varchar(60)		NOT NULL,
   	item_id						int8        	NULL, 
	momento						timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	tiempo_respuesta			float			NULL,
	CONSTRAINT	"apex_log_sol_pk" PRIMARY KEY ("solicitud", "proyecto")
	--CONSTRAINT	"apex_log_sol_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	--CONSTRAINT	"apex_log_sol_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
	--CONSTRAINT	"apex_log_sol_fk_tipo" FOREIGN KEY ("solicitud_tipo") REFERENCES "apex_solicitud_tipo" ("solicitud_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_sesion_browser_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_sesion_browser
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: sesion_browser
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	sesion_browser				int8			DEFAULT nextval('__toba_logs__."apex_sesion_browser_seq"'::text) NOT NULL, 
	proyecto					varchar(15)		NOT NULL,
	usuario						varchar(60) 	NOT NULL,
	ingreso						timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	egreso						timestamp(0) 	without time zone		NULL,
	observaciones				TEXT	NULL,
	php_id						TEXT	NOT NULL,
	ip							varchar(20)		NULL,
	punto_acceso				varchar(80) 	NULL,
	CONSTRAINT	"apex_ses_brw_pk" PRIMARY KEY ("sesion_browser", "proyecto") 
	--CONSTRAINT	"apex_log_sol_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_solicitud_browser
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_browser
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto					varchar(15)		NULL,		-- NOT!
	sesion_browser				int8			NOT NULL,
	solicitud_proyecto			varchar(15)		NULL,		-- NOT!
	solicitud_browser			int8			NOT NULL, 
	ip							varchar(20)		NULL,
	CONSTRAINT	"apex_sol_brw_pk" PRIMARY KEY ("solicitud_proyecto", "solicitud_browser"),
	CONSTRAINT	"apex_sol_brw_fk_sol" FOREIGN KEY ("solicitud_browser", "solicitud_proyecto") REFERENCES "apex_solicitud" ("solicitud", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_sol_brw_fk_sesion" FOREIGN KEY ("sesion_browser","proyecto") REFERENCES "apex_sesion_browser" ("sesion_browser","proyecto") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_solicitud_consola
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_consola
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto					varchar(15)			NULL,		-- NOT!
	solicitud_consola			int8				NOT NULL, 
	usuario						varchar(60)			NOT NULL,
	ip							varchar(20)			NULL,
	llamada						TEXT				NULL,
	entorno						text				NULL,
	CONSTRAINT	"apex_sol_consola_pk" PRIMARY KEY ("solicitud_consola", "proyecto"),
	CONSTRAINT	"apex_sol_consola_fk_sol" FOREIGN KEY ("solicitud_consola", "proyecto") REFERENCES "apex_solicitud" ("solicitud", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_solicitud_cronometro
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: no_requerido
--: dump_order_by: solicitud, marca
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto					varchar(15)			NULL,		-- NOT!
	solicitud	 				int8				NOT NULL,
	marca						smallint			NOT NULL,
	nivel_ejecucion				varchar(15)			NOT NULL,
	texto						TEXT		NULL,
	tiempo						float				NULL,
	CONSTRAINT	"apex_sol_cron_pk" PRIMARY KEY ("solicitud", "proyecto","marca"),
	CONSTRAINT	"apex_sol_cron_fk_sol" FOREIGN KEY ("solicitud", "proyecto") REFERENCES "apex_solicitud" ("solicitud", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_solicitud_observacion_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_solicitud_observacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_observacion
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NULL,		-- NOT!
	solicitud	 					int8				NOT NULL,
	solicitud_observacion			int8				DEFAULT nextval('__toba_logs__."apex_solicitud_observacion_seq"'::text) NOT NULL, 
	solicitud_obs_tipo_proyecto		varchar(15)			NULL,
	solicitud_obs_tipo				varchar(20)			NULL,
	observacion						TEXT				NULL,
	CONSTRAINT	"apex_sol_obs_pk" PRIMARY KEY ("solicitud_observacion"),
	--CONSTRAINT	"apex_sol_obs_fk_sol_ot" FOREIGN KEY ("solicitud_obs_tipo_proyecto","solicitud_obs_tipo") REFERENCES "apex_solicitud_obs_tipo" ("proyecto","solicitud_obs_tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_sol_obs_fk_sol" FOREIGN KEY ("solicitud", "proyecto") REFERENCES "apex_solicitud" ("solicitud", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);

--##################################################################################################
--##################################################################################################
--##################################  Monitoreo y control  #########################################
--##################################################################################################
--##################################################################################################

CREATE SEQUENCE apex_log_sistema_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_log_sistema
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: log_sistema
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_sistema		 			int8				DEFAULT nextval('__toba_logs__."apex_log_sistema_seq"'::text) NOT NULL, 
	momento						timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,
	usuario						varchar(60) 		NULL,
	log_sistema_tipo			varchar(20) 		NOT NULL,
	observaciones				text				NULL,
	CONSTRAINT	"apex_log_sis_pk" PRIMARY KEY ("log_sistema")
	--CONSTRAINT	"apex_log_sis_fk_tipo" FOREIGN KEY ("log_sistema_tipo") REFERENCES "apex_log_sistema_tipo" ("log_sistema_tipo") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################-------------------

CREATE SEQUENCE apex_log_error_login_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_log_error_login
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: log_error_login
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_error_login 			int8				DEFAULT nextval('__toba_logs__."apex_log_error_login_seq"'::text) NOT NULL, 
	momento						timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,
	usuario						TEXT 			NULL,
	clave						TEXT		 		NULL,
	ip							TEXT				NULL,
	gravedad					smallint			NULL,
	mensaje						text				NULL,
	punto_acceso				TEXT		 		NULL,
	CONSTRAINT	"apex_log_error_login_pk" PRIMARY KEY ("log_error_login")
);
--###################################################################################################-------------------

CREATE TABLE apex_log_ip_rechazada
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: ip
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	ip							varchar(255)											NOT NULL,
	momento						timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,
	CONSTRAINT	"apex_ip_rechazada_pk" PRIMARY KEY ("ip")
);

--###################################################################################################-------------------

CREATE SEQUENCE apex_log_tarea_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE	apex_log_tarea
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: log_tarea
--: dump_where: (	proyecto =	'%%' )
--: clave_proyecto: proyecto
--: clave_elemento: log_tarea
--: zona: nucleo
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  	proyecto 					VARCHAR(15)  	NOT NULL,
	log_tarea					int8			DEFAULT nextval('__toba_logs__."apex_log_tarea_seq"'::text) NOT NULL, 
	tarea						int8			NOT NULL,	
	nombre						TEXT		NULL,
	tarea_clase					varchar(120)	NOT NULL,
	tarea_objeto				bytea			NOT NULL,	
	ejecucion					timestamp		NOT NULL,
  	CONSTRAINT "apex_log_tarea_pk"  PRIMARY KEY ("log_tarea","proyecto")
  	--CONSTRAINT "apex_log_tarea_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE SEQUENCE apex_log_objeto_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_log_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: log_objeto
--: dump_where: objeto_proyecto ='%%'
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_objeto							int8			DEFAULT nextval('__toba_logs__."apex_log_objeto_seq"'::text) NOT NULL, 
	momento								timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	usuario								varchar(60) 	NULL,
	objeto_proyecto          			varchar(15)  	NOT NULL,
	objeto								int8			NULL,
	item								varchar(60)		NULL,
	observacion							varchar			NULL,
	CONSTRAINT	"apex_log_objeto_pk" PRIMARY KEY ("log_objeto")
);

--#################################################################################################

CREATE TABLE apex_solicitud_web_service
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto			VARCHAR(15) NOT NULL, 
   solicitud			BIGINT	NOT NULL, 
   metodo			TEXT	NULL, 
   ip				VARCHAR(20)	NULL, 
   CONSTRAINT "apex_solicitud_web_service_pk" PRIMARY KEY ("solicitud","proyecto" ), 
   CONSTRAINT "apex_sol_web_service_solicitud_fk" FOREIGN KEY ("solicitud","proyecto" ) REFERENCES "apex_solicitud" ( "solicitud","proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE
); 