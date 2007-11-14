
CREATE TABLE apex_usuario_tipodoc
(	
	usuario_tipodoc				varchar(10)		NOT NULL,
	descripcion						varchar(40)		NOT NULL,
	CONSTRAINT	 apex_usuario_tipodoc_pk 	 PRIMARY	KEY ( usuario_tipodoc )
) ENGINE=InnoDB;

CREATE TABLE apex_usuario
(	
	usuario							varchar(60)		NOT NULL,
	clave							varchar(128)	NOT NULL,
	nombre							varchar(255)	NULL,
	usuario_tipodoc					varchar(10)		NULL,
	pre								varchar(2)		NULL,
	ciu								varchar(18)		NULL,
	suf								varchar(1)		NULL,
	email							varchar(255)		NULL,
	telefono						varchar(30)		NULL,
	vencimiento						date				NULL,
	dias							smallint			NULL,
	hora_entrada				 time  NULL,
	hora_salida					 time  NULL,
	ip_permitida					varchar(20)		NULL,
	solicitud_registrar				smallint			NULL,
	solicitud_obs_tipo_proyecto		varchar(15)		NULL,
	solicitud_obs_tipo				varchar(20)		NULL,
	solicitud_observacion			varchar(255)	NULL,
	parametro_a						varchar(255)	NULL,
	parametro_b						varchar(255)	NULL,
	parametro_c						varchar(255)	NULL,
	autentificacion					varchar(10)		NULL DEFAULT 'plano',
	bloqueado						smallint		DEFAULT 0 NULL,
	CONSTRAINT	 apex_usuario_pk 	 PRIMARY	KEY ( usuario ),
	CONSTRAINT	 apex_usuario_fk_tipodoc  FOREIGN KEY ( usuario_tipodoc ) REFERENCES	 apex_usuario_tipodoc  ( usuario_tipodoc ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	
) ENGINE=InnoDB;


CREATE TABLE apex_usuario_perfil_datos
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						text			NULL,
	listar							smallint			NULL,
	CONSTRAINT	 apex_usuario_perfil_datos_pk  PRIMARY	KEY ( proyecto , usuario_perfil_datos )
) ENGINE=InnoDB;

CREATE TABLE apex_usuario_grupo_acc
(	
	proyecto						varchar(15)		NOT NULL,
	usuario_grupo_acc				varchar(30)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	nivel_acceso					smallint		NULL,
	descripcion						text			NULL,
	vencimiento						date			NULL,
	dias							smallint		NULL,
	hora_entrada				 time  NULL,
	hora_salida					 time  NULL,
	listar							smallint			NULL,
	CONSTRAINT	 apex_usu_g_acc_pk  PRIMARY KEY ( proyecto , usuario_grupo_acc )
) ENGINE=InnoDB;

CREATE TABLE apex_usuario_proyecto
(	
	proyecto							varchar(15)			NOT NULL,
	usuario_grupo_acc					varchar(30)			NOT NULL,
	usuario								varchar(60)			NOT NULL,
	usuario_perfil_datos				varchar(20)			NULL,		
	CONSTRAINT	 apex_usu_proy_pk   PRIMARY KEY ( proyecto ,  usuario_grupo_acc ,  usuario ),
	CONSTRAINT	 apex_usu_proy_fk_usuario 	FOREIGN KEY	( usuario )	REFERENCES  apex_usuario  ( usuario ) ON DELETE	CASCADE ON UPDATE	CASCADE  	,
	CONSTRAINT	 apex_usu_proy_fk_grupo_acc  FOREIGN KEY ( proyecto , usuario_grupo_acc ) REFERENCES  apex_usuario_grupo_acc  ( proyecto , usuario_grupo_acc ) ON DELETE	CASCADE ON UPDATE CASCADE		 
) ENGINE=InnoDB;

CREATE TABLE apex_usuario_grupo_acc_item
(
	proyecto								varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(30)		NOT NULL,
 item_id integer NULL,	
 item varchar(60) NOT NULL ,
	CONSTRAINT	 apex_usu_item_pk  PRIMARY	KEY ( proyecto , usuario_grupo_acc , item ),
	CONSTRAINT	 apex_usu_item_fk_us_gru_acc 	FOREIGN KEY	( proyecto , usuario_grupo_acc )	REFERENCES  apex_usuario_grupo_acc 	( proyecto , usuario_grupo_acc )	ON	DELETE CASCADE ON UPDATE CASCADE   
) ENGINE=InnoDB;
  

CREATE TABLE apex_permiso_grupo_acc
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(30)		NOT NULL,
	permiso							integer		NOT NULL,
	CONSTRAINT	 apex_per_grupo_acc_pk  		PRIMARY	KEY ( usuario_grupo_acc , permiso , proyecto ),
	CONSTRAINT	 apex_per_grupo_acc_grupo_fk 	FOREIGN KEY	( proyecto , usuario_grupo_acc )	REFERENCES  apex_usuario_grupo_acc 	( proyecto , usuario_grupo_acc )	ON	DELETE CASCADE ON UPDATE CASCADE   
) ENGINE=InnoDB;
