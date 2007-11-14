
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
