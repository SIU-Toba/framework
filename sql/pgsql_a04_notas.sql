--**************************************************************************************************
--**************************************************************************************************
--*******************************************  NOTAS  **********************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_nota_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: nota_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	nota_tipo                  	varchar(20)    	NOT NULL,
--	proyecto					varchar(15)		NULL,
   	descripcion                	varchar(255)   	NOT NULL,
   	icono                      	varchar(30)    	NULL,
   	CONSTRAINT  "apex_nota_tipo_pk" PRIMARY KEY ("nota_tipo")
--	CONSTRAINT	"apex_nota_tipo_fk_proy" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nota
--: zona: central
--: desc:
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(  
	nota		            int4           DEFAULT nextval('"apex_nota_seq"'::text) NOT NULL, 
	nota_tipo               varchar(20)    NOT NULL,
	proyecto   	   			varchar(15)    NOT NULL,
	usuario_origen          varchar(20)    NULL,
	usuario_destino         varchar(20)    NULL, 
	titulo                  varchar(50)    NULL,
	texto                   text           NULL,
	leido					smallint		NULL,
	bl						smallint		NULL,
	creacion                timestamp(0)   without time zone DEFAULT current_timestamp NULL,
	CONSTRAINT  "apex_nota_pk" PRIMARY KEY ("nota"),
	CONSTRAINT  "apex_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_nota_fk_proy" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apex_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_patron_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_patron_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: patron_nota
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(  
	patron_nota             int4           DEFAULT nextval('"apex_patron_nota_seq"'::text) NOT NULL, 
	nota_tipo               varchar(20)    NOT NULL,
	patron_proyecto   	   varchar(15)    NOT NULL,
	patron                  varchar(20)    NOT NULL,
	usuario_origen          varchar(20)    NULL,
	usuario_destino         varchar(20)    NULL, 
	titulo                  varchar(50)    NULL,
	texto                   text           NULL,
	leido					smallint		NULL,
	bl						smallint		NULL,
	creacion                timestamp(0)   without time zone DEFAULT current_timestamp NULL,
	CONSTRAINT  "apex_patron_nota_pk" PRIMARY KEY ("patron_nota"),
	CONSTRAINT  "apex_patron_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_patron_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_patron_nota_fk_patron" FOREIGN KEY ("patron_proyecto","patron") REFERENCES "apex_patron" ("proyecto","patron") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_patron_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apex_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_item_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_item_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: item_nota
--: dump_where: ( item_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(  
	item_nota           		   int4           DEFAULT nextval('"apex_item_nota_seq"'::text) NOT NULL, 
   	nota_tipo           		   varchar(20)    NOT NULL,
   	item_id   						int4        	NULL, 
   	item_proyecto       		   varchar(15)    NOT NULL,
   	item                		   varchar(60)    NOT NULL,
   	usuario_origen      		   varchar(20)    NULL,
   	usuario_destino     		   varchar(20)    NULL, 
   	titulo              		   varchar(50)    NULL,
   	texto               		   text           NULL,
	leido					smallint		NULL,
	bl						smallint		NULL,
   	creacion            		   timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   	CONSTRAINT  "apex_item_nota_pk"   PRIMARY KEY ("item_nota"),
   	CONSTRAINT  "apex_item_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   	CONSTRAINT  "apex_item_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   	CONSTRAINT  "apex_item_nota_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   	CONSTRAINT  "apex_item_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apex_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_clase_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_clase_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase_nota
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(  
	clase_nota       		      int4           DEFAULT nextval('"apex_clase_nota_seq"'::text) NOT NULL, 
   nota_tipo            		varchar(20)    NOT NULL,
   clase_proyecto   	         varchar(15)    NOT NULL,
   clase                      varchar(60)    NOT NULL,
   usuario_origen             varchar(20)    NULL,
   usuario_destino            varchar(20)    NULL, 
   titulo                     varchar(50)    NULL,
   texto                      text           NULL,
	bl						smallint		NULL,
	leido					smallint		NULL,
   creacion                   timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apex_clase_nota_pk"  PRIMARY KEY ("clase_nota"),
   CONSTRAINT  "apex_clase_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_clase_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_clase_nota_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_clase_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apex_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_objeto_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_nota
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(
	objeto_nota             		int4           DEFAULT nextval('"apex_objeto_nota_seq"'::text) NOT NULL, 
	nota_tipo               		varchar(20)    NOT NULL,
	objeto_proyecto   				varchar(15)    NOT NULL,
	objeto                  		int4           NOT NULL,
	usuario_origen          		varchar(20)    NULL,
	usuario_destino         		varchar(20)    NULL, 
	titulo                  		varchar(50)    NULL,
	texto                   		text           NULL,
	bl						smallint		NULL,
	leido							smallint		NULL,
	creacion                		timestamp(0)   without time zone DEFAULT current_timestamp NULL,
	CONSTRAINT  "apex_objeto_nota_pk" PRIMARY KEY ("objeto_nota"),
	CONSTRAINT  "apex_objeto_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_nota_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apex_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_nucleo_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_nucleo_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nucleo_nota
--: dump_where: ( nucleo_proyecto = '%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
--: instancia:	1
---------------------------------------------------------------------------------------------------
(
   nucleo_nota             		int4           DEFAULT nextval('"apex_nucleo_nota_seq"'::text) NOT NULL, 
   nota_tipo               		varchar(20)    NOT NULL,
   nucleo_proyecto         		varchar(15)    NOT NULL,
   nucleo                  		varchar(60)    NOT NULL,
   usuario_origen          		varchar(20)    NULL,
   usuario_destino         		varchar(20)    NULL, 
   titulo                  		varchar(50)    NULL,
   texto                   		text           NULL,
	bl						smallint		NULL,
	leido					smallint		NULL,
   creacion                		timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apex_nucleo_nota_pk" PRIMARY KEY ("nucleo_nota"),
   CONSTRAINT  "apex_nucleo_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_nucleo_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_nucleo_nota_fk_nucleo" FOREIGN KEY ("nucleo_proyecto","nucleo") REFERENCES "apex_nucleo" ("proyecto","nucleo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_nucleo_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apex_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################
