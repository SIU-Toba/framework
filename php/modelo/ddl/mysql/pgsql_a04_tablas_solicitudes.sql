


CREATE TABLE apex_solicitud
(
	proyecto					varchar(15)		NOT NULL,
	solicitud	 			integer		 auto_increment  NOT NULL, 
	solicitud_tipo				varchar(20)		NOT NULL,
	item_proyecto				varchar(15)		NOT NULL,
 item varchar(60) NOT NULL ,
    item_id integer NULL, 
	momento					 timestamp 	DEFAULT current_timestamp NOT NULL,
	tiempo_respuesta			float			NULL,
	CONSTRAINT	 apex_log_sol_pk  PRIMARY KEY ( solicitud ,  proyecto )
) ENGINE=InnoDB;

CREATE TABLE apex_sesion_browser
(
	sesion_browser			integer		 auto_increment  NOT NULL, 
	proyecto					varchar(15)		NOT NULL,
	usuario						varchar(60) 	NOT NULL,
	ingreso					 timestamp 	DEFAULT current_timestamp NOT NULL,
	egreso					 timestamp 		NULL,
	observaciones				varchar(255)	NULL,
	php_id						varchar(100)	NOT NULL,
	ip							varchar(20)		NULL,
	punto_acceso				varchar(80) 	NULL,
	CONSTRAINT	 apex_ses_brw_pk  PRIMARY KEY ( sesion_browser ,  proyecto ) 
) ENGINE=InnoDB;

CREATE TABLE apex_solicitud_browser
(
	proyecto					varchar(15)		NULL,		
	sesion_browser			integer		NOT NULL,
	solicitud_proyecto			varchar(15)		NULL,		
	solicitud_browser		integer		NOT NULL, 
	ip							varchar(20)		NULL,
	CONSTRAINT	 apex_sol_brw_pk  PRIMARY KEY ( solicitud_proyecto ,  solicitud_browser ),
	CONSTRAINT	 apex_sol_brw_fk_sol  FOREIGN KEY ( solicitud_browser ,  solicitud_proyecto ) REFERENCES  apex_solicitud  ( solicitud ,  proyecto ) ON DELETE CASCADE ON UPDATE CASCADE    ,
	CONSTRAINT	 apex_sol_brw_fk_sesion  FOREIGN KEY ( sesion_browser , proyecto ) REFERENCES  apex_sesion_browser  ( sesion_browser , proyecto ) ON DELETE CASCADE ON UPDATE CASCADE   
) ENGINE=InnoDB;

CREATE TABLE apex_solicitud_consola
(
	proyecto					varchar(15)			NULL,		
	solicitud_consola		integer			NOT NULL, 
	usuario						varchar(60)			NOT NULL,
	ip							varchar(20)			NULL,
	llamada						text				NULL,
	entorno						text				NULL,
	CONSTRAINT	 apex_sol_consola_pk  PRIMARY KEY ( solicitud_consola ,  proyecto ),
	CONSTRAINT	 apex_sol_consola_fk_sol  FOREIGN KEY ( solicitud_consola ,  proyecto ) REFERENCES  apex_solicitud  ( solicitud ,  proyecto ) ON DELETE CASCADE ON UPDATE CASCADE    
) ENGINE=InnoDB;

CREATE TABLE apex_solicitud_cronometro
(
	proyecto					varchar(15)			NULL,		
	solicitud	 			integer			NOT NULL,
	marca						smallint			NOT NULL,
	nivel_ejecucion				varchar(15)			NOT NULL,
	texto						varchar(120)		NULL,
	tiempo						float				NULL,
	CONSTRAINT	 apex_sol_cron_pk  PRIMARY KEY ( solicitud ,  proyecto , marca ),
	CONSTRAINT	 apex_sol_cron_fk_sol  FOREIGN KEY ( solicitud ,  proyecto ) REFERENCES  apex_solicitud  ( solicitud ,  proyecto ) ON DELETE CASCADE ON UPDATE CASCADE    
) ENGINE=InnoDB;

CREATE TABLE apex_solicitud_observacion
(
	proyecto						varchar(15)			NULL,		
	solicitud	 				integer			NOT NULL,
	solicitud_observacion		integer			 auto_increment  NOT NULL, 
	solicitud_obs_tipo_proyecto		varchar(15)			NULL,
	solicitud_obs_tipo				varchar(20)			NULL,
	observacion						text				NULL,
	CONSTRAINT	 apex_sol_obs_pk  PRIMARY KEY ( solicitud_observacion ),
	CONSTRAINT	 apex_sol_obs_fk_sol  FOREIGN KEY ( solicitud ,  proyecto ) REFERENCES  apex_solicitud  ( solicitud ,  proyecto ) ON DELETE CASCADE ON UPDATE CASCADE    
) ENGINE=InnoDB;


CREATE TABLE apex_log_sistema
(
	log_sistema		 		integer			 auto_increment  NOT NULL, 
	momento					 timestamp 	DEFAULT current_timestamp NOT NULL,
	usuario						varchar(60) 		NULL,
	log_sistema_tipo			varchar(20) 		NOT NULL,
	observaciones				text				NULL,
	CONSTRAINT	 apex_log_sis_pk  PRIMARY KEY ( log_sistema )
) ENGINE=InnoDB;

CREATE TABLE apex_log_error_login
(
	log_error_login 		integer			 auto_increment  NOT NULL, 
	momento					 timestamp 	DEFAULT current_timestamp NOT NULL,
	usuario						text 			NULL,
	clave						text		 		NULL,
	ip							varchar(100)				NULL,
	gravedad					smallint			NULL,
	mensaje						text				NULL,
	punto_acceso				text		 		NULL,
	CONSTRAINT	 apex_log_error_login_pk  PRIMARY KEY ( log_error_login )
) ENGINE=InnoDB;

CREATE TABLE apex_log_ip_rechazada
(
	ip							varchar(255)											NOT NULL,
	momento					 timestamp 	DEFAULT current_timestamp NOT NULL,
	CONSTRAINT	 apex_ip_rechazada_pk  PRIMARY KEY ( ip )
) ENGINE=InnoDB;

