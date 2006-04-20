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
	solicitud	 				int4			DEFAULT nextval('"apex_solicitud_seq"'::text) NOT NULL, 
	solicitud_tipo				varchar(20)		NOT NULL,
	item_proyecto				varchar(15)		NOT NULL,
	item 						varchar(60)		NOT NULL,
   	item_id						int4        	NULL, 
	momento						timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	tiempo_respuesta			float			NULL,
	CONSTRAINT	"apex_log_sol_pk" PRIMARY KEY ("proyecto", "solicitud"),
	CONSTRAINT	"apex_log_sol_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_log_sol_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_log_sol_fk_tipo" FOREIGN KEY ("solicitud_tipo") REFERENCES "apex_solicitud_tipo" ("solicitud_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
	sesion_browser				int4			DEFAULT nextval('"apex_sesion_browser_seq"'::text) NOT NULL, 
	proyecto					varchar(15)		NOT NULL,
	usuario						varchar(20) 	NOT NULL,
	ingreso						timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	egreso						timestamp(0) 	without time zone		NULL,
	observaciones				varchar(255)	NULL,
	php_id						varchar(100)	NOT NULL,
	ip							varchar(20)		NULL,
	punto_acceso				varchar(80) 	NULL,
	CONSTRAINT	"apex_ses_brw_pk" PRIMARY KEY ("sesion_browser", "proyecto"), 
	CONSTRAINT	"apex_log_sol_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_ses_brw_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_solicitud_browser
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_browser
--: dump_from: apex_solicitud
--: dump_where: (apex_solicitud.solicitud = dd.solicitud_browser) AND (apex_solicitud.proyecto ='%%')
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_browser			int4			NOT NULL, 
	sesion_browser				int4			NOT NULL,
	ip							varchar(20)		NULL,
	CONSTRAINT	"apex_sol_brw_pk" PRIMARY KEY ("solicitud_browser")
--  Estos constraint no funcionan porque debe estar tambien el proyecto en esta tabla
--	CONSTRAINT	"apex_sol_brw_fk_sol" FOREIGN KEY ("solicitud_browser") REFERENCES "apex_solicitud" ("solicitud") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE,
--	CONSTRAINT	"apex_sol_brw_fk_sesion" FOREIGN KEY ("sesion_browser") REFERENCES "apex_sesion_browser" ("sesion_browser") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_solicitud_wddx
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_wddx
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_wddx) AND (apex_solicitud.proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_wddx				int4			NOT NULL, 
	usuario						varchar(20) 	NOT NULL,
	ip							varchar(20)		NULL,
	instancia					varchar(80) 	NOT NULL,
	instancia_usuario			varchar(20) 	NOT NULL,
	paquete						text			NULL,
	CONSTRAINT	"apex_sol_wddx_pk" PRIMARY KEY ("solicitud_wddx"),
--  Este constraint no funcionan porque debe estar tambien el proyecto en esta tabla
--	CONSTRAINT	"apex_sol_wddx_fk_sol" FOREIGN KEY ("solicitud_wddx") REFERENCES "apex_solicitud" ("solicitud") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE, 
--	CONSTRAINT	"apex_sol_wddx_fk_sol" FOREIGN KEY ("solicitud_wddx") REFERENCES "apex_solicitud" ("solicitud") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_sol_wddx_fk_usu" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
--	CONSTRAINT	"apex_sol_wddx_fk_usu" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_solicitud_consola
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_consola
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_consola) AND (apex_solicitud.proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_consola			int4				NOT NULL, 
	usuario						varchar(20)			NOT NULL,
	ip							varchar(20)			NULL,
	llamada						varchar				NULL,
	entorno						text				NULL,
	CONSTRAINT	"apex_sol_consola_pk" PRIMARY KEY ("solicitud_consola"),
--  Este constraint no funcionan porque debe estar tambien el proyecto en esta tabla	
--	CONSTRAINT	"apex_sol_consola_fk_sol" FOREIGN KEY ("solicitud_consola") REFERENCES "apex_solicitud" ("solicitud") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE,
--	CONSTRAINT	"apex_sol_consola_fk_sol" FOREIGN KEY ("solicitud_consola") REFERENCES "apex_solicitud" ("solicitud") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_sol_consola_fk_usu" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
--	CONSTRAINT	"apex_sol_consola_fk_usu" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_solicitud_cronometro
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud	 				int4				NOT NULL,
	marca						smallint			NOT NULL,
	nivel_ejecucion				varchar(15)			NOT NULL,
	texto						varchar(120)		NULL,
	tiempo						float				NULL,
	CONSTRAINT	"apex_sol_cron_pk" PRIMARY KEY ("solicitud","marca")
--	CONSTRAINT	"apex_sol_cron_fk_nivel" FOREIGN KEY ("nivel_ejecucion") REFERENCES "apex_nivel_ejecucion" ("nivel_ejecucion") ON DELETE NO ACTION ON UPDATE NO ACTION  DEFERRABLE INITIALLY IMMEDIATE
--  Este constraint no funcionan porque debe estar tambien el proyecto en esta tabla		
--	CONSTRAINT	"apex_sol_cron_fk_sol" FOREIGN KEY ("solicitud") REFERENCES "apex_solicitud" ("solicitud") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE
--	CONSTRAINT	"apex_sol_cron_fk_sol" FOREIGN KEY ("solicitud") REFERENCES "apex_solicitud" ("solicitud") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_solicitud_observacion_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_solicitud_observacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_observacion
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_observacion) AND (apex_solicitud.proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_observacion			int4				DEFAULT nextval('"apex_solicitud_observacion_seq"'::text) NOT NULL, 
	solicitud_obs_tipo_proyecto		varchar(15)			NULL,
	solicitud_obs_tipo				varchar(20)			NULL,
	solicitud	 					int4				NOT NULL,
	observacion						varchar				NULL,
	CONSTRAINT	"apex_sol_obs_pk" PRIMARY KEY ("solicitud_observacion"),
	CONSTRAINT	"apex_sol_obs_fk_sol_ot" FOREIGN KEY ("solicitud_obs_tipo_proyecto","solicitud_obs_tipo") REFERENCES "apex_solicitud_obs_tipo" ("proyecto","solicitud_obs_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
--  Este constraint no funcionan porque debe estar tambien el proyecto en esta tabla		
--	CONSTRAINT	"apex_sol_obs_fk_sol" FOREIGN KEY ("solicitud") REFERENCES "apex_solicitud" ("solicitud") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE
--	CONSTRAINT	"apex_sol_obs_fk_sol" FOREIGN KEY ("solicitud") REFERENCES "apex_solicitud" ("solicitud") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--###################################################################################################

CREATE SEQUENCE apex_solicitud_obj_obs_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_solicitud_obj_observacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_obj_observacion
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_obj_observacion			int4			DEFAULT nextval('"apex_solicitud_obj_obs_seq"'::text) NOT NULL, 
	solicitud_obj_obs_tipo				varchar(20)		NULL,
	solicitud		 					int4			NOT NULL,
   objeto_proyecto          			varchar(15)  	NOT NULL,
	objeto								int4			NOT NULL,
	observacion							varchar			NULL,
	CONSTRAINT	"apex_sol_obj_obs_pk" PRIMARY KEY ("solicitud_obj_observacion"),
	CONSTRAINT	"apex_sol_obj_obs_fk_sol_ot" FOREIGN KEY ("solicitud_obj_obs_tipo") REFERENCES "apex_solicitud_obj_obs_tipo" ("solicitud_obj_obs_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_sol_obj_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
--  Este constraint no funcionan porque debe estar tambien el proyecto en esta tabla		
--	CONSTRAINT	"apex_sol_obj_obs_fk_sol" FOREIGN KEY ("solicitud") REFERENCES "apex_solicitud" ("solicitud") ON DELETE CASCADE ON UPDATE CASCADE  DEFERRABLE INITIALLY IMMEDIATE
--	CONSTRAINT	"apex_sol_obj_obs_fk_sol" FOREIGN KEY ("solicitud") REFERENCES "apex_solicitud" ("solicitud") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--###################################################################################################

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
	log_objeto							int4			DEFAULT nextval('"apex_log_objeto_seq"'::text) NOT NULL, 
	momento								timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	usuario								varchar(20) 	NULL,
	objeto_proyecto          			varchar(15)  	NOT NULL,
	objeto								int4			NULL,
	item								varchar(60)		NULL,
	observacion							varchar			NULL,
	CONSTRAINT	"apex_log_objeto_pk" PRIMARY KEY ("log_objeto")
--	CONSTRAINT	"apex_log_sis_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
--	CONSTRAINT	"apex_log_objeto_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
	log_sistema		 			int4				DEFAULT nextval('"apex_log_sistema_seq"'::text) NOT NULL, 
	momento						timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,
	usuario						varchar(20) 		NULL,
	log_sistema_tipo			varchar(20) 		NOT NULL,
	observaciones				text				NULL,
	CONSTRAINT	"apex_log_sis_pk" PRIMARY KEY ("log_sistema"),
	CONSTRAINT	"apex_log_sis_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
--	CONSTRAINT	"apex_log_sis_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_log_sis_fk_tipo" FOREIGN KEY ("log_sistema_tipo") REFERENCES "apex_log_sistema_tipo" ("log_sistema_tipo") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
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
	log_error_login 			int4				DEFAULT nextval('"apex_log_error_login_seq"'::text) NOT NULL, 
	momento						timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,
	usuario						varchar(20) 		NULL,
	clave						varchar(20) 		NULL,
	ip							varchar(20)			NULL,
	gravedad					smallint			NULL,
	mensaje						text				NULL,
	punto_acceso				varchar(80) 		NULL,
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
	ip								varchar(20)								NOT NULL,
	momento						timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,
	CONSTRAINT	"apex_ip_rechazada_pk" PRIMARY KEY ("ip")
);

--###################################################################################################-------------------
