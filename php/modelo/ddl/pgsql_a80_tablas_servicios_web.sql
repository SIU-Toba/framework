--**************************************************************************************************
--**************************************************************************************************
--*******************************	SERVICIOS WEB	*******************************************
--**************************************************************************************************
--**************************************************************************************************

--#################################################################################################
CREATE TABLE apex_servicio_web
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: clave_proyecto: proyecto
--: clave_elemento: servicio_web
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  proyecto 			VARCHAR(15)		NOT NULL,
  servicio_web			VARCHAR(50)		NOT NULL,
  descripcion			TEXT			NULL,
  param_to			TEXT			NOT NULL,				--url del servicio
  param_wsa			SMALLINT		NOT NULL DEFAULT 0,		--usar WSA?		 
  CONSTRAINT "apex_servicio_web_pk" PRIMARY KEY("proyecto", "servicio_web"),
  CONSTRAINT "apex_servicio_web_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto"("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_servicio_web_param
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: clave_proyecto: proyecto
--: clave_elemento: servicio_web, parametro
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  proyecto 			VARCHAR(15)	NOT NULL,
  servicio_web			VARCHAR(50)	NOT NULL,
  parametro			TEXT		NOT NULL,
  valor				TEXT		NOT NULL,
  CONSTRAINT "apex_servicio_web_param_pk" PRIMARY KEY("proyecto", "servicio_web", "parametro"),
  CONSTRAINT "apex_servicio_web_param_fk_serv_web" FOREIGN KEY ("proyecto", "servicio_web") REFERENCES "apex_servicio_web"("proyecto", "servicio_web") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################
CREATE SEQUENCE apex_mapeo_rsa_kp_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;
CREATE TABLE apex_mapeo_rsa_kp
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: proyecto, servicio_web
--: dump_where: (	proyecto =	'%%' )
--: clave_proyecto: proyecto
--: clave_elemento: cod_mapeo, servicio_web
--: zona: general
--: desc: Guarda asociacion entre claves RSA y servicio
--: version: 1.0
--:instancia: 1
---------------------------------------------------------------------------------------------------
(
 cod_mapeo			int8	DEFAULT nextval('"apex_mapeo_rsa_kp_seq"'::text)	 NOT NULL,
 proyecto			VARCHAR(15)	NOT NULL, 
 servicio_web			VARCHAR(50)	NOT NULL,
 id				TEXT	NOT NULL,		--Hash
 pub_key				TEXT	NOT NULL,		--ruta archivo
 anulada				SMALLINT	NOT NULL DEFAULT 0,
 CONSTRAINT "apex_mapeo_rsa_kp_pk" PRIMARY KEY("cod_mapeo","proyecto", "servicio_web"),
 CONSTRAINT "apex_mapeo_rsa_kp_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto"("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
 CONSTRAINT "apex_mapeo_rsa_kp_fk_item" FOREIGN KEY ("servicio_web", "proyecto") REFERENCES "apex_item"("item", "proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################
