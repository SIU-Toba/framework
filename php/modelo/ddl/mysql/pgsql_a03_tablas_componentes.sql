
CREATE TABLE apex_item_zona
(	
	proyecto						varchar(15)		NOT NULL,
	zona							varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	clave_editable					varchar(100)	NULL,		
	archivo							varchar(80)		NULL, 		
	descripcion						text			NULL,		
	consulta_archivo				varchar(255)	NULL,
	consulta_clase					varchar(60)		NULL,
	consulta_metodo					varchar(80)		NULL,
	CONSTRAINT	 apex_item_zona_pk  PRIMARY KEY ( proyecto , zona ),
	CONSTRAINT	 apex_item_zona_fk_proy  FOREIGN	KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_item
(	
 item_id integer NULL,
	proyecto						varchar(15)		NOT NULL,
 item varchar(60) NOT NULL ,
	padre_id					integer		NULL,	
	padre_proyecto					varchar(15)		NOT NULL,
	padre							varchar(60)		NOT NULL,
	carpeta							smallint		NULL,
	nivel_acceso					smallint		NULL,
	solicitud_tipo					varchar(20)		NULL,
	pagina_tipo_proyecto			varchar(15)		NULL,
	pagina_tipo						varchar(20)		NULL,
	actividad_buffer_proyecto		varchar(15)		NULL,
	actividad_buffer			integer		NULL,
	actividad_patron_proyecto		varchar(15)		NULL,
	actividad_patron				varchar(20)		NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						text			NULL,
	actividad_accion				varchar(80)		NULL,
	menu							smallint		NULL,
	orden							float			NULL,
	solicitud_registrar				smallint		NULL,
	solicitud_obs_tipo_proyecto		varchar(15)		NULL,
	solicitud_obs_tipo				varchar(20)		NULL,
	solicitud_observacion			varchar(90)		NULL,
	solicitud_registrar_cron		smallint		NULL,
	prueba_directorios				smallint		NULL,
	zona_proyecto					varchar(15)		NULL,
	zona							varchar(20)		NULL,
	zona_orden						float			NULL,
	zona_listar						smallint		NULL,
	imagen_recurso_origen			varchar(10)		NULL,
	imagen							varchar(60)		NULL,
	parametro_a						varchar(255)	NULL,
	parametro_b						varchar(255)	NULL,
	parametro_c						varchar(255)	NULL,
	publico							smallint		NULL,
	redirecciona					smallint		NULL,
	usuario							varchar(60)		NULL,
	creacion					 timestamp 	DEFAULT current_timestamp NULL,
	CONSTRAINT	 apex_item_pk 	PRIMARY KEY	( item ,  proyecto ),
	CONSTRAINT	 apex_item_fk_proyecto 	FOREIGN KEY	( proyecto ) REFERENCES	 apex_proyecto  ( proyecto ) ON DELETE	NO	ACTION ON UPDATE NO ACTION		 ,
	CONSTRAINT	 apex_item_fk_padre 	FOREIGN KEY	( padre_proyecto , padre )	REFERENCES  apex_item  ( proyecto , item ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	,
	CONSTRAINT	 apex_item_fk_solic_tipo  FOREIGN KEY ( solicitud_tipo )	REFERENCES  apex_solicitud_tipo 	( solicitud_tipo ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	,
	CONSTRAINT	 apex_item_fk_solic_ot 	FOREIGN KEY	( solicitud_obs_tipo_proyecto , solicitud_obs_tipo ) REFERENCES  apex_solicitud_obs_tipo 	( proyecto , solicitud_obs_tipo ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	,
	CONSTRAINT	 apex_item_fk_niv_acc  FOREIGN KEY ( nivel_acceso ) REFERENCES	 apex_nivel_acceso  ( nivel_acceso ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	,
	CONSTRAINT	 apex_item_fk_pag_tipo 	FOREIGN KEY	( pagina_tipo_proyecto , pagina_tipo )	REFERENCES  apex_pagina_tipo 	( proyecto , pagina_tipo )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   ,
	CONSTRAINT	 apex_item_fk_zona  FOREIGN KEY ( zona_proyecto , zona )	REFERENCES  apex_item_zona  ( proyecto , zona )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   ,
	CONSTRAINT	 apex_item_fk_rec_orig 	FOREIGN KEY	( imagen_recurso_origen ) REFERENCES  apex_recurso_origen  ( recurso_origen )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   
) ENGINE=InnoDB;


CREATE TABLE apex_item_info
(	
 item_id integer NULL,	
	item_proyecto					varchar(15)		NOT NULL,
 item varchar(60) NOT NULL ,
	descripcion_breve				varchar(255)	NULL,
	descripcion_larga				text				NULL,
	CONSTRAINT	 apex_item_info_pk 	 PRIMARY	KEY ( item_proyecto , item ),
	CONSTRAINT	 apex_item_info_fk_item  FOREIGN	KEY ( item_proyecto , item ) REFERENCES  apex_item  ( proyecto , item )	ON	DELETE CASCADE ON UPDATE	NO	ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_clase_tipo
(	
	clase_tipo					integer			 auto_increment  NOT	NULL,	
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	icono							varchar(60)			NULL,
	orden							float				NULL,
	metodologia						varchar(10)			NULL, 
	CONSTRAINT	 apex_clase_tipo_pk 	 PRIMARY	KEY ( clase_tipo )
) ENGINE=InnoDB;

CREATE TABLE apex_clase
(	
	proyecto						varchar(15)		NOT NULL,
	clase							varchar(60)		NOT NULL,
	clase_tipo					integer		NOT NULL, 
	archivo							varchar(80)		NULL,
	descripcion						varchar(250)	NOT NULL,
	icono							varchar(60)		NOT NULL, 		
	descripcion_corta				varchar(40)		NULL,			
	editor_proyecto					varchar(15)		NOT NULL,
	editor_item						varchar(60)		NOT NULL,			
	objeto_dr_proyecto				varchar(15)		NOT NULL,		
	objeto_dr					integer		NOT NULL,		
	utiliza_fuente_datos		integer		NULL,
	screenshot						varchar(60)		NULL,			
	ancestro_proyecto				varchar(15)		NULL,			
	ancestro						varchar(60)		NULL,
	instanciador_id				integer		NULL,	
	instanciador_proyecto			varchar(15)		NULL,
	instanciador_item				varchar(60)		NULL,			
	editor_id					integer		NULL,	
	editor_ancestro_proyecto		varchar(15)		NULL,			
	editor_ancestro					varchar(60)		NULL,
	plan_dump_objeto				varchar(255)	NULL, 			
	sql_info						text			NULL, 			
	doc_clase						varchar(255)	NULL,			
	doc_db							varchar(255)	NULL,			
	doc_sql							varchar(255)	NULL,			
	vinculos						smallint		NULL,			
	autodoc							smallint		NULL,
	parametro_a						varchar(255)	NULL,
	parametro_b						varchar(255)	NULL,
	parametro_c						varchar(255)	NULL,
	exclusivo_toba					smallint		NULL,
	CONSTRAINT	 apex_clase_pk  PRIMARY	KEY ( proyecto , clase ),
	CONSTRAINT	 apex_clase_uq  UNIQUE 	( clase ),
	CONSTRAINT	 apex_clase_fk_proyecto  FOREIGN	KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   ,
	CONSTRAINT	 apex_clase_fk_tipo 	FOREIGN KEY	( clase_tipo )	REFERENCES  apex_clase_tipo  ( clase_tipo ) ON DELETE	NO	ACTION ON UPDATE NO ACTION		 
) ENGINE=InnoDB;

CREATE TABLE apex_clase_relacion
(
	proyecto							varchar(15)		NOT NULL,
	clase_relacion					integer		 auto_increment  NOT NULL, 
	clase_contenedora					varchar(60)		NOT NULL,
	clase_contenida						varchar(60)		NOT NULL,
	CONSTRAINT	 apex_clase_rel_pk  PRIMARY KEY ( clase_relacion ),
	CONSTRAINT	 apex_clase_rel_fk_clase_padre  FOREIGN KEY ( proyecto , clase_contenedora ) REFERENCES  apex_clase  ( proyecto , clase ) ON DELETE CASCADE ON UPDATE CASCADE   ,
	CONSTRAINT	 apex_clase_rel_fk_clase_hijo  FOREIGN KEY ( proyecto , clase_contenida ) REFERENCES  apex_clase  ( proyecto , clase ) ON DELETE	CASCADE ON UPDATE CASCADE   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto
(
	objeto							integer		 auto_increment  NOT NULL, 
	proyecto							varchar(15)		NOT NULL,
	anterior							varchar(20)		NULL,
	reflexivo							smallint		NULL,
	clase_proyecto						varchar(15)		NOT NULL,
	clase								varchar(60)		NOT NULL,
	subclase							varchar(80)		NULL,
	subclase_archivo					varchar(80)		NULL,
	objeto_categoria_proyecto			varchar(15)		NULL,
	objeto_categoria					varchar(30)		NULL,
	nombre								varchar(120)	NOT NULL,
	titulo								varchar(120)	NULL,
	colapsable							smallint		NULL,
	descripcion							text			NULL,
	fuente_datos_proyecto				varchar(15)		NULL,
	fuente_datos						varchar(20)		NULL,
	solicitud_registrar					smallint		NULL,	
	solicitud_obj_obs_tipo				varchar(20)		NULL,	
	solicitud_obj_observacion			varchar(255)	NULL,	
	parametro_a							varchar(100)	NULL,
	parametro_b							varchar(100)	NULL,
	parametro_c							varchar(100)	NULL,
	parametro_d							varchar(100)	NULL,
	parametro_e							varchar(100)	NULL,
	parametro_f							varchar(100)	NULL,
	usuario								varchar(20)		NULL,
	creacion						 timestamp 	DEFAULT current_timestamp NULL,
	CONSTRAINT	 apex_objeto_pk 	 PRIMARY	KEY ( objeto ,  proyecto ),
	CONSTRAINT	 apex_objeto_fk_clase  FOREIGN KEY ( clase_proyecto , clase ) REFERENCES  apex_clase  ( proyecto , clase ) ON DELETE	NO	ACTION ON UPDATE NO ACTION		 ,
	CONSTRAINT	 apex_objeto_fk_fuente_datos 	FOREIGN KEY	( fuente_datos_proyecto , fuente_datos ) REFERENCES  apex_fuente_datos 	( proyecto , fuente_datos ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	,
	CONSTRAINT	 apex_objeto_fk_proyecto  FOREIGN KEY ( proyecto )	REFERENCES  apex_proyecto 	( proyecto ) ON DELETE NO ACTION	ON	UPDATE NO ACTION  	
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_info
(
	objeto_proyecto						varchar(15)			NOT NULL,
	objeto							integer			NOT NULL,
	descripcion_breve					varchar(255)		NULL,
	descripcion_larga					text				NULL,
	CONSTRAINT	 apex_objeto_info_pk  PRIMARY	KEY ( objeto_proyecto , objeto ),
	CONSTRAINT	 apex_objeto_info_fk_objeto  FOREIGN KEY ( objeto_proyecto , objeto ) REFERENCES	 apex_objeto  ( proyecto , objeto )	ON	DELETE CASCADE ON UPDATE	NO	ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_dependencias
(
	proyecto							varchar(15)			NOT NULL,
	dep_id							integer			 auto_increment  NOT NULL, 
	objeto_consumidor				integer			NOT NULL,
	objeto_proveedor				integer			NOT NULL,
	identificador						varchar(40)			NOT NULL,
	parametros_a						varchar(255)		NULL,
	parametros_b						varchar(255)		NULL,
	parametros_c						varchar(255)		NULL,
	inicializar							smallint			NULL,
	orden								smallint			NULL,
	CONSTRAINT	 apex_objeto_depen_pk 	 PRIMARY	KEY ( dep_id , proyecto , objeto_consumidor ),
	CONSTRAINT	 apex_objeto_depen_uq 	 UNIQUE  ( proyecto , objeto_consumidor , identificador ),
	CONSTRAINT	 apex_objeto_depen_fk_objeto_c  FOREIGN KEY ( proyecto , objeto_consumidor ) REFERENCES  apex_objeto 	( proyecto , objeto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT	 apex_objeto_depen_fk_objeto_p  FOREIGN KEY ( proyecto , objeto_proveedor ) REFERENCES	 apex_objeto  ( proyecto , objeto )	ON	DELETE CASCADE ON UPDATE NO	ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_eventos
(
	proyecto							varchar(15)			NOT NULL,
	evento_id						integer			 auto_increment  NOT NULL,
	objeto							integer			NOT NULL,
	identificador						varchar(40)			NOT NULL,
	etiqueta							varchar(255)		NULL,
	maneja_datos						smallint			NULL DEFAULT 1,
	sobre_fila							smallint			NULL,
	confirmacion						varchar(255)		NULL,
	estilo								varchar(40)			NULL,
	imagen_recurso_origen				varchar(10)			NULL,
	imagen								varchar(60)			NULL,
	en_botonera							smallint			NULL DEFAULT 1,
	ayuda								text				NULL,
	orden								smallint			NULL,
	ci_predep							smallint			NULL, 
	implicito							smallint			NULL,
	defecto								smallint			NULL,
	display_datos_cargados				smallint			NULL, 
	grupo								varchar(80)			NULL,
	accion								varchar(1)			NULL,
	accion_imphtml_debug				smallint			NULL,
	accion_vinculo_carpeta				varchar(60)			NULL,
	accion_vinculo_item					varchar(60)			NULL,
	accion_vinculo_objeto			integer			NULL,
	accion_vinculo_popup				smallint			NULL,
	accion_vinculo_popup_param			varchar(100)		NULL,
	accion_vinculo_target				varchar(40)			NULL,
	accion_vinculo_celda				varchar(40)			NULL,
	CONSTRAINT	 apex_objeto_eventos_pk  PRIMARY KEY ( evento_id , proyecto ),
	CONSTRAINT	 apex_objeto_eventos_uq  UNIQUE ( proyecto , objeto , identificador ),	
	CONSTRAINT	 apex_objeto_eventos_fk_rec_orig  FOREIGN KEY ( imagen_recurso_origen ) REFERENCES  apex_recurso_origen  ( recurso_origen )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   ,
	CONSTRAINT	 apex_objeto_eventos_fk_objeto  FOREIGN KEY ( proyecto , objeto ) REFERENCES  apex_objeto 	( proyecto , objeto ) ON DELETE CASCADE ON UPDATE NO ACTION  	
) ENGINE=InnoDB;

CREATE TABLE apex_ptos_control_x_evento
(
  proyecto 					VARCHAR(15) NOT NULL,
  pto_control              	VARCHAR(20) NOT NULL,
  evento_id                	INTEGER     NOT NULL,
  objeto				integer	NOT NULL,
  CONSTRAINT  apex_ptos_ctrl_x_evt__pk  PRIMARY KEY( proyecto ,  pto_control ,  evento_id ),
  CONSTRAINT  apex_proyecto_fk_ptos_ctrl  FOREIGN KEY ( proyecto ,  pto_control ) REFERENCES  apex_ptos_control ( proyecto ,  pto_control ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
  CONSTRAINT  apex_ptos_ctrl_x_evt_fk_proyecto  FOREIGN KEY ( proyecto ) REFERENCES  apex_proyecto ( proyecto ) ON DELETE NO ACTION ON UPDATE NO ACTION   , 
  CONSTRAINT  apex_ptos_ctrl_x_evt_fk_evento  FOREIGN KEY ( evento_id ,  proyecto ) REFERENCES  apex_objeto_eventos ( evento_id ,  proyecto ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;


CREATE TABLE apex_item_objeto
(
 item_id integer NULL,	
	proyecto							varchar(15)		NOT NULL,
 item varchar(60) NOT NULL ,
	objeto							integer		NOT NULL,
	orden								smallint		NOT NULL,
	inicializar							smallint		NULL,
	CONSTRAINT	 apex_item_consumo_obj_pk 	 PRIMARY	KEY ( proyecto , item , objeto ),
	CONSTRAINT	 apex_item_consumo_obj_fk_item  FOREIGN KEY ( proyecto , item ) REFERENCES	 apex_item 	( proyecto , item ) ON DELETE CASCADE ON UPDATE NO ACTION		 ,
	CONSTRAINT	 apex_item_consumo_obj_fk_objeto  FOREIGN	KEY ( proyecto , objeto ) REFERENCES  apex_objeto 	( proyecto , objeto ) ON DELETE CASCADE	ON	UPDATE NO ACTION  	
) ENGINE=InnoDB;

CREATE TABLE apex_log_objeto
(
	log_objeto						integer		 auto_increment  NOT NULL, 
	momento							 timestamp 	DEFAULT current_timestamp NOT NULL,
	usuario								varchar(60) 	NULL,
	objeto_proyecto          			varchar(15)  	NOT NULL,
	objeto							integer		NULL,
	item								varchar(60)		NULL,
	observacion							text			NULL,
	CONSTRAINT	 apex_log_objeto_pk  PRIMARY KEY ( log_objeto )
) ENGINE=InnoDB;

CREATE TABLE apex_arbol_items_fotos

(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(60)		NOT NULL,
	foto_nombre							varchar(100)	NOT NULL,
	foto_nodos_visibles					text			NULL,
	foto_opciones						text			NULL,
  CONSTRAINT  apex_arbol_items_fotos_pk  PRIMARY KEY( proyecto ,  usuario ,  foto_nombre )
) ENGINE=InnoDB;


CREATE TABLE apex_admin_album_fotos

(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(60)		NOT NULL,
	foto_tipo							varchar(20)		NOT NULL,	
	foto_nombre							varchar(100)	NOT NULL,
	foto_nodos_visibles					text			NULL,
	foto_opciones						text			NULL,
	predeterminada							smallint	NULL,
  CONSTRAINT  apex_admin_album_fotos_pk  PRIMARY KEY( proyecto ,  usuario ,  foto_nombre ,  foto_tipo )
) ENGINE=InnoDB;


CREATE TABLE apex_admin_param_previsualizazion

(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(60)		NOT NULL,
	grupo_acceso						varchar(255)		NOT NULL,
	punto_acceso						varchar(100)	NOT NULL,
  CONSTRAINT  apex_admin_param_prev_pk  PRIMARY KEY( proyecto ,  usuario )
) ENGINE=InnoDB;
 

CREATE TABLE apex_conversion
(
	proyecto							varchar(15)		NOT NULL,
	conversion_aplicada					varchar(60)		NOT NULL,
	fecha								timestamp		NOT NULL,
	CONSTRAINT	 apex_conversion_pk  PRIMARY	KEY ( proyecto , conversion_aplicada ),
	CONSTRAINT	 apex_conversion_proy  FOREIGN KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto ) ON	DELETE NO ACTION ON UPDATE	NO	ACTION   
) ENGINE=InnoDB;

