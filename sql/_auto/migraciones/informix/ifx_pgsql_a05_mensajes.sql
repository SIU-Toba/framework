--**************************************************************************************************
--**************************************************************************************************
--**************************************   Manejo de ERRORES  **************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_msg_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	msg_tipo                	 	char(20)    NOT NULL,
   descripcion                	char(255)   NOT NULL,
   icono                      	char(30)  ,
   PRIMARY KEY (msg_tipo)
);
--#################################################################################################


CREATE TABLE apex_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	msg 			    					serial,
   proyecto  							char(15)    NOT NULL,
   msg_tipo       					char(20)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (proyecto,msg),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_patron_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	patron_msg     					serial,
   msg_tipo       					char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   patron_proyecto  					char(15)    NOT NULL,
   patron           					char(20)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (patron_msg),
   FOREIGN KEY (patron_proyecto,patron) REFERENCES apex_patron (proyecto,patron)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_item_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( item_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	item_msg          		   	serial,
   msg_tipo          		   	char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   item_id      						integer        , 
   item_proyecto       		   	char(15)    NOT NULL,
   item                		   	char(60)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (item_msg),
   UNIQUE (indice),
   FOREIGN KEY (item_proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_clase_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	clase_msg      		      	serial,
   msg_tipo            				char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   clase_proyecto   	         	char(15)    NOT NULL,
   clase                      	char(60)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (clase_msg),
   UNIQUE (indice),
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_nucleo_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( nucleo_proyecto = '%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   nucleo_msg        	     		serial,
   msg_tipo       	        		char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   nucleo_proyecto         		char(15)    NOT NULL,
   nucleo                  		char(60)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (nucleo_msg),
   UNIQUE (indice),
   FOREIGN KEY (nucleo_proyecto,nucleo) REFERENCES apex_nucleo (proyecto,nucleo)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################