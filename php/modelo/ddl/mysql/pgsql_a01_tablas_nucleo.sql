

CREATE TABLE	apex_menu
(
	menu						varchar(40)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	archivo						varchar(255)	NOT NULL,
	soporta_frames				smallint		NULL,
	CONSTRAINT	 apex_menu_pk  PRIMARY	KEY ( menu )
) ENGINE=InnoDB;



CREATE TABLE			apex_estilo
(
	estilo					varchar(40)		NOT NULL,
	descripcion				varchar(255)	NOT NULL,
	proyecto				varchar(15)		NOT NULL,
	CONSTRAINT	 apex_estilo_pk  PRIMARY KEY ( estilo ),
	CONSTRAINT	 apex_estilo_fk_proyecto  FOREIGN KEY ( proyecto )	REFERENCES  apex_proyecto 	( proyecto ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  		
) ENGINE=InnoDB;


CREATE TABLE apex_log_sistema_tipo 
(
	log_sistema_tipo			varchar(20)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	CONSTRAINT	 apex_log_sistema_tipo_pk  PRIMARY KEY ( log_sistema_tipo )
) ENGINE=InnoDB;


CREATE TABLE apex_fuente_datos_motor
(	
	fuente_datos_motor			varchar(30)		NOT NULL,
	nombre						varchar(255)	NOT NULL,
	version						varchar(30)		NOT NULL,
	CONSTRAINT	 apex_fuente_datos_motor_pk  PRIMARY KEY ( fuente_datos_motor ) 
) ENGINE=InnoDB;

CREATE TABLE apex_fuente_datos
(	
	proyecto					varchar(15)		NOT NULL,
	fuente_datos				varchar(20)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	descripcion_corta			varchar(40)		NULL,	
	fuente_datos_motor			varchar(30)		NULL,
	host						varchar(60)		NULL,
	usuario						varchar(60)		NULL,
	clave						varchar(60)		NULL,
	base						varchar(60)		NULL,	
	administrador				varchar(60)		NULL,
	link_instancia				smallint		NULL,	
	instancia_id				varchar(60)	NULL,
	subclase_archivo			varchar(255) 	NULL,
	subclase_nombre				varchar(60) 	NULL,
	orden						smallint		NULL,
	CONSTRAINT	 apex_fuente_datos_pk  PRIMARY KEY ( proyecto , fuente_datos ),
	CONSTRAINT	 apex_fuente_datos_fk_motor  FOREIGN KEY ( fuente_datos_motor ) REFERENCES	 apex_fuente_datos_motor  ( fuente_datos_motor ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	,
	CONSTRAINT	 apex_fuente_datos_fk_proyecto  FOREIGN KEY ( proyecto )	REFERENCES  apex_proyecto 	( proyecto ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	
) ENGINE=InnoDB;

CREATE TABLE apex_grafico
(	
	grafico						varchar(30)			NOT NULL,
	descripcion_corta			varchar(40)			NULL,	
	descripcion					varchar(255)		NOT NULL,
	parametros					text				NULL,
	CONSTRAINT	 apex_tipo_grafico_pk  PRIMARY KEY ( grafico ) 
) ENGINE=InnoDB;

CREATE TABLE apex_recurso_origen
(	
	recurso_origen				varchar(30)			NOT NULL,
	descripcion					varchar(255)		NOT NULL,
	CONSTRAINT	 apex_rec_origen_pk 	PRIMARY KEY	( recurso_origen ) 
) ENGINE=InnoDB;

CREATE TABLE apex_nivel_acceso
(	
	nivel_acceso					smallint			NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						text			NULL,
	CONSTRAINT	 apex_nivel_acceso_pk  PRIMARY KEY ( nivel_acceso )
) ENGINE=InnoDB;

CREATE TABLE apex_solicitud_tipo
(
	solicitud_tipo					varchar(20)		NOT NULL,
	descripcion						varchar(255)	NOT NULL,
	descripcion_corta				varchar(40)		NULL,	
	icono								varchar(30)		NULL,
	CONSTRAINT	 apex_sol_tipo_pk  PRIMARY	KEY ( solicitud_tipo )
) ENGINE=InnoDB;

CREATE TABLE apex_elemento_formulario
(	
	elemento_formulario				varchar(50)		NOT NULL,
	padre							varchar(30)		NULL,
	descripcion						text			NOT NULL,
	parametros						text			NULL,	
	proyecto						varchar(15)		NOT NULL,
	exclusivo_toba					smallint		NULL,
	obsoleto						smallint		NULL,
	CONSTRAINT	 apex_elform_pk  PRIMARY KEY ( elemento_formulario ),
	CONSTRAINT	 apex_elform_fk_padre  FOREIGN KEY ( padre ) REFERENCES  apex_elemento_formulario 	( elemento_formulario ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	,
	CONSTRAINT	 apex_elform_fk_proyecto  FOREIGN KEY ( proyecto )	REFERENCES  apex_proyecto 	( proyecto ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	
) ENGINE=InnoDB;

CREATE TABLE apex_solicitud_obs_tipo
(	
	proyecto						varchar(15)		NOT NULL,
	solicitud_obs_tipo				varchar(20)		NOT NULL,
	descripcion						varchar(255)	NOT NULL,
	criterio						varchar(20)		NOT NULL,
	CONSTRAINT	 apex_sol_obs_tipo_pk  PRIMARY KEY ( proyecto , solicitud_obs_tipo ),
	CONSTRAINT	 apex_sol_obs_tipo_fk_proyecto  FOREIGN KEY ( proyecto )	REFERENCES  apex_proyecto 	( proyecto ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	
) ENGINE=InnoDB;

CREATE TABLE apex_pagina_tipo
(	
	proyecto							varchar(15)		NOT NULL,
	pagina_tipo							varchar(20)		NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	clase_nombre						varchar(40)		NULL,
	clase_archivo						varchar(255)	NULL,
	include_arriba						varchar(100)	NULL,
	include_abajo						varchar(100)	NULL,
	exclusivo_toba						smallint		NULL,
	contexto							varchar(255)	NULL,	
	CONSTRAINT	 apex_pagina_tipo_pk  PRIMARY	KEY ( proyecto , pagina_tipo ),
	CONSTRAINT	 apex_pagina_tipo_fk_proy 	FOREIGN KEY	( proyecto ) REFERENCES	 apex_proyecto  ( proyecto ) ON DELETE	NO	ACTION ON UPDATE NO ACTION		 
) ENGINE=InnoDB;

CREATE TABLE apex_columna_estilo
(
	columna_estilo					integer			 auto_increment 	NOT NULL, 
	css									varchar(40)		NOT NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)	  NULL,
	CONSTRAINT	 apex_columna_estilo_pk  PRIMARY	KEY ( columna_estilo ) 
) ENGINE=InnoDB;

CREATE TABLE apex_columna_formato
(
	columna_formato				integer			 auto_increment  NOT NULL, 
	funcion								varchar(60)		NOT NULL,
	archivo								varchar(80)		NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)		NULL,
	parametros							varchar(255)	NULL,
	CONSTRAINT	 apex_columna_formato_pk  PRIMARY KEY ( columna_formato ) 
) ENGINE=InnoDB;


CREATE TABLE apex_ptos_control 
(
  proyecto VARCHAR(15) NOT NULL,
  pto_control          VARCHAR(30) NOT NULL,
  descripcion          VARCHAR(255) NULL,
  CONSTRAINT  apex_ptos_control__pk  PRIMARY KEY( proyecto ,  pto_control )
) ENGINE=InnoDB;

CREATE TABLE apex_ptos_control_param
(
  proyecto VARCHAR(15) NOT NULL,
  pto_control              VARCHAR(30) NOT NULL,
  parametro                VARCHAR(60) NULL,
  CONSTRAINT  apex_ptos_ctrl_param__pk  PRIMARY KEY( proyecto ,  pto_control ,  parametro ),
  CONSTRAINT  apex_ptos_ctrl_param_fk_ptos_ctrl  FOREIGN KEY ( proyecto ,  pto_control ) REFERENCES  apex_ptos_control ( proyecto ,  pto_control ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_ptos_control_ctrl
(
  proyecto VARCHAR(15)  NOT NULL,
  pto_control             VARCHAR(30)  NOT NULL,
  clase                   VARCHAR(60)  NOT NULL,
  archivo                 VARCHAR(255) NULL,
  actua_como              CHAR(1)      DEFAULT 'M' NOT NULL CHECK (actua_como IN ('E','A','M')),
  CONSTRAINT  apex_ptos_ctrl_ctrl__pk  PRIMARY KEY( proyecto ,  pto_control ,  clase ),
  CONSTRAINT  apex_ptos_ctrl_ctrl_fk_ptos_ctrl  FOREIGN KEY ( proyecto ,  pto_control ) REFERENCES  apex_ptos_control ( proyecto ,  pto_control ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;


CREATE TABLE	apex_consulta_php
(
  	proyecto 					VARCHAR(15)  	NOT NULL,
	consulta_php			integer		 auto_increment  NOT NULL, 
  	clase                   	VARCHAR(60)  	NOT NULL,
  	archivo                 	VARCHAR(255) 	NOT NULL,
  	descripcion                	VARCHAR(255) 	NULL,
  	CONSTRAINT  apex_consulta_php_pk  PRIMARY KEY( consulta_php , proyecto ),
  	CONSTRAINT  apex_consulta_php_fk_proyecto  FOREIGN KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;
