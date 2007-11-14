
CREATE TABLE apex_nota_tipo
(  
	nota_tipo                  	varchar(20)    	NOT NULL,
   	descripcion                	varchar(255)   	NOT NULL,
   	icono                      	varchar(30)    	NULL,
   	CONSTRAINT   apex_nota_tipo_pk  PRIMARY KEY ( nota_tipo )
) ENGINE=InnoDB;

CREATE TABLE apex_nota
(  
	nota		           integer           auto_increment  NOT NULL, 
	nota_tipo               varchar(20)    NOT NULL,
	proyecto   	   			varchar(15)    NOT NULL,
	usuario_origen          varchar(20)    NULL,
	usuario_destino         varchar(20)    NULL, 
	titulo                  varchar(50)    NULL,
	texto                   text           NULL,
	leido					smallint		NULL,
	bl						smallint		NULL,
	creacion                timestamp  DEFAULT current_timestamp NULL,
	CONSTRAINT   apex_nota_pk  PRIMARY KEY ( nota ),
	CONSTRAINT   apex_nota_fk_usuo  FOREIGN KEY ( usuario_origen ) REFERENCES  apex_usuario  ( usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_nota_fk_usud  FOREIGN KEY ( usuario_destino ) REFERENCES  apex_usuario  ( usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT	 apex_nota_fk_proy  FOREIGN KEY ( proyecto )	REFERENCES  apex_proyecto 	( proyecto ) ON DELETE NO ACTION	ON	UPDATE NO ACTION   ,
	CONSTRAINT   apex_nota_fk_tipo  FOREIGN KEY ( nota_tipo ) REFERENCES  apex_nota_tipo  ( nota_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_item_nota
(  
	item_nota           		  integer           auto_increment  NOT NULL, 
   	nota_tipo           		   varchar(20)    NOT NULL,
    item_id integer NULL, 
   	item_proyecto       		   varchar(15)    NOT NULL,
    item varchar(60) NOT NULL ,
   	usuario_origen      		   varchar(20)    NULL,
   	usuario_destino     		   varchar(20)    NULL, 
   	titulo              		   varchar(50)    NULL,
   	texto               		   text           NULL,
	leido					smallint		NULL,
	bl						smallint		NULL,
   	creacion            		   timestamp  DEFAULT current_timestamp NULL,
   	CONSTRAINT   apex_item_nota_pk    PRIMARY KEY ( item_nota ),
   	CONSTRAINT   apex_item_nota_fk_usuo  FOREIGN KEY ( usuario_origen ) REFERENCES  apex_usuario  ( usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
   	CONSTRAINT   apex_item_nota_fk_usud  FOREIGN KEY ( usuario_destino ) REFERENCES  apex_usuario  ( usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
   	CONSTRAINT   apex_item_nota_fk_item  FOREIGN KEY ( item_proyecto , item ) REFERENCES  apex_item  ( proyecto , item ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
   	CONSTRAINT   apex_item_nota_fk_tipo  FOREIGN KEY ( nota_tipo ) REFERENCES  apex_nota_tipo  ( nota_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_nota
(
	objeto_nota             	integer           auto_increment  NOT NULL, 
	nota_tipo               		varchar(20)    NOT NULL,
	objeto_proyecto   				varchar(15)    NOT NULL,
	objeto                  	integer          NOT NULL,
	usuario_origen          		varchar(20)    NULL,
	usuario_destino         		varchar(20)    NULL, 
	titulo                  		varchar(50)    NULL,
	texto                   		text           NULL,
	bl						smallint		NULL,
	leido							smallint		NULL,
	creacion                	 timestamp  DEFAULT current_timestamp NULL,
	CONSTRAINT   apex_objeto_nota_pk  PRIMARY KEY ( objeto_nota ),
	CONSTRAINT   apex_objeto_nota_fk_usuo  FOREIGN KEY ( usuario_origen ) REFERENCES  apex_usuario  ( usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_objeto_nota_fk_usud  FOREIGN KEY ( usuario_destino ) REFERENCES  apex_usuario  ( usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_objeto_nota_fk_objeto  FOREIGN KEY ( objeto_proyecto , objeto ) REFERENCES  apex_objeto  ( proyecto , objeto ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_objeto_nota_fk_tipo  FOREIGN KEY ( nota_tipo ) REFERENCES  apex_nota_tipo  ( nota_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;
