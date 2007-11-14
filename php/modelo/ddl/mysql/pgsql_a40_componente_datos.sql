
CREATE TABLE apex_admin_persistencia
(
	ap							integer			 auto_increment  		NOT NULL, 
	clase							varchar(60)			NOT	NULL,
	archivo							varchar(120)			NOT	NULL,
	descripcion						varchar(60)			NOT	NULL,
	categoria						varchar(20)			NULL,		
	CONSTRAINT	 apex_admin_persistencia_pk  PRIMARY	KEY ( ap )
) ENGINE=InnoDB;

CREATE TABLE apex_tipo_datos
(
	tipo							varchar(1)			NOT NULL,
	descripcion						varchar(50)			NOT	NULL,
	CONSTRAINT	 apex_tipo_datos_pk  PRIMARY	KEY ( tipo )
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_db_registros
(
	objeto_proyecto  				varchar(15)		NOT NULL,
	objeto      	    	 	integer		NOT NULL,
	max_registros					smallint		NULL,
	min_registros					smallint		NULL,
	ap							integer		NULL,
	ap_clase						varchar(60)		NULL,
	ap_archivo						varchar(120)	NULL,
	tabla 							varchar(120)	NULL,
	alias 							varchar(60)		NULL,
	modificar_claves				smallint		NULL,
	fuente_datos_proyecto			varchar(15)		NULL,	
	fuente_datos					varchar(20)		NULL,	
	CONSTRAINT   apex_objeto_dbr_pk  PRIMARY KEY ( objeto ,  objeto_proyecto ),
	CONSTRAINT	 apex_objeto_dbr_uq_tabla  UNIQUE ( fuente_datos ,  tabla ),
	CONSTRAINT   apex_objeto_dbr_fk_ap   FOREIGN KEY ( ap ) REFERENCES    apex_admin_persistencia  ( ap ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_objeto_dbr_fk_objeto   FOREIGN KEY ( objeto ,  objeto_proyecto ) REFERENCES    apex_objeto  ( objeto ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_objeto_dbr_fk_fuente   FOREIGN KEY ( fuente_datos_proyecto , fuente_datos ) REFERENCES    apex_fuente_datos  ( proyecto , fuente_datos ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_db_registros_col
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                integer      		NOT NULL,
	col_id						integer		 auto_increment  		NOT NULL, 
	columna		    				varchar(120)		NOT NULL, 
	tipo							varchar(1)		NULL,
	pk								smallint 		NULL,
	secuencia		    			varchar(120)		NULL, 
	largo							smallint		NULL,
	no_nulo							smallint 		NULL,
	no_nulo_db						smallint 		NULL,
	externa							smallint		NULL,
	CONSTRAINT   apex_obj_dbr_col_pk  PRIMARY KEY ( col_id ,  objeto ,  objeto_proyecto ),
	CONSTRAINT   apex_obj_dbr_col_fk_tipo  FOREIGN KEY ( tipo ) REFERENCES  apex_tipo_datos  ( tipo ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_obj_dbr_col_fk_objeto_dbr  FOREIGN KEY ( objeto ,  objeto_proyecto ) REFERENCES  apex_objeto_db_registros  ( objeto ,  objeto_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;


CREATE TABLE apex_objeto_db_registros_ext
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                integer      		NOT NULL,
	externa_id					integer		 auto_increment  		NOT NULL, 
	tipo							varchar(3)		NOT NULL,
	sincro_continua					smallint		NULL,
	metodo							varchar(100)	NULL,
	clase							varchar(100)	NULL,
	include							varchar(255)	NULL,
nosql							text			NULL,
	CONSTRAINT   apex_obj_dbr_ext_pk  PRIMARY KEY ( externa_id ,  objeto ,  objeto_proyecto ),
	CONSTRAINT   apex_obj_dbr_ext_fk_objeto_dbr  FOREIGN KEY ( objeto ,  objeto_proyecto ) REFERENCES  apex_objeto_db_registros  ( objeto ,  objeto_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   	
) ENGINE=InnoDB;


CREATE TABLE apex_objeto_db_registros_ext_col
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                integer      		NOT NULL,
	externa_id					integer		NOT NULL,
	col_id						integer		NOT NULL,
	es_resultado					smallint		NULL,
	CONSTRAINT   apex_obj_dbr_ext_col_pk  PRIMARY KEY ( externa_id , col_id , objeto , objeto_proyecto ),
	CONSTRAINT   apex_obj_dbr_ext_col_fk_ext  FOREIGN KEY ( externa_id ,  objeto ,  objeto_proyecto ) 
		REFERENCES  apex_objeto_db_registros_ext  ( externa_id ,  objeto ,  objeto_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_obj_dbr_ext_col_fk_col  FOREIGN KEY ( col_id ,  objeto ,  objeto_proyecto  ) 
		REFERENCES  apex_objeto_db_registros_col  ( col_id , objeto , objeto_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;


CREATE TABLE apex_objeto_db_registros_uniq
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                integer      		NOT NULL,
	uniq_id						integer		 auto_increment  		NOT NULL, 
	columnas						varchar(255)	NULL,
	CONSTRAINT   apex_obj_dbr_uniq_pk  PRIMARY KEY ( uniq_id ,  objeto ,  objeto_proyecto ),
	CONSTRAINT   apex_obj_dbr_uniq_fk_objeto_dbr  FOREIGN KEY ( objeto ,  objeto_proyecto ) REFERENCES  apex_objeto_db_registros  ( objeto ,  objeto_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   	
) ENGINE=InnoDB;


CREATE TABLE apex_objeto_datos_rel
(
	proyecto 		 				varchar(15)		NOT NULL,
	objeto      	    	 	integer		NOT NULL,
	debug							smallint		NULL DEFAULT 0,	
	clave							varchar(60)		NULL,
	ap							integer		NULL,
	ap_clase						varchar(60)		NULL,
	ap_archivo						varchar(120)	NULL,
	sinc_susp_constraints			smallint		NULL DEFAULT 0,
	sinc_orden_automatico			smallint		NULL DEFAULT 1,
	CONSTRAINT   apex_objeto_datos_rel_pk  PRIMARY KEY ( objeto ,  proyecto ),
	CONSTRAINT   apex_objeto_datos_rel_fk_ap   FOREIGN KEY ( ap ) REFERENCES    apex_admin_persistencia  ( ap ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_objeto_datos_rel_fk_objeto   FOREIGN KEY ( objeto ,  proyecto ) REFERENCES    apex_objeto  ( objeto ,  proyecto ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_datos_rel_asoc
(
	proyecto 		   			   	varchar(15)			NOT NULL,
	objeto 		                integer      			NOT NULL,
	asoc_id						integer			 auto_increment  		NOT NULL, 
	identificador    				varchar(60)			NULL, 
	padre_proyecto					varchar(15)			NOT NULL,
	padre_objeto				integer			NOT NULL,
	padre_id						varchar(20)			NOT NULL,
	padre_clave		    			varchar(255)			NULL, 
	hijo_proyecto					varchar(15)			NOT NULL,
	hijo_objeto					integer			NOT NULL,
	hijo_id							varchar(20)			NOT NULL,
	hijo_clave		    			varchar(255)			NULL, 
	cascada							smallint			NULL,
	orden							float				NULL,
	CONSTRAINT   apex_obj_datos_rel_asoc_pk  PRIMARY KEY ( asoc_id , objeto , proyecto ),
	CONSTRAINT   apex_obj_datos_rel_asoc_fk_objeto  FOREIGN KEY ( objeto , proyecto ) REFERENCES  apex_objeto_datos_rel  ( objeto , proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_obj_datos_rel_asoc_fk_padre  FOREIGN KEY ( proyecto , objeto , padre_id ) REFERENCES  apex_objeto_dependencias  ( proyecto , objeto_consumidor , identificador ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_obj_datos_rel_asoc_fk_hijo  FOREIGN KEY ( proyecto , objeto , hijo_id ) REFERENCES  apex_objeto_dependencias  ( proyecto , objeto_consumidor , identificador ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;
