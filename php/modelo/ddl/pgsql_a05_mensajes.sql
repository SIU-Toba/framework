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
   icono                      	varchar(30)    NULL,
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
	msg 			    					int4           DEFAULT nextval('"apex_msg_seq"'::text) NOT NULL, 
	indice          					varchar(20)    NOT NULL,
	proyecto  							varchar(15)    NOT NULL,
   msg_tipo       					varchar(20)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  varchar        NULL,
   mensaje_b	                  varchar        NULL,
   mensaje_c	                  varchar        NULL,
   mensaje_customizable          varchar        NULL,
   CONSTRAINT  "apex_msg_pk" PRIMARY KEY ("proyecto","msg"),
--   CONSTRAINT  "apex_msg_msg_uk" UNIQUE ("indice"),
   CONSTRAINT  "apex_msg_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_patron_msg_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_patron_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: patron_msg
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	patron_msg     					int4           DEFAULT nextval('"apex_patron_msg_seq"'::text) NOT NULL, 
   msg_tipo       					varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   patron_proyecto  					varchar(15)    NOT NULL,
   patron           					varchar(20)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  varchar        NULL,
   mensaje_b	                  varchar        NULL,
   mensaje_c	                  varchar        NULL,
   mensaje_customizable          varchar        NULL,
--   CONSTRAINT  "apex_patron_msg_uk" UNIQUE ("indice"),
   CONSTRAINT  "apex_patron_msg_pk" PRIMARY KEY ("patron_msg", "patron_proyecto"),
   CONSTRAINT  "apex_patron_msg_fk_patron" FOREIGN KEY ("patron_proyecto","patron") REFERENCES "apex_patron" ("proyecto","patron") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_patron_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
	item_msg          		   	int4           DEFAULT nextval('"apex_item_msg_seq"'::text) NOT NULL, 
   msg_tipo          		   	varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   item_id      						int4        	NULL, 
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
   CONSTRAINT  "apex_item_msg_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_item_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_clase_msg_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_clase_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase_msg
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	clase_msg      		      	int4           DEFAULT nextval('"apex_clase_msg_seq"'::text) NOT NULL, 
   msg_tipo            				varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   clase_proyecto   	         	varchar(15)    NOT NULL,
   clase                      	varchar(60)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  varchar        NULL,
   mensaje_b	                  varchar        NULL,
   mensaje_c	                  varchar        NULL,
   mensaje_customizable          varchar        NULL,
   CONSTRAINT  "apex_clase_msg_pk"  PRIMARY KEY ("clase_msg", "clase_proyecto"),
--   CONSTRAINT  "apex_clase_msg_uk" UNIQUE ("indice"),
   CONSTRAINT  "apex_clase_msg_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_clase_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
   objeto_msg        	     		int4           DEFAULT nextval('"apex_objeto_msg_seq"'::text) NOT NULL, 
   msg_tipo       	        		varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   objeto_proyecto         		varchar(15)    NOT NULL,
   objeto                  		varchar(60)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  varchar        NULL,
   mensaje_b	                  varchar        NULL,
   mensaje_c	                  varchar        NULL,
   mensaje_customizable          varchar        NULL,
	parametro_clase					varchar(100)	NULL,
   CONSTRAINT  "apex_objeto_msg_pk" PRIMARY KEY ("objeto_msg", "objeto_proyecto"),
--   CONSTRAINT  "apex_objeto_msg_uk" UNIQUE ("indice"),
   CONSTRAINT  "apex_objeto_msg_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_objeto_msg_fk_tipo" FOREIGN KEY ("msg_tipo") REFERENCES "apex_msg_tipo" ("msg_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################