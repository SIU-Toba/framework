
CREATE TABLE apex_objeto_mt_me_tipo_nav
(
	tipo_navegacion							varchar(10)			NOT NULL,
	descripcion								varchar(30)			NOT	NULL,
	CONSTRAINT	 apex_objeto_mt_me_tn_pk  PRIMARY	KEY ( tipo_navegacion )
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_mt_me
(
	objeto_mt_me_proyecto					varchar(15)			NOT NULL,
	objeto_mt_me						integer			NOT NULL,
	ev_procesar_etiq						varchar(30)			NULL,
	ev_cancelar_etiq						varchar(30)			NULL,
	ancho									varchar(20)			NULL,
	alto									varchar(20)			NULL,
	posicion_botonera						varchar(10)			NULL,
	tipo_navegacion							varchar(10)			NULL,
	con_toc									smallint			NULL,
	incremental								smallint			NULL,
	debug_eventos							smallint			NULL,
	activacion_procesar						varchar(40)			NULL, 
	activacion_cancelar						varchar(40)			NULL, 
	ev_procesar								smallint			NULL,
	ev_cancelar								smallint			NULL,
	objetos									varchar(255)		NULL,	
	post_procesar							varchar(40)			NULL, 
	metodo_despachador						varchar(40)			NULL,  
	metodo_opciones							varchar(40)			NULL,  
	CONSTRAINT	 apex_objeto_mt_me_pk  PRIMARY	KEY ( objeto_mt_me_proyecto , objeto_mt_me ),
	CONSTRAINT	 obj_objeto_mt_me_fk_objeto  FOREIGN	KEY ( objeto_mt_me_proyecto , objeto_mt_me )	REFERENCES  apex_objeto  ( proyecto , objeto ) ON DELETE	NO	ACTION ON UPDATE NO ACTION		 ,
	CONSTRAINT	 obj_objeto_mt_me_fk_tnav  FOREIGN	KEY ( tipo_navegacion )	REFERENCES  apex_objeto_mt_me_tipo_nav  ( tipo_navegacion ) ON DELETE	NO	ACTION ON UPDATE NO ACTION		 
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_ci_pantalla
(
	objeto_ci_proyecto					varchar(15)			NOT NULL,
	objeto_ci						integer			NOT NULL,
	pantalla						integer			 auto_increment  NOT NULL, 
	identificador						varchar(40)			NOT NULL,
	orden								smallint			NULL,	
	etiqueta							varchar(80)			NULL,
	descripcion							text				NULL,
	tip									varchar(255)			NULL,
	imagen_recurso_origen				varchar(10)			NULL,
	imagen								varchar(60)			NULL,
	objetos								text				NULL,
	eventos								text				NULL,
	subclase							varchar(80)			NULL,
	subclase_archivo					varchar(80)			NULL,
	CONSTRAINT	 apex_obj_ci_pan__pk  PRIMARY KEY ( pantalla , objeto_ci , objeto_ci_proyecto ),
   	CONSTRAINT   apex_obj_ci_pan__uk  UNIQUE ( objeto_ci_proyecto , objeto_ci , identificador ),
	CONSTRAINT	 apex_obj_ci_pan__fk_padre  FOREIGN KEY ( objeto_ci_proyecto , objeto_ci ) REFERENCES  apex_objeto_mt_me  ( objeto_mt_me_proyecto , objeto_mt_me ) ON DELETE CASCADE ON UPDATE NO ACTION		 ,
	CONSTRAINT	 apex_obj_ci_pan_fk_rec_orig 	FOREIGN KEY	( imagen_recurso_origen ) REFERENCES  apex_recurso_origen  ( recurso_origen )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   
) ENGINE=InnoDB;
