--**************************************************************************************************
--**************************************************************************************************
--*******************************************  NOTAS  **********************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_nota_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	nota_tipo                  char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   icono                      char(30)  ,
   PRIMARY KEY (nota_tipo)
);
--#################################################################################################


CREATE TABLE apex_patron_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	patron_nota             serial,
   nota_tipo               char(20)    NOT NULL,
   patron_proyecto   	   char(15)    NOT NULL,
   patron                  char(20)    NOT NULL,
   usuario_origen          char(20)  ,
   usuario_destino         char(20)  , 
   titulo                  char(50)  ,
   texto                   text         ,
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (patron_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (patron_proyecto,patron) REFERENCES apex_patron (proyecto,patron)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_item_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( item_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	item_nota           		   serial,
   nota_tipo           		   char(20)    NOT NULL,
   item_id   						integer        , 
   item_proyecto       		   char(15)    NOT NULL,
   item                		   char(60)    NOT NULL,
   usuario_origen      		   char(20)  ,
   usuario_destino     		   char(20)  , 
   titulo              		   char(50)  ,
   texto               		   text         ,
   creacion            		   datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (item_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (item_proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_clase_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	clase_nota       		      serial,
   nota_tipo            		char(20)    NOT NULL,
   clase_proyecto   	         char(15)    NOT NULL,
   clase                      char(60)    NOT NULL,
   usuario_origen             char(20)  ,
   usuario_destino            char(20)  , 
   titulo                     char(50)  ,
   texto                      text         ,
   creacion                   datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (clase_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_objeto_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_nota             		serial,
   nota_tipo               		char(20)    NOT NULL,
   objeto_proyecto   				char(15)    NOT NULL,
   objeto                  		integer           NOT NULL,
   usuario_origen          		char(20)  ,
   usuario_destino         		char(20)  , 
   titulo                  		char(50)  ,
   texto                   		text         ,
   creacion                		datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (objeto_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (objeto_proyecto,objeto) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_nucleo_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( nucleo_proyecto = '%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   nucleo_nota             		serial,
   nota_tipo               		char(20)    NOT NULL,
   nucleo_proyecto         		char(15)    NOT NULL,
   nucleo                  		char(60)    NOT NULL,
   usuario_origen          		char(20)  ,
   usuario_destino         		char(20)  , 
   titulo                  		char(50)  ,
   texto                   		text         ,
   creacion                		datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (nucleo_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (nucleo_proyecto,nucleo) REFERENCES apex_nucleo (proyecto,nucleo)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################
