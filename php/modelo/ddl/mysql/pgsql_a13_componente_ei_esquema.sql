
CREATE TABLE apex_objeto_esquema
(
   objeto_esquema_proyecto   	varchar(15)		NOT NULL,
   objeto_esquema            integer		NOT NULL,
   parser	            	   	varchar(30)  	NULL, 
   descripcion            	   	varchar(80)  	NULL,
   dot		               		text			NULL, 
   debug						smallint		NULL,
   formato						varchar(15)		NULL,
   modelo_ejecucion				varchar(15)		NULL,
   modelo_ejecucion_cache		smallint		NULL, 
   tipo_incrustacion			varchar(15)		NULL, 
   ancho						varchar(10)		NULL,
   alto							varchar(10)		NULL,
   dirigido						smallint		DEFAULT 1 NULL,
  nosql						text			NULL,
   CONSTRAINT   apex_objeto_esquema_pk  PRIMARY KEY ( objeto_esquema_proyecto , objeto_esquema ),
   CONSTRAINT   apex_objeto_esquema_fk_objeto   FOREIGN KEY ( objeto_esquema_proyecto , objeto_esquema ) REFERENCES    apex_objeto  ( proyecto , objeto ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;
