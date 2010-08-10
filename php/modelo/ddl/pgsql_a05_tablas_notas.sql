--**************************************************************************************************
--**************************************************************************************************
--*******************************************  NOTAS  **********************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_nota_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: nota_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	nota_tipo                  	varchar(20)    	NOT NULL,
--	proyecto					varchar(15)		NULL,
   	descripcion                	TEXT   	NOT NULL,
   	icono                      	varchar(30)    	NULL,
   	CONSTRAINT  "apex_nota_tipo_pk" PRIMARY KEY ("nota_tipo")
--	CONSTRAINT	"apex_nota_tipo_fk_proy" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_nota_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nota
--: clave_proyecto: proyecto
--: clave_elemento: nota
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	nota		            int8           DEFAULT nextval('"apex_nota_seq"'::text) NOT NULL, 
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

CREATE SEQUENCE apex_item_nota_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_item_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: item_nota
--: dump_where: ( item_proyecto = '%%' )
--: clave_proyecto: item_proyecto
--: clave_elemento: item_nota
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	item_nota           		   int8           DEFAULT nextval('"apex_item_nota_seq"'::text) NOT NULL, 
   	nota_tipo           		   varchar(20)    NOT NULL,
   	item_id   						int8        	NULL, 
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
   	CONSTRAINT  "apex_item_nota_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
   	CONSTRAINT  "apex_item_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apex_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_nota_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_objeto_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_nota
--: dump_where: ( objeto_proyecto = '%%' )
--: clave_proyecto: objeto_proyecto
--: clave_elemento: objeto_nota
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_nota             		int8           DEFAULT nextval('"apex_objeto_nota_seq"'::text) NOT NULL, 
	nota_tipo               		varchar(20)    NOT NULL,
	objeto_proyecto   				varchar(15)    NOT NULL,
	objeto                  		int8           NOT NULL,
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
