--**************************************************************************************************
--**************************************************************************************************
--************************************   PERSISTENCIA    *******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE SEQUENCE apex_admin_persistencia_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_admin_persistencia
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: ap
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	ap								int4				DEFAULT nextval('"apex_admin_persistencia_seq"'::text) 		NOT NULL, 
	clase							varchar(60)			NOT	NULL,
	archivo							varchar(120)			NOT	NULL,
	descripcion						varchar(60)			NOT	NULL,
	categoria						varchar(20)			NULL,		-- Indica si es un AP de tablas o relaciones
	CONSTRAINT	"apex_admin_persistencia_pk" PRIMARY	KEY ("ap")
);
--###################################################################################################

CREATE TABLE apex_tipo_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: tipo
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tipo							varchar(1)			NOT NULL,
	descripcion						varchar(50)			NOT	NULL,
	CONSTRAINT	"apex_tipo_datos_pk" PRIMARY	KEY ("tipo")
);
--###################################################################################################
--**************************************************************************************************
--*******************************    objeto_datos_tabla    *****************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_db_registros
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto  				varchar(15)		NOT NULL,
	objeto      	    	 		int4			NOT NULL,
	max_registros					smallint		NULL,
	min_registros					smallint		NULL,
--	Configuracion del AP por defecto
	ap								int4			NULL,
	ap_clase						varchar(60)		NULL,
	ap_archivo						varchar(120)	NULL,
	tabla 							varchar(120)	NULL,
	alias 							varchar(60)		NULL,
	modificar_claves				smallint		NULL,
