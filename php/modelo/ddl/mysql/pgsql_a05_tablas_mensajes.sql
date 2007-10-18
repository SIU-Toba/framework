
CREATE TABLE apex_msg_tipo
(  
	msg_tipo                	 	varchar(20)    NOT NULL,
   descripcion                	varchar(255)   NOT NULL,
   icono                      	varchar(60)    NULL,
   CONSTRAINT   apex_msg_tipo_pk  PRIMARY KEY ( msg_tipo )
) ENGINE=InnoDB;

CREATE TABLE apex_msg
(  
	msg 			    				integer           auto_increment  NOT NULL, 
	indice          					varchar(20)    NOT NULL,
	proyecto  							varchar(15)    NOT NULL,
   msg_tipo       					varchar(20)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  text        NULL,
   mensaje_b	                  text        NULL,
   mensaje_c	                  text        NULL,
   mensaje_customizable          text        NULL,
   CONSTRAINT   apex_msg_pk  PRIMARY KEY ( proyecto , msg ),
   CONSTRAINT   apex_msg_fk_proy  FOREIGN KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
   CONSTRAINT   apex_msg_fk_tipo  FOREIGN KEY ( msg_tipo ) REFERENCES  apex_msg_tipo  ( msg_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_item_msg
(  
	item_msg          		   integer           auto_increment  NOT NULL, 
   msg_tipo          		   	varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   item_id      					integer       	NULL, 
   item_proyecto       		   	varchar(15)    NOT NULL,
    item integer     NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  text        NULL,
   mensaje_b	                  text        NULL,
   mensaje_c	                  text        NULL,
   mensaje_customizable          text        NULL,
	parametro_patron					varchar(100)	NULL,
   CONSTRAINT   apex_item_msg_pk    PRIMARY KEY ( item_msg , item_proyecto ),
   CONSTRAINT   apex_item_msg_uk  UNIQUE ( indice ),
   CONSTRAINT   apex_item_msg_fk_item  FOREIGN KEY ( item_proyecto , item ) REFERENCES  apex_item  ( proyecto , item ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
   CONSTRAINT   apex_item_msg_fk_tipo  FOREIGN KEY ( msg_tipo ) REFERENCES  apex_msg_tipo  ( msg_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_msg
(
   objeto_msg        	     	integer           auto_increment  NOT NULL, 
   msg_tipo       	        		varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   objeto_proyecto         		varchar(15)    NOT NULL,
   objeto                  		varchar(60)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  text        NULL,
   mensaje_b	                  text        NULL,
   mensaje_c	                  text        NULL,
   mensaje_customizable          text        NULL,
	parametro_clase					varchar(100)	NULL,
   CONSTRAINT   apex_objeto_msg_pk  PRIMARY KEY ( objeto_msg ,  objeto_proyecto ),
   CONSTRAINT   apex_objeto_msg_fk_objeto  FOREIGN KEY ( objeto_proyecto , objeto ) REFERENCES  apex_objeto  ( proyecto , objeto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
   CONSTRAINT   apex_objeto_msg_fk_tipo  FOREIGN KEY ( msg_tipo ) REFERENCES  apex_msg_tipo  ( msg_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;
