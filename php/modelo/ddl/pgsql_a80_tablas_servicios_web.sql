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
  tipo				TEXT			NOT NULL default 'soap',
  param_to			TEXT			NULL,				--url del servicio
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