--	Fin configuracion del AP
	CONSTRAINT  "apex_objeto_dbr_pk" PRIMARY KEY ("objeto_proyecto","objeto"),
	CONSTRAINT  "apex_objeto_dbr_fk_ap"  FOREIGN KEY ("ap") REFERENCES   "apex_admin_persistencia" ("ap") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_dbr_fk_objeto"  FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_objeto_dbr_columna_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_db_registros_col
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, col_id
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                	int4       		NOT NULL,
	col_id							int4			DEFAULT nextval('"apex_objeto_dbr_columna_seq"'::text) 		NOT NULL, 
	columna		    				varchar(120)		NOT NULL, 
	tipo							varchar(1)		NULL,
	pk								smallint 		NULL,
	secuencia		    			varchar(120)		NULL, 
	largo							smallint		NULL,
	no_nulo							smallint 		NULL,
	no_nulo_db						smallint 		NULL,
	externa							smallint		NULL,
	CONSTRAINT  "apex_obj_dbr_col_pk" PRIMARY KEY ("objeto_proyecto","objeto","col_id"),
	CONSTRAINT  "apex_obj_dbr_col_fk_tipo" FOREIGN KEY ("tipo") REFERENCES "apex_tipo_datos" ("tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_dbr_col_fk_objeto_dbr" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES "apex_objeto_db_registros" ("objeto_proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--###################################################################################################

CREATE SEQUENCE apex_objeto_dbr_ext_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_db_registros_ext
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, externa_id
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                	int4       		NOT NULL,
	externa_id						int4			DEFAULT nextval('"apex_objeto_dbr_ext_seq"'::text) 		NOT NULL, 
	tipo							varchar(3)		NOT NULL,
	sincro_continua					smallint		NULL,
--- CARGA PHP
	metodo							varchar(100)	NULL,
	clase							varchar(100)	NULL,
	include							varchar(255)	NULL,
--- CARGA SQL
	sql								varchar			NULL,
	CONSTRAINT  "apex_obj_dbr_ext_pk" PRIMARY KEY ("objeto_proyecto","objeto","externa_id"),
	CONSTRAINT  "apex_obj_dbr_ext_fk_objeto_dbr" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES "apex_objeto_db_registros" ("objeto_proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE	
);

--###################################################################################################

CREATE TABLE apex_objeto_db_registros_ext_col
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, externa_id, col_id
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc: Asocia una carga externa con una columna, ya sea como resultado o como parametro
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                	int4       		NOT NULL,
	externa_id						int4			NOT NULL,
	col_id							int4			NOT NULL,
	es_resultado					smallint		NULL,
	CONSTRAINT  "apex_obj_dbr_ext_col_pk" PRIMARY KEY ("objeto_proyecto","objeto","externa_id","col_id"),
	CONSTRAINT  "apex_obj_dbr_ext_col_fk_ext" FOREIGN KEY ("objeto_proyecto","objeto", "externa_id") 
		REFERENCES "apex_objeto_db_registros_ext" ("objeto_proyecto","objeto","externa_id") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_dbr_ext_col_fk_col" FOREIGN KEY ("objeto_proyecto","objeto", "col_id") 
		REFERENCES "apex_objeto_db_registros_col" ("objeto_proyecto","objeto","col_id") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
		
);

--###################################################################################################

CREATE SEQUENCE apex_objeto_dbr_uniq_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_db_registros_uniq
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, uniq_id
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                	int4       		NOT NULL,
	uniq_id							int4			DEFAULT nextval('"apex_objeto_dbr_uniq_seq"'::text) 		NOT NULL, 
	columnas						varchar(255)	NULL,
	CONSTRAINT  "apex_obj_dbr_uniq_pk" PRIMARY KEY ("objeto_proyecto","objeto","uniq_id"),
	CONSTRAINT  "apex_obj_dbr_uniq_fk_objeto_dbr" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES "apex_objeto_db_registros" ("objeto_proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE	
);

--###################################################################################################
--**************************************************************************************************
--*************************************    objeto_datos_relacion    ********************************
--**************************************************************************************************

CREATE TABLE apex_objeto_datos_rel
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto 		 				varchar(15)		NOT NULL,
	objeto      	    	 		int4			NOT NULL,
	debug							smallint		NULL DEFAULT(0),	
	clave							varchar(60)		NULL,
--	Configuracion del AP por defecto
	ap								int4			NULL,
	ap_clase						varchar(60)		NULL,
	ap_archivo						varchar(120)	NULL,
--	Opciones de sincronizaci�n
	sinc_susp_constraints			smallint		NULL DEFAULT(0),
	sinc_orden_automatico			smallint		NULL DEFAULT(1),
	CONSTRAINT  "apex_objeto_datos_rel_pk" PRIMARY KEY ("proyecto","objeto"),
	CONSTRAINT  "apex_objeto_datos_rel_fk_ap"  FOREIGN KEY ("ap") REFERENCES   "apex_admin_persistencia" ("ap") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_datos_rel_fk_objeto"  FOREIGN KEY ("proyecto","objeto") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_objeto_datos_rel_asoc_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_datos_rel_asoc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, asoc_id
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto 		   			   	varchar(15)			NOT NULL,
	objeto 		                	int4       			NOT NULL,
	asoc_id							int4				DEFAULT nextval('"apex_objeto_datos_rel_asoc_seq"'::text) 		NOT NULL, 
	identificador    				varchar(60)			NULL, 
--	padre --
	padre_proyecto					varchar(15)			NOT NULL,
	padre_objeto					int4				NOT NULL,
	padre_id						varchar(20)			NOT NULL,
	padre_clave		    			varchar(255)			NULL, 
--	hijo --
	hijo_proyecto					varchar(15)			NOT NULL,
	hijo_objeto						int4				NOT NULL,
	hijo_id							varchar(20)			NOT NULL,
	hijo_clave		    			varchar(255)			NULL, 
	cascada							smallint			NULL,
	orden							float				NULL,
	CONSTRAINT  "apex_obj_datos_rel_asoc_pk" PRIMARY KEY ("proyecto","objeto","asoc_id"),
	CONSTRAINT  "apex_obj_datos_rel_asoc_fk_objeto" FOREIGN KEY ("proyecto","objeto") REFERENCES "apex_objeto_datos_rel" ("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_datos_rel_asoc_fk_padre" FOREIGN KEY ("proyecto","objeto","padre_id") REFERENCES "apex_objeto_dependencias" ("proyecto","objeto_consumidor","identificador") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_datos_rel_asoc_fk_hijo" FOREIGN KEY ("proyecto","objeto","hijo_id") REFERENCES "apex_objeto_dependencias" ("proyecto","objeto_consumidor","identificador") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################