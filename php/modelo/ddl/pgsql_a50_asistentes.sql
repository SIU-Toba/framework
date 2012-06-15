--**************************************************************************************************
--**************************************************************************************************
--************************     molde de construccion de operaciones    ******************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_molde_opciones_generacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	uso_autoload					smallint			NULL,		-- Hace que no se generen require_once
	origen_datos_cuadro				varchar(20)			NULL,		-- metodologia usada para proveer datos: consulta_php, datos_tabla
	punto_montaje					int8			NULL,
	carga_php_include				TEXT		NULL,		-- consulta_php por defecto
	carga_php_clase					TEXT		NULL,		-- consulta_php por defecto
	CONSTRAINT "apex_molde_opciones_generacion_pk" PRIMARY KEY("proyecto"),
	CONSTRAINT "apex_molde_opciones_generacion_fk_proy" 	FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_opciones_generacion_fk_puntos_montaje" FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_molde_operacion_tipo_seq	INCREMENT 1	MINVALUE	0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_molde_operacion_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: operacion_tipo
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	operacion_tipo					int8				DEFAULT nextval('"apex_molde_operacion_tipo_seq"'::text) NOT	NULL,	
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						TEXT		NULL,
	clase							TEXT		NOT NULL,
	ci								TEXT		NOT NULL,
	icono							varchar(30)			NULL,
	vista_previa					TEXT		NULL,
	orden							float				NULL,
	CONSTRAINT	"apex_molde_operacion_tipo_pk"	 PRIMARY	KEY ("operacion_tipo")
);
--#################################################################################################

