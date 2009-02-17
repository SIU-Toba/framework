--**************************************************************************************************
--**************************************************************************************************
--**************************************   Manejo de ERRORES  **************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_msg_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: msg_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	msg_tipo                	 	varchar(20)    NOT NULL,
   descripcion                	varchar(255)   NOT NULL,
   icono                      	varchar(60)    NULL,
   CONSTRAINT  "apex_msg_tipo_pk" PRIMARY KEY ("msg_tipo")
);
--#################################################################################################

CREATE SEQUENCE apex_msg_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: msg
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	msg 			    					int8           DEFAULT nextval('"apex_msg_seq"'::text) NOT NULL, 
	indice          					varchar(20)    NOT NULL,	
	proyecto  							varchar(15)    NOT NULL,
   msg_tipo       					varchar(20)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  varchar        NULL,
   mensaje_b	                  varchar        NULL,
   mensaje_c	                  varchar        NULL,
   mensaje_customizable          varchar        NULL,
   CONSTRAINT  "apex_msg_pk" PRIMARY KEY ("msg", "proyecto"),
--   CONSTRAINT  "apex_msg_msg_uk" UNIQUE ("indice"),
   CONSTRAINT  "apex_msg_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_item_msg_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_item_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: item_msg
--: dump_where: ( item_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	item_msg          		   	int8           DEFAULT nextval('"apex_item_msg_seq"'::text) NOT NULL, 
   msg_tipo          		   	varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   item_id      						int8        	NULL, 
   item_proyecto       		   	varchar(15)    NOT NULL,
   item                		   	varchar(60)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  varchar        NULL,
   mensaje_b	                  varchar        NULL,
   mensaje_c	                  varchar        NULL,
   mensaje_customizable          varchar        NULL,
	parametro_patron					varchar(100)	NULL,
   CONSTRAINT  "apex_item_msg_pk"   PRIMARY KEY ("item_msg","item_proyecto"),
   CONSTRAINT  "apex_item_msg_uk" UNIQUE ("indice"),
   CONSTRAINT  "apex_item_msg_fk_item" FOREIGN KEY ("item", "item_proyecto") REFERENCES "apex_item" ("item", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_item_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_msg_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_objeto_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_msg
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   	objeto_msg        	     		int8           	DEFAULT nextval('"apex_objeto_msg_seq"'::text) NOT NULL, 
   	msg_tipo       	        		varchar(20)    	NOT NULL,
	indice          				varchar(20)    	NOT NULL,
   	objeto_proyecto         		varchar(15)    	NOT NULL,	
   	objeto                  		int8	    	NOT NULL,
   	descripcion_corta            	varchar(50)    	NULL,
   	mensaje_a	             	    varchar        	NULL,
   	mensaje_b	             	    varchar        	NULL,
   	mensaje_c	             	    varchar        	NULL,
   	mensaje_customizable     	    varchar        	NULL,
	parametro_clase					varchar(100)	NULL,
   	CONSTRAINT  "apex_objeto_msg_pk" PRIMARY KEY ("objeto_msg", "objeto_proyecto"),
--   CONSTRAINT  "apex_objeto_msg_uk" UNIQUE ("indice"),
   	CONSTRAINT  "apex_objeto_msg_fk_objeto" FOREIGN KEY ("objeto", "objeto_proyecto") REFERENCES "apex_objeto" ("objeto", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   	CONSTRAINT  "apex_objeto_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################