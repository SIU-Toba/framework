CREATE TABLE			apex_revision
(
	revision					varchar(20)	NOT NULL,
	creacion				 timestamp 	DEFAULT current_timestamp NOT	NULL
) ENGINE=InnoDB;

CREATE TABLE apex_instancia
(
	instancia					varchar(80)		NOT NULL,
	version						varchar(15)		NOT NULL,
	institucion					varchar(255)	NULL,
	observaciones				varchar(255)	NULL,
	administrador_1				varchar(60)		NULL,
	administrador_2				varchar(60)		NULL,
	administrador_3				varchar(60)		NULL,
	creacion				 timestamp 	DEFAULT current_timestamp NOT	NULL,
	CONSTRAINT	 apex_instancia_pk 	 PRIMARY	KEY ( instancia )
) ENGINE=InnoDB;

CREATE TABLE			apex_proyecto
(
	proyecto							varchar(15)		NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	descripcion_corta					varchar(60)		NOT NULL, 
	estilo								varchar(30)		NOT NULL,
	con_frames							smallint		DEFAULT 1 NULL,
	frames_clase						varchar(40)		NULL,
	frames_archivo						varchar(255)	NULL,
	salida_impr_html_c					varchar(40)		NULL,
	salida_impr_html_a					varchar(255)	NULL,
	menu								varchar(15)		NULL,
	path_includes						varchar(255)	NULL,
	path_browser						varchar(255)	NULL,
	administrador						varchar(60)		NULL,
	listar_multiproyecto				smallint		NULL,
	orden								float			NULL,
	palabra_vinculo_std					varchar(30)		NULL,
	version_toba						varchar(15)		NULL,
	requiere_validacion					smallint		NULL,
	usuario_anonimo						varchar(60)		NULL,
	usuario_anonimo_desc				varchar(60)		NULL,
	usuario_anonimo_grupos_acc			varchar(255)	NULL,
	validacion_intentos					smallint		NULL,
	validacion_intentos_min				smallint		NULL,
	validacion_bloquear_usuario			smallint		DEFAULT 1 NULL,
	validacion_debug					smallint		NULL,
	sesion_tiempo_no_interac_min		smallint		NULL,
	sesion_tiempo_maximo_min			smallint		NULL,
	sesion_subclase						varchar(60)		NULL,
	sesion_subclase_archivo				varchar(255)	NULL,
	contexto_ejecucion_subclase			varchar(60)		NULL,
	contexto_ejecucion_subclase_archivo	varchar(255)	NULL,
	usuario_subclase					varchar(60)		NULL,
	usuario_subclase_archivo			varchar(255)	NULL,
	encriptar_qs						smallint		NULL,
	registrar_solicitud					varchar(1)		NULL,
	registrar_cronometro				varchar(1)		NULL,
	item_inicio_sesion      			varchar(60)		NULL,
	item_pre_sesion		          		varchar(60)		NULL,
	item_set_sesion						varchar(60)		NULL,
	log_archivo							smallint		NULL,
	log_archivo_nivel					smallint		NULL,
	fuente_datos						varchar(20)		NULL,
	pagina_tipo							varchar(20)		NULL,
	version								varchar(20)		NULL,
	version_fecha						date			NULL,
	version_detalle						text			NULL,
	version_link						varchar(255)	NULL,
	CONSTRAINT	 apex_proyecto_pk  PRIMARY	KEY ( proyecto )
) ENGINE=InnoDB;



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
	proyecto  							varchar(15)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   msg_tipo       					varchar(20)    NOT NULL,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  text        NULL,
   mensaje_b	                  text        NULL,
   mensaje_c	                  text        NULL,
   mensaje_customizable          text        NULL,
   CONSTRAINT   apex_msg_pk  PRIMARY KEY ( msg ,  proyecto ),
   CONSTRAINT   apex_msg_fk_proy  FOREIGN KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
   CONSTRAINT   apex_msg_fk_tipo  FOREIGN KEY ( msg_tipo ) REFERENCES  apex_msg_tipo  ( msg_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_item_msg
(  
	item_msg          		   integer           auto_increment  NOT NULL, 
   msg_tipo          		   	varchar(20)    NOT NULL,
	indice          					varchar(20)    NOT NULL,
   item_id integer NULL, 
   item_proyecto       		   	varchar(15)    NOT NULL,
   item varchar(60) NOT NULL ,
   descripcion_corta            	varchar(50)    NULL,
   mensaje_a	                  text        NULL,
   mensaje_b	                  text        NULL,
   mensaje_c	                  text        NULL,
   mensaje_customizable          text        NULL,
	parametro_patron					varchar(100)	NULL,
   CONSTRAINT   apex_item_msg_pk    PRIMARY KEY ( item_msg , item_proyecto ),
   CONSTRAINT   apex_item_msg_uk  UNIQUE ( indice ),
   CONSTRAINT   apex_item_msg_fk_item  FOREIGN KEY ( item ,  item_proyecto ) REFERENCES  apex_item  ( item ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
   CONSTRAINT   apex_item_msg_fk_tipo  FOREIGN KEY ( msg_tipo ) REFERENCES  apex_msg_tipo  ( msg_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_msg
(
   	objeto_msg        	     	integer          	 auto_increment  NOT NULL, 
   	msg_tipo       	        		varchar(20)    	NOT NULL,
	indice          				varchar(20)    	NOT NULL,
   	objeto                  	integer    	NOT NULL,
   	objeto_proyecto         		varchar(15)    	NOT NULL,
   	descripcion_corta            	varchar(50)    	NULL,
   	mensaje_a	             	    text        	NULL,
   	mensaje_b	             	    text        	NULL,
   	mensaje_c	             	    text        	NULL,
   	mensaje_customizable     	    text        	NULL,
	parametro_clase					varchar(100)	NULL,
   	CONSTRAINT   apex_objeto_msg_pk  PRIMARY KEY ( objeto_msg ,  objeto_proyecto ),
   	CONSTRAINT   apex_objeto_msg_fk_objeto  FOREIGN KEY ( objeto ,  objeto_proyecto ) REFERENCES  apex_objeto  ( objeto ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
   	CONSTRAINT   apex_objeto_msg_fk_tipo  FOREIGN KEY ( msg_tipo ) REFERENCES  apex_msg_tipo  ( msg_tipo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

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

CREATE TABLE apex_permiso
(
	permiso					integer auto_increment  NOT NULL, 
	proyecto							varchar(15)		NOT NULL,
	nombre								varchar(100)	NOT NULL,
	descripcion							varchar(255)	NULL,
	mensaje_particular					text			NULL,
	CONSTRAINT	 apex_per_pk  			PRIMARY	KEY ( permiso ,  proyecto ),
	CONSTRAINT	 apex_per_uq_nombre  	UNIQUE	( proyecto , nombre )
) ENGINE=InnoDB;

	ALTER TABLE apex_permiso_grupo_acc ADD CONSTRAINT   apex_per_grupo_acc_per_fk 
	FOREIGN KEY ( permiso , proyecto ) 
	REFERENCES  apex_permiso  ( permiso , proyecto ) 
	ON	DELETE NO ACTION 
	ON UPDATE	NO	ACTION 
	 
	 
	;

	ALTER TABLE apex_usuario_grupo_acc_item ADD CONSTRAINT	 apex_usu_item_fk_item 	
	FOREIGN KEY	( proyecto , item ) 
	REFERENCES  apex_item  ( proyecto , item )	
	ON	DELETE CASCADE 
	ON UPDATE	NO	ACTION 
	 
	 
	;

	ALTER TABLE apex_proyecto ADD CONSTRAINT	 apex_proyecto_fk_menu  
	FOREIGN KEY ( menu ) 
	REFERENCES	 apex_menu  ( menu ) 
	ON DELETE NO ACTION	
	ON	UPDATE NO ACTION 
	 
	
	;

	ALTER TABLE apex_proyecto ADD CONSTRAINT	 apex_proyecto_fk_pagina_tipo  
	FOREIGN KEY ( proyecto ,  pagina_tipo ) 
	REFERENCES	 apex_pagina_tipo  ( proyecto , pagina_tipo ) 
	ON DELETE NO ACTION	
	ON	UPDATE NO ACTION 
	 
	
	;



	ALTER TABLE apex_usuario_grupo_acc ADD CONSTRAINT  apex_usu_g_acc_fk_proy 
	FOREIGN KEY ( proyecto )
	REFERENCES  apex_proyecto  ( proyecto )
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	
	
	;

	ALTER TABLE apex_usuario_proyecto ADD CONSTRAINT  apex_usu_proy_fk_proyecto 
	FOREIGN KEY ( proyecto )
	REFERENCES  apex_proyecto  ( proyecto )
	ON DELETE NO ACTION
	ON UPDATE NO ACTION
	
	
	;


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

CREATE TABLE apex_objeto_cuadro
(
	objeto_cuadro_proyecto  	varchar(15)		NOT NULL,
	objeto_cuadro           integer		NOT NULL,
	titulo                  	varchar(255) 	NULL,
	subtitulo               	varchar(255) 	NULL,
nosql                    	text     	NULL,       
	columnas_clave				text			NULL,   
	clave_dbr					smallint		NULL,
	archivos_callbacks      	varchar(255)	NULL,			
	ancho                   	varchar(10) 	NULL,
	ordenar                 	smallint    	NULL,
	paginar                 	smallint    	NULL,
	tamano_pagina           	smallint    	NULL,
	tipo_paginado				varchar(1)  	NULL,
	eof_invisible           	smallint    	NULL,   
	eof_customizado       		text			NULL,
	exportar		           	smallint       	NULL,		
	exportar_rtf            	smallint       	NULL,		
	pdf_propiedades         	text			NULL,
	pdf_respetar_paginacion 	smallint       	NULL,  		
	asociacion_columnas			varchar(255)	NULL,
	ev_seleccion				smallint		NULL,		
	ev_eliminar					smallint		NULL,		
	dao_nucleo_proyecto			varchar(15)		NULL,
	dao_nucleo					varchar(60)		NULL,
	dao_metodo					varchar(80)		NULL,
	dao_parametros				varchar(255)	NULL,
	desplegable					smallint		NULL,
	desplegable_activo			smallint		NULL,
	scroll						smallint		NULL,
	scroll_alto					varchar(10)		NULL,
	cc_modo						varchar(1)		NULL,		
	cc_modo_anidado_colap		smallint		NULL,		
	cc_modo_anidado_totcol		smallint		NULL,		
	cc_modo_anidado_totcua		smallint		NULL,		
	CONSTRAINT   apex_objeto_cuadro_pk  PRIMARY KEY ( objeto_cuadro ,  objeto_cuadro_proyecto ),
	CONSTRAINT   apex_objeto_cuadro_fk_objeto   FOREIGN KEY ( objeto_cuadro ,  objeto_cuadro_proyecto ) REFERENCES    apex_objeto  ( objeto ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_cuadro_cc
(
	objeto_cuadro_proyecto        	varchar(15)		NOT NULL,
	objeto_cuadro                 integer      		NOT NULL,
	objeto_cuadro_cc			integer		 auto_increment  NOT NULL, 
	identificador					varchar(200)		NULL,			
	descripcion						varchar(200)		NULL,
	orden				            float      		NOT NULL,
	columnas_id	    				varchar(200)	NOT NULL,		
	columnas_descripcion			varchar(200)	NOT NULL,		
	pie_contar_filas				varchar(10)		NULL,
	pie_mostrar_titular				smallint		NULL,			
	pie_mostrar_titulos				smallint		NULL,			
	imp_paginar						smallint		NULL,		
	modo_inicio_colapsado			smallint		NULL DEFAULT 0,			
	CONSTRAINT   apex_obj_cuadro_cc_pk  PRIMARY KEY ( objeto_cuadro_cc ,  objeto_cuadro_proyecto , objeto_cuadro ),
	CONSTRAINT   apex_obj_cuadro_cc_uq  UNIQUE ( objeto_cuadro_proyecto , objeto_cuadro , identificador ),
	CONSTRAINT   apex_obj_cuadro_cc_fk_objeto_cuadro  FOREIGN KEY ( objeto_cuadro ,  objeto_cuadro_proyecto ) REFERENCES  apex_objeto_cuadro  ( objeto_cuadro ,  objeto_cuadro_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_ei_cuadro_columna
(
	objeto_cuadro_proyecto        	varchar(15)		NOT NULL,
	objeto_cuadro                 integer      		NOT NULL,
	objeto_cuadro_col			integer		 auto_increment  NOT NULL, 
	clave          					varchar(80)    	NOT NULL,		
	orden				            float      		NOT NULL,
	titulo                        	varchar(255)	NULL,
	estilo_titulo                   varchar(100)	DEFAULT 'ei-cuadro-col-tit' NULL,
	estilo    					integer	    NOT NULL,	
	ancho							varchar(10)		NULL,		
	formateo   					integer	    NULL,		
	vinculo_indice	      			varchar(20) 	NULL,       
	no_ordenar						smallint		NULL,		
	mostrar_xls						smallint		NULL,
	mostrar_pdf						smallint		NULL,
	pdf_propiedades          		text			NULL,
	desabilitado					smallint		NULL,
	total							smallint		NULL,		
	total_cc						varchar(100)	NULL,			
	usar_vinculo					smallint			NULL,
	vinculo_carpeta					varchar(60)			NULL,
	vinculo_item					varchar(60)			NULL,
	vinculo_popup					smallint			NULL,
	vinculo_popup_param				varchar(100)		NULL,
	vinculo_target					varchar(40)			NULL,
	vinculo_celda					varchar(40)			NULL,
	CONSTRAINT   apex_obj_ei_cuadro_pk  PRIMARY KEY ( objeto_cuadro_col ,  objeto_cuadro ,  objeto_cuadro_proyecto ),
	CONSTRAINT   apex_obj_ei_cuadro_fk_objeto_cuadro  FOREIGN KEY ( objeto_cuadro ,  objeto_cuadro_proyecto ) REFERENCES  apex_objeto_cuadro  ( objeto_cuadro ,  objeto_cuadro_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_obj_ei_cuadro_fk_formato  FOREIGN KEY ( formateo ) REFERENCES  apex_columna_formato  ( columna_formato ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_obj_ei_cuadro_fk_estilo  FOREIGN KEY ( estilo ) REFERENCES  apex_columna_estilo  ( columna_estilo ) ON DELETE NO ACTION ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_ut_formulario
(
	objeto_ut_formulario_proyecto    	varchar(15)		NOT NULL,
	objeto_ut_formulario       		integer 			NOT NULL,
	tabla                      			varchar(100)   	NULL,
	titulo                     			varchar(255)   	NULL,       
	ev_agregar							smallint		NULL,		
	ev_agregar_etiq						varchar(30)		NULL,
	ev_mod_modificar					smallint		NULL,
	ev_mod_modificar_etiq				varchar(30)		NULL,
   	ev_mod_eliminar            			smallint       	NULL,       
	ev_mod_eliminar_etiq				varchar(30)		NULL,
	ev_mod_limpiar	           			smallint       	NULL,       
	ev_mod_limpiar_etiq					varchar(30)		NULL,
   	ev_mod_clave      	      			smallint       	NULL,       
	clase_proyecto						varchar(15)		NULL, 		
	clase								varchar(60)		NULL,
	auto_reset							smallint       	NULL,       
   ancho                   				varchar(10)    	NULL,	
   ancho_etiqueta						varchar(10)		NULL,
   expandir_descripcion					smallint		NULL,
	campo_bl							varchar(40)		NULL,
	scroll								smallint		NULL,
	filas								smallint       	NULL,
	filas_agregar						smallint       	NULL,
	filas_agregar_online				smallint		NULL DEFAULT 1,
	filas_undo							smallint		NULL,
	filas_ordenar						smallint		NULL,
	columna_orden						varchar(100)	NULL,
	filas_numerar						smallint 		NULL,
	ev_seleccion						smallint		NULL,
	alto								varchar(10)		NULL,
	analisis_cambios					varchar(10)		NULL,
	CONSTRAINT   apex_objeto_ut_f_pk  PRIMARY KEY ( objeto_ut_formulario ,  objeto_ut_formulario_proyecto ),
	CONSTRAINT   apex_objeto_ut_f_fk_objeto  FOREIGN KEY ( objeto_ut_formulario ,  objeto_ut_formulario_proyecto ) REFERENCES  apex_objeto  ( objeto ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_objeto_ei_formulario_ef
(
	objeto_ei_formulario_fila		integer		 auto_increment  NOT NULL, 
	objeto_ei_formulario             integer		NOT NULL,
	objeto_ei_formulario_proyecto    	varchar(15)		NOT NULL,
	identificador      					varchar(30)    	NOT NULL,
	elemento_formulario     			varchar(30)    	NOT NULL,
	columnas                			varchar(255)   	NOT NULL,
	obligatorio             			smallint       	NULL,	
	oculto_relaja_obligatorio			smallint		NULL,		
	orden                   			float       	NOT NULL,
	etiqueta                			varchar(80)    	NULL,
	etiqueta_estilo            			varchar(80)    	NULL,
	descripcion             			text        	NULL,
	colapsado							smallint		NULL,
	desactivado             			smallint       	NULL,
	estilo   				 		integer	    NULL,		
	total								smallint		NULL,		
	inicializacion          			text        	NULL,
	estado_defecto						varchar(255)	NULL,
	solo_lectura						smallint		NULL,
	carga_metodo						varchar(100)	NULL,	
	carga_clase							varchar(100)	NULL,	
	carga_include						varchar(255)	NULL,
	carga_dt						integer		NULL,	
	carga_consulta_php				integer		NULL,	
	carga_sql							text			NULL,	
	carga_fuente						varchar(30)		NULL,
	carga_lista							varchar(255)	NULL,	
	carga_col_clave						varchar(100)	NULL,
	carga_col_desc						varchar(100)	NULL,
	carga_maestros						varchar(255)	NULL,
	carga_cascada_relaj					smallint		NULL,
	carga_no_seteado					varchar(100)	NULL,
	carga_no_seteado_ocultar			smallint		NULL,
	edit_tamano							smallint		NULL,
	edit_maximo							smallint		NULL,
	edit_mascara						varchar(100)	NULL,
	edit_unidad							varchar(255)	NULL,
	edit_rango							varchar(100)	NULL,
	edit_filas							smallint		NULL,
	edit_columnas						smallint		NULL,
	edit_wrap							varchar(20)		NULL,
	edit_resaltar						smallint		NULL,
	edit_ajustable						smallint		NULL,
	edit_confirmar_clave				smallint		NULL,
	popup_item							varchar(60)		NULL,
	popup_proyecto						varchar(15)		NULL,
	popup_editable						smallint		NULL,
	popup_ventana						varchar(50)		NULL,
	popup_carga_desc_metodo				varchar(100)	NULL,
	popup_carga_desc_clase				varchar(100)	NULL,
	popup_carga_desc_include			varchar(255)	NULL,
	fieldset_fin						smallint		NULL,
	check_valor_si						varchar(40)		NULL,
	check_valor_no						varchar(40)		NULL,
	check_desc_si						varchar(100)	NULL,
	check_desc_no						varchar(100)	NULL,
	fijo_sin_estado						smallint		NULL,
	editor_ancho						varchar(10)		NULL,
	editor_alto							varchar(10)		NULL,
	editor_botonera						varchar(50)		NULL,
	selec_cant_minima					smallint		NULL,
	selec_cant_maxima					smallint		NULL,
	selec_utilidades					smallint		NULL,
	selec_tamano						smallint		NULL,
	selec_ancho							varchar(30)		NULL,
	selec_serializar					smallint		NULL,
	selec_cant_columnas					smallint		NULL,
	upload_extensiones					varchar(255)	NULL,
	CONSTRAINT   apex_ei_f_ef_pk  PRIMARY KEY ( objeto_ei_formulario_fila ,  objeto_ei_formulario ,  objeto_ei_formulario_proyecto ),
	CONSTRAINT   apex_ei_f_ef_fk_padre  FOREIGN KEY ( objeto_ei_formulario ,  objeto_ei_formulario_proyecto ) REFERENCES  apex_objeto_ut_formulario  ( objeto_ut_formulario ,  objeto_ut_formulario_proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_ei_f_ef_fk_estilo  FOREIGN KEY ( estilo ) REFERENCES  apex_columna_estilo  ( columna_estilo ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_ei_f_ef_fk_ef  FOREIGN KEY ( elemento_formulario ) REFERENCES  apex_elemento_formulario  ( elemento_formulario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_ei_f_ef_fk_datos_tabla  FOREIGN KEY ( objeto_ei_formulario_proyecto , carga_dt ) REFERENCES  apex_objeto  ( proyecto , objeto ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_ei_f_ef_fk_consulta_php  FOREIGN KEY ( objeto_ei_formulario_proyecto , carga_consulta_php ) REFERENCES  apex_consulta_php  ( proyecto ,  consulta_php ) ON DELETE NO ACTION ON UPDATE NO ACTION   

) ENGINE=InnoDB;

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

CREATE TABLE apex_molde_opciones_generacion
(
	proyecto						varchar(15)			NOT NULL,
	uso_autoload					smallint			NULL,		
	origen_datos_cuadro				varchar(20)			NULL,		
	carga_php_include				varchar(255)		NULL,		
	carga_php_clase					varchar(255)		NULL,		
	CONSTRAINT  apex_molde_opciones_generacion_pk  PRIMARY KEY( proyecto ),
	CONSTRAINT  apex_molde_opciones_generacion_fk_proy  	FOREIGN KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto ) ON	DELETE CASCADE ON UPDATE CASCADE   
) ENGINE=InnoDB;

CREATE TABLE apex_molde_operacion_tipo
(	
	operacion_tipo				integer			 auto_increment  NOT	NULL,	
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	clase							varchar(255)		NOT NULL,
	ci								varchar(255)		NOT NULL,
	icono							varchar(30)			NULL,
	vista_previa					varchar(100)		NULL,
	orden							float				NULL,
	CONSTRAINT	 apex_molde_operacion_tipo_pk 	 PRIMARY	KEY ( operacion_tipo )
) ENGINE=InnoDB;

CREATE TABLE apex_molde_operacion_tipo_dato
(	
	tipo_dato					integer			 auto_increment  NOT	NULL,
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	dt_tipo_dato					varchar(1)			NULL,		
	elemento_formulario				varchar(30)			NULL,
	cuadro_estilo 				integer	    	NULL,	
	cuadro_formato 				integer	    	NULL,	
	orden							float				NULL,
	CONSTRAINT	 apex_molde_operacion_tipo_dato_pk 	PRIMARY	KEY ( tipo_dato ),
	CONSTRAINT   apex_molde_operacion_tipo_dato_fk_ef  FOREIGN KEY ( elemento_formulario ) REFERENCES  apex_elemento_formulario  ( elemento_formulario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_tipo_dato_fk_estilo  FOREIGN KEY ( cuadro_estilo ) REFERENCES  apex_columna_estilo  ( columna_estilo ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_tipo_dato_fk_formato  FOREIGN KEY ( cuadro_formato ) REFERENCES  apex_columna_formato  ( columna_formato ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_tipo_dato_fk_tipo_datos  FOREIGN KEY ( dt_tipo_dato ) REFERENCES  apex_tipo_datos  ( tipo ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_molde_operacion
(
	proyecto  					varchar(255)	NOT NULL,
	molde					integer		 auto_increment  		NOT NULL, 
	operacion_tipo			integer		NOT NULL,
	nombre                  	varchar(255) 	NULL,
 item varchar(60) NOT NULL ,
	carpeta_archivos           	varchar(255) 	NOT NULL,
	prefijo_clases				varchar(30)		NOT NULL,
	fuente						varchar(20)		NOT NULL,
	CONSTRAINT   apex_molde_operacion_pk  PRIMARY KEY ( molde ,  proyecto ),
	CONSTRAINT 	 apex_molde_operacion_item  UNIQUE ( item ),
	CONSTRAINT	 apex_molde_operacion_proy  FOREIGN	KEY ( proyecto ) REFERENCES  apex_proyecto  ( proyecto )	ON	DELETE NO ACTION ON UPDATE	NO	ACTION   ,
	CONSTRAINT	 apex_molde_operacion_fk_item  FOREIGN	KEY ( item ,  proyecto ) REFERENCES	 apex_item 	( item ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION		 ,
	CONSTRAINT   apex_molde_operacion_fk_tipo   FOREIGN KEY ( operacion_tipo ) REFERENCES    apex_molde_operacion_tipo  ( operacion_tipo ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT	 apex_molde_operacion_abms_fk_fuente  FOREIGN KEY	( proyecto , fuente ) REFERENCES  apex_fuente_datos 	( proyecto , fuente_datos ) ON DELETE NO ACTION	ON	UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_molde_operacion_log
(
	proyecto  					varchar(255)	NOT NULL,
	molde					integer 		NOT NULL, 
	generacion				integer		 auto_increment  		NOT NULL, 
	momento					 timestamp 	DEFAULT current_timestamp NOT NULL,
	CONSTRAINT   apex_molde_operacion_log_pk  PRIMARY KEY ( generacion ),
	CONSTRAINT   apex_molde_operacion_log_fk  FOREIGN KEY ( molde ,  proyecto ) REFERENCES  apex_molde_operacion  ( molde ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_molde_operacion_log_elementos
(
	generacion				integer		NOT NULL, 
	molde					integer 		NOT NULL, 
	id						integer		 auto_increment  		NOT NULL, 
	tipo						varchar(255)	NOT NULL,
	proyecto					varchar(255)	NOT NULL,
	clave						varchar(255)	NOT NULL, 
	CONSTRAINT   apex_molde_operacion_log_e_pk  PRIMARY KEY ( id ),
	CONSTRAINT   apex_molde_operacion_log_e_fk  FOREIGN KEY ( generacion ) REFERENCES  apex_molde_operacion_log  ( generacion ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;


CREATE TABLE apex_molde_operacion_abms
(
	proyecto  							varchar(255)	NOT NULL,
	molde							integer		NOT NULL, 
	tabla								varchar(255)	NOT NULL,
	gen_usa_filtro						smallint		NULL,
	gen_separar_pantallas				smallint		NULL,
	filtro_comprobar_parametros			smallint		NULL,
	cuadro_eof							varchar(255)	NULL,
	cuadro_eliminar_filas				smallint		NULL,
	cuadro_id							varchar(255)	NULL,
	cuadro_forzar_filtro				smallint		NULL,
	cuadro_carga_origen					varchar(15)		NULL,
	cuadro_carga_sql					text			NULL,
	cuadro_carga_php_include			varchar(255)	NULL,
	cuadro_carga_php_clase				varchar(255)	NULL,
	cuadro_carga_php_metodo				varchar(255)	NULL,
	datos_tabla_validacion				smallint		NULL,
	apdb_pre							smallint		NULL,	
	CONSTRAINT   apex_molde_operacion_abms_pk  PRIMARY KEY ( proyecto , molde ),
	CONSTRAINT   apex_molde_operacion_abms_fk_molde  FOREIGN KEY ( molde ,  proyecto ) REFERENCES  apex_molde_operacion  ( molde ,  proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;

CREATE TABLE apex_molde_operacion_abms_fila
(
	proyecto  							varchar(255)	NOT NULL,
	molde							integer		NOT NULL, 
	fila							integer		 auto_increment  NOT NULL,
	orden								float			NOT NULL,
	columna        						varchar(255)   	NOT NULL,
	asistente_tipo_dato				integer	   	NULL,
	etiqueta       						varchar(255)   	NULL,
	en_cuadro							smallint		NULL,
	en_form								smallint		NULL,
	en_filtro							smallint		NULL,
	filtro_operador						varchar(10)		NULL, 
	cuadro_estilo 					integer	   	NULL,	
	cuadro_formato 					integer	  	NULL,	
	dt_tipo_dato						varchar(1)		NULL,
	dt_largo							smallint		NULL,
	dt_secuencia						varchar(255)	NULL,
	dt_pk								smallint		NULL,
	elemento_formulario					varchar(30)		NULL,
	ef_obligatorio						smallint		NULL,
	ef_desactivar_modificacion			smallint		NULL,
	ef_procesar_javascript				smallint		NULL,
	ef_carga_origen						varchar(15)		NULL,
	ef_carga_sql						text			NULL,
	ef_carga_php_include				varchar(255)	NULL,
	ef_carga_php_clase					varchar(255)	NULL,
	ef_carga_php_metodo					varchar(255)	NULL,
	ef_carga_tabla						varchar(255)	NULL,
	ef_carga_col_clave					varchar(255)	NULL,
	ef_carga_col_desc					varchar(255)	NULL,
	CONSTRAINT   apex_molde_operacion_abms_fila_pk  PRIMARY KEY ( fila , molde , proyecto ),
	CONSTRAINT	 apex_molde_operacion_abms_fila_uq  UNIQUE 	( proyecto , molde , columna ),
	CONSTRAINT   apex_molde_operacion_abms_fila_fk_molde  FOREIGN KEY ( molde , proyecto ) REFERENCES  apex_molde_operacion  ( molde , proyecto ) ON DELETE CASCADE ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_abms_fila  FOREIGN KEY ( asistente_tipo_dato ) REFERENCES  apex_molde_operacion_tipo_dato  ( tipo_dato ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_abms_fila_fk_ef  FOREIGN KEY ( elemento_formulario ) REFERENCES  apex_elemento_formulario  ( elemento_formulario ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_abms_fila_fk_estilo  FOREIGN KEY ( cuadro_estilo ) REFERENCES  apex_columna_estilo  ( columna_estilo ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_abms_fila_fk_formato  FOREIGN KEY ( cuadro_formato ) REFERENCES  apex_columna_formato  ( columna_formato ) ON DELETE NO ACTION ON UPDATE NO ACTION   ,
	CONSTRAINT   apex_molde_operacion_abms_fila_fk_tipo_datos  FOREIGN KEY ( dt_tipo_dato ) REFERENCES  apex_tipo_datos  ( tipo ) ON DELETE CASCADE ON UPDATE NO ACTION   
) ENGINE=InnoDB;