CREATE SEQUENCE apex_molde_operacion_tipo_dato_seq	INCREMENT 1	MINVALUE	0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_molde_operacion_tipo_dato
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: tipo_dato
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	tipo_dato						int8				DEFAULT nextval('"apex_molde_operacion_tipo_dato_seq"'::text) NOT	NULL,
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						TEXT		NULL,
	dt_tipo_dato					varchar(1)			NULL,		
	elemento_formulario				varchar(30)			NULL,
	cuadro_estilo 					int8		    	NULL,	
	cuadro_formato 					int8		    	NULL,
	orden							float				NULL,
	filtro_operador					varchar(10)			NULL,
	CONSTRAINT	"apex_molde_operacion_tipo_dato_pk"	PRIMARY	KEY ("tipo_dato"),
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_ef" FOREIGN KEY ("elemento_formulario") REFERENCES "apex_elemento_formulario" ("elemento_formulario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_estilo" FOREIGN KEY ("cuadro_estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_formato" FOREIGN KEY ("cuadro_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_tipo_dato_fk_tipo_datos" FOREIGN KEY ("dt_tipo_dato") REFERENCES "apex_tipo_datos" ("tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_molde_operacion_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: molde
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  					varchar(255)	NOT NULL,
	molde						int8			DEFAULT nextval('"apex_molde_operacion_seq"'::text) 		NOT NULL, 
	operacion_tipo				int8			NOT NULL,
	nombre                  	TEXT 	NULL,
	item						varchar(60)		NOT NULL,
	carpeta_archivos           	TEXT 	NOT NULL,
	prefijo_clases				varchar(30)		NOT NULL,
	fuente						varchar(20)		NOT NULL,
	punto_montaje					int8			NULL,
	CONSTRAINT  "apex_molde_operacion_pk" PRIMARY KEY ("molde", "proyecto"),
	CONSTRAINT 	"apex_molde_operacion_item" UNIQUE ("proyecto","item"),
	CONSTRAINT	"apex_molde_operacion_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_operacion_fk_item" FOREIGN	KEY ("item", "proyecto") REFERENCES	"apex_item"	("item", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_fk_tipo"  FOREIGN KEY ("operacion_tipo") REFERENCES   "apex_molde_operacion_tipo" ("operacion_tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_operacion_abms_fk_fuente" FOREIGN KEY	("proyecto","fuente") REFERENCES "apex_fuente_datos"	("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_operacion_fk_puntos_montaje" FOREIGN KEY ("proyecto", "punto_montaje")	REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_molde_operacion_log_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion_log
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: generacion
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  					varchar(255)	NOT NULL,
	molde						int8	 		NOT NULL, 
	generacion					int8			DEFAULT nextval('"apex_molde_operacion_log_seq"'::text) 		NOT NULL, 
	momento						timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	CONSTRAINT  "apex_molde_operacion_log_pk" PRIMARY KEY ("generacion"),
	CONSTRAINT  "apex_molde_operacion_log_fk" FOREIGN KEY ("molde", "proyecto") REFERENCES "apex_molde_operacion" ("molde", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_molde_operacion_log_elementos_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion_log_elementos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: generacion
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	generacion					int8			NOT NULL, 
	molde						int8	 		NOT NULL, 
	id							int8			DEFAULT nextval('"apex_molde_operacion_log_elementos_seq"'::text) 		NOT NULL, 
	tipo						TEXT	NOT NULL,
	proyecto					TEXT	NOT NULL,
	clave						TEXT	NOT NULL,
	CONSTRAINT  "apex_molde_operacion_log_e_pk" PRIMARY KEY ("id"),
	CONSTRAINT  "apex_molde_operacion_log_e_fk" FOREIGN KEY ("generacion") REFERENCES "apex_molde_operacion_log" ("generacion") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--**************************************************************************************************
--**************************************************************************************************
--************************                 ABM SIMPLE                 ******************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_molde_operacion_abms
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: molde
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  							varchar(255)	NOT NULL,
	molde								int8			NOT NULL, 
	tabla								TEXT	NOT NULL,
	gen_usa_filtro						smallint		NULL,
	gen_separar_pantallas				smallint		NULL,
	filtro_comprobar_parametros			smallint		NULL,
	cuadro_eof							TEXT	NULL,
	cuadro_eliminar_filas				smallint		NULL,
	cuadro_id							TEXT	NULL,
	cuadro_forzar_filtro				smallint		NULL,
	cuadro_carga_origen					varchar(15)		NULL,
	cuadro_carga_sql					TEXT			NULL,
	cuadro_carga_php_include			TEXT	NULL,
	cuadro_carga_php_clase				TEXT	NULL,
	cuadro_carga_php_metodo				TEXT	NULL,
	datos_tabla_validacion				smallint		NULL,
	apdb_pre							smallint		NULL,	-- Hay que poner uno por ventana.
	punto_montaje					int8			NULL,
	CONSTRAINT  "apex_molde_operacion_abms_pk" PRIMARY KEY ("proyecto","molde"),
	CONSTRAINT  "apex_molde_operacion_abms_fk_molde" FOREIGN KEY ("molde", "proyecto") REFERENCES "apex_molde_operacion" ("molde", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_operacion_abms_fk_puntos_montaje" FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_molde_operacion_abms_fila_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_molde_operacion_abms_fila
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: molde, fila
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  							varchar(255)	NOT NULL,
	molde								int8			NOT NULL, 
	fila								int8			DEFAULT nextval('"apex_molde_operacion_abms_fila_seq"'::text) NOT NULL,
	orden								float			NOT NULL,
	columna        						TEXT   	NOT NULL,
	asistente_tipo_dato					int8		   	NULL,
	etiqueta       						TEXT   	NULL,
	en_cuadro							smallint		NULL,
	en_form								smallint		NULL,
	en_filtro							smallint		NULL,
	filtro_operador						varchar(10)		NULL, -- Que operador utilizar? (=, <>, >, <, LIKE, etc)
	cuadro_estilo 						int8		   	NULL,	
	cuadro_formato 						int8		  	NULL,	
	dt_tipo_dato						varchar(1)		NULL,
	dt_largo							smallint		NULL,
	dt_secuencia						TEXT	NULL,
	dt_pk								smallint		NULL,
	elemento_formulario					varchar(30)		NULL,
	ef_obligatorio						smallint		NULL,
	ef_desactivar_modificacion			smallint		NULL,
	ef_procesar_javascript				smallint		NULL,
	ef_carga_origen						varchar(15)		NULL,
	ef_carga_sql						varchar			NULL,
	ef_carga_php_include				TEXT	NULL,
	ef_carga_php_clase					TEXT	NULL,
	ef_carga_php_metodo					TEXT	NULL,
	ef_carga_tabla						TEXT	NULL,
	ef_carga_col_clave					TEXT	NULL,
	ef_carga_col_desc					TEXT	NULL,
	punto_montaje					int8			NULL,
	CONSTRAINT  "apex_molde_operacion_abms_fila_pk" PRIMARY KEY ("fila","molde","proyecto"),
	CONSTRAINT	"apex_molde_operacion_abms_fila_uq" UNIQUE 	("proyecto","molde","columna"),
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_molde" FOREIGN KEY ("molde","proyecto") REFERENCES "apex_molde_operacion" ("molde","proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila" FOREIGN KEY ("asistente_tipo_dato") REFERENCES "apex_molde_operacion_tipo_dato" ("tipo_dato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_ef" FOREIGN KEY ("elemento_formulario") REFERENCES "apex_elemento_formulario" ("elemento_formulario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_estilo" FOREIGN KEY ("cuadro_estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_formato" FOREIGN KEY ("cuadro_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_molde_operacion_abms_fila_fk_tipo_datos" FOREIGN KEY ("dt_tipo_dato") REFERENCES "apex_tipo_datos" ("tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_molde_operacion_abms_fila_fk_puntos_montaje" FOREIGN KEY ("proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################


CREATE TABLE apex_molde_operacion_importacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: molde
--: dump_order_by: molde
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  							varchar(255)	NOT NULL,
	molde								int8			NOT NULL, 
	origen_item							varchar(60)		NOT NULL,
	origen_proyecto						varchar(30)		NULL,
	CONSTRAINT  "apex_molde_operacion_imp_pk" 		PRIMARY KEY ("proyecto","molde"),
	CONSTRAINT  "apex_molde_operacion_imp_fk_molde" FOREIGN KEY ("molde", "proyecto") REFERENCES "apex_molde_operacion" ("molde", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
