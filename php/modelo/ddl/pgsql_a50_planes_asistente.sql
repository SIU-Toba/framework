--**************************************************************************************************
--**************************************************************************************************
--************************     Plan de construccion de operaciones    ******************************
--**************************************************************************************************
--**************************************************************************************************

CREATE SEQUENCE apex_plan_operacion_tipo_seq	INCREMENT 1	MINVALUE	0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_plan_operacion_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: operacion_tipo
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	operacion_tipo					int4				DEFAULT nextval('"apex_clase_tipo_seq"'::text) NOT	NULL,	
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	clase							varchar(255)		NOT NULL,
	icono							varchar(30)			NULL,
	orden							float				NULL,
	CONSTRAINT	"apex_plan_operacion_tipo_pk"	 PRIMARY	KEY ("operacion_tipo")
);
--#################################################################################################

CREATE SEQUENCE apex_plan_operacion_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_plan_operacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: plan
--: dump_order_by: plan
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  					varchar(255)	NOT NULL,
	plan						int4			DEFAULT nextval('"apex_plan_operacion_seq"'::text) 		NOT NULL, 
	operacion_tipo				int4			NOT NULL,
	nombre                  	varchar(255) 	NOT NULL,
	carpeta_item				varchar(60)		NOT NULL,
	carpeta_archivos           	varchar(255) 	NOT NULL,
	CONSTRAINT  "apex_plan_operacion_pk" PRIMARY KEY ("proyecto","plan"),
	CONSTRAINT	"apex_plan_operacion_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_plan_operacion_fk_carpeta" FOREIGN	KEY ("proyecto","carpeta_item") REFERENCES	"apex_item"	("proyecto","item") ON DELETE CASCADE ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_plan_operacion_fk_tipo"  FOREIGN KEY ("operacion_tipo") REFERENCES   "apex_plan_operacion_tipo" ("operacion_tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_plan_operacion_log_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_plan_operacion_log
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: plan
--: dump_order_by: generacion
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  					varchar(255)	NOT NULL,
	plan						int4	 		NOT NULL, 
	generacion					int4			DEFAULT nextval('"apex_plan_operacion_log_seq"'::text) 		NOT NULL, 
	momento						timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	CONSTRAINT  "apex_plan_operacion_log_pk" PRIMARY KEY ("generacion"),
	CONSTRAINT  "apex_plan_operacion_log_fk" FOREIGN KEY ("proyecto","plan") REFERENCES "apex_plan_operacion" ("proyecto","plan") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_plan_operacion_log_elementos_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_plan_operacion_log_elementos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: plan
--: dump_order_by: generacion
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	generacion					int4			NOT NULL, 
	plan						int4	 		NOT NULL, 
	id							int4			DEFAULT nextval('"apex_plan_operacion_log_elementos_seq"'::text) 		NOT NULL, 
	tipo						varchar(255)	NOT NULL,
	proyecto					varchar(255)	NOT NULL,
	clave						varchar(255)	NOT NULL, 
	CONSTRAINT  "apex_plan_operacion_log_e_pk" PRIMARY KEY ("id"),
	CONSTRAINT  "apex_plan_operacion_log_e_fk" FOREIGN KEY ("generacion") REFERENCES "apex_plan_operacion_log" ("generacion") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);



--**************************************************************************************************
--**************************************************************************************************
--************************                 ABM SIMPLE                 ******************************
--**************************************************************************************************
--**************************************************************************************************


CREATE TABLE apex_plan_operacion_abms
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: plan
--: dump_order_by: plan
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  							varchar(255)	NOT NULL,
	plan								int4			NOT NULL, 
	tabla								varchar(255)	NOT NULL,
	gen_usa_filtro						smallint		NULL,
	gen_separar_pantallas				smallint		NULL,
	cuadro_eof							varchar(255)	NULL,
	cuadro_id							varchar(255)	NULL,
	cuadro_eliminar_filas				smallint		NULL,
	cuadro_datos_origen					varchar(10)		NULL,
	cuadro_datos_origen_ci_sql			varchar			NULL,
	cuadro_datos_orgien_php_archivo		varchar(255)	NULL,
	cuadro_datos_orgien_php_clase		varchar(255)	NULL,
	cuadro_datos_orgien_php_metodo		varchar(255)	NULL,
	datos_tabla_validacion				smallint		NULL,
	apdb_pre							smallint		NULL,	-- Hay que poner uno por ventana.
	CONSTRAINT  "apex_plan_operacion_abms_pk" PRIMARY KEY ("proyecto","plan"),
	CONSTRAINT  "apex_plan_operacion_abms_fk_plan" FOREIGN KEY ("proyecto","plan") REFERENCES "apex_plan_operacion" ("proyecto","plan") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_plan_operacion_abms_fila_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_plan_operacion_abms_fila
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: plan
--: dump_order_by: plan, fila
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  							varchar(255)	NOT NULL,
	plan								int4			NOT NULL, 
	fila								int4			DEFAULT nextval('"apex_plan_operacion_abms_fila_seq"'::text) NOT NULL,
	orden								float			NOT NULL,
	columna        						varchar(255)   	NOT NULL,
	etiqueta       						varchar(255)   	NULL,
	en_cuadro							smallint		NULL,
	en_form								smallint		NULL,
	en_filtro							smallint		NULL,
	elemento_formulario					varchar(30)		NULL,
	ef_desactivar_modificacion			smallint		NULL,
	ef_procesar_javascript				smallint		NULL,
	ef_datos_origen						varchar(10)		NULL,
	ef_datos_origen_ci_sql				varchar			NULL,
	ef_datos_orgien_php_archivo			varchar(255)	NULL,
	ef_datos_orgien_php_clase			varchar(255)	NULL,
	ef_datos_orgien_php_metodo			varchar(255)	NULL,
	CONSTRAINT  "apex_plan_operacion_abms_fila_pk" PRIMARY KEY ("proyecto","plan","fila"),
	CONSTRAINT  "apex_plan_operacion_abms_fila_fk_plan" FOREIGN KEY ("proyecto","plan") REFERENCES "apex_plan_operacion" ("proyecto","plan") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
	--CONSTRAINT  "apex_plan_operacion_abms_fila" FOREIGN KEY ("elemento_formulario") REFERENCES "apex_elemento_formulario" ("elemento_formulario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
