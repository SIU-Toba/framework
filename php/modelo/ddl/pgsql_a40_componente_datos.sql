--**************************************************************************************************
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
	ap								int8				DEFAULT nextval('"apex_admin_persistencia_seq"'::text) 		NOT NULL, 
	clase							varchar(60)			NOT	NULL,
	archivo							TEXT			NOT	NULL,
	descripcion						TEXT			NOT	NULL,
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
	objeto      	    	 		int8			NOT NULL,
	max_registros					smallint		NULL,
	min_registros					smallint		NULL,
--	Configuracion del AP por defecto
	punto_montaje					int8		NULL,
	ap								int8			NULL,
	ap_clase						varchar(60)		NULL,
	ap_archivo						TEXT	NULL,
	tabla 							TEXT	NULL,
	tabla_ext						TEXT	NULL,
	alias 							varchar(60)		NULL,
	modificar_claves				smallint		NULL,
	fuente_datos_proyecto			varchar(15)		NULL,	
	fuente_datos					varchar(20)		NULL,
	permite_actualizacion_automatica	SMALLINT NOT NULL DEFAULT 1,
	esquema						TEXT	NULL,
	 esquema_ext					TEXT	NULL,
--	Fin configuracion del AP
	CONSTRAINT  "apex_objeto_dbr_pk" PRIMARY KEY ("objeto", "objeto_proyecto"),
	CONSTRAINT	"apex_objeto_dbr_uq_tabla" UNIQUE ("fuente_datos_proyecto", "fuente_datos", "tabla"),
	CONSTRAINT  "apex_objeto_dbr_fk_ap"  FOREIGN KEY ("ap") REFERENCES   "apex_admin_persistencia" ("ap") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_dbr_fk_objeto"  FOREIGN KEY ("objeto", "objeto_proyecto") REFERENCES   "apex_objeto" ("objeto", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_dbr_fk_fuente"  FOREIGN KEY ("fuente_datos_proyecto","fuente_datos") REFERENCES   "apex_fuente_datos" ("proyecto","fuente_datos") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_puntos_montaje" FOREIGN KEY ("objeto_proyecto", "punto_montaje")	REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_fuente_schemas" FOREIGN KEY ("objeto_proyecto", "fuente_datos", "esquema") REFERENCES "apex_fuente_datos_schemas" ("proyecto", "fuente_datos", "nombre") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
-- agregar la tabla
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                	int8       		NOT NULL,
	col_id							int8			DEFAULT nextval('"apex_objeto_dbr_columna_seq"'::text) 		NOT NULL, 
	columna		    				TEXT			NOT NULL,
	tipo							varchar(1)		NULL,
	pk								smallint 		NULL,
	secuencia		    			TEXT			NULL,
	largo							smallint		NULL,
	no_nulo							smallint 		NULL,
	no_nulo_db						smallint 		NULL,
	externa							smallint		NULL,
	tabla							varchar(200)	NULL,
	CONSTRAINT  "apex_obj_dbr_col_pk" PRIMARY KEY ("col_id", "objeto", "objeto_proyecto"),
	--CONSTRAINT	"apex_obj_dbr_uq_col" UNIQUE ("objeto_proyecto", "objeto", "columna"),
	CONSTRAINT	"apex_obj_dbr_uq_col" UNIQUE ("objeto_proyecto", "objeto", "columna"),
	CONSTRAINT  "apex_obj_dbr_col_fk_tipo" FOREIGN KEY ("tipo") REFERENCES "apex_tipo_datos" ("tipo") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_dbr_col_fk_objeto_dbr" FOREIGN KEY ("objeto", "objeto_proyecto") REFERENCES "apex_objeto_db_registros" ("objeto", "objeto_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_objeto_db_columna_fks_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_db_columna_fks
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, id
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
-- agregar la tabla
	id								int8			DEFAULT nextval('"apex_objeto_db_columna_fks_seq"'::text) 		NOT NULL,
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                	int8       		NOT NULL,
	tabla							varchar(200)	NOT NULL,
	columna							varchar(200)	NOT NULL,
	tabla_ext						varchar(200)	NOT NULL,
	columna_ext						varchar(200)	NOT NULL,
	CONSTRAINT  "apex_obj_db_col_fks_pk" PRIMARY KEY ("id", "objeto", "objeto_proyecto"),
	CONSTRAINT  "apex_obj_db_col_fks_reg" FOREIGN KEY ("objeto_proyecto", "objeto") REFERENCES "apex_objeto_db_registros" ("objeto_proyecto", "objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
	objeto 		                	int8       		NOT NULL,
	externa_id						int8			DEFAULT nextval('"apex_objeto_dbr_ext_seq"'::text) 		NOT NULL, 
	tipo							varchar(3)		NOT NULL,
	sincro_continua					smallint		NULL,
--- CARGA PHP
	metodo							TEXT	NULL,
	clase							TEXT	NULL,
	include							TEXT	NULL,
	punto_montaje			int8		NULL,
--- CARGA SQL
	sql								TEXT		NULL,
	dato_estricto			SMALLINT  DEFAULT	1  NULL,
--- CARGA DAO
	carga_dt							BIGINT NULL,
	carga_consulta_php		BIGINT NULL,
	permite_carga_masiva		SMALLINT NOT NULL DEFAULT 0,
	metodo_masivo			TEXT NULL,
	CONSTRAINT  "apex_obj_dbr_ext_pk" PRIMARY KEY ("externa_id", "objeto", "objeto_proyecto"),
	CONSTRAINT  "apex_obj_dbr_ext_fk_objeto_dbr" FOREIGN KEY ("objeto", "objeto_proyecto") REFERENCES "apex_objeto_db_registros" ("objeto", "objeto_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_dbr_ext_fk_datos_tabla" FOREIGN KEY ("objeto_proyecto","carga_dt") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_dbr_ext_fk_consulta_php" FOREIGN KEY ("objeto_proyecto","carga_consulta_php") REFERENCES "apex_consulta_php" ("proyecto", "consulta_php") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_obj_dbr_ext_fk_punto_montaje" FOREIGN KEY ("objeto_proyecto", "punto_montaje") REFERENCES "apex_puntos_montaje" ("proyecto", "id")  ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--###################################################################################################

CREATE TABLE apex_objeto_db_registros_ext_col
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: clave_elemento: objeto, externa_id, col_id, objeto_proyecto
--: dump_order_by: objeto, externa_id, col_id
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: objeto
--: desc: Asocia una carga externa con una columna, ya sea como resultado o como parametro
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto    			   	varchar(15)		NOT NULL,
	objeto 		                	int8       		NOT NULL,
	externa_id						int8			NOT NULL,
	col_id							int8			NOT NULL,
	es_resultado					smallint		NULL,
	CONSTRAINT  "apex_obj_dbr_ext_col_pk" PRIMARY KEY ("externa_id","col_id","objeto","objeto_proyecto"),
	CONSTRAINT  "apex_obj_dbr_ext_col_fk_ext" FOREIGN KEY ("externa_id", "objeto", "objeto_proyecto") 
		REFERENCES "apex_objeto_db_registros_ext" ("externa_id", "objeto", "objeto_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_dbr_ext_col_fk_col" FOREIGN KEY ("col_id", "objeto", "objeto_proyecto" ) 
		REFERENCES "apex_objeto_db_registros_col" ("col_id","objeto","objeto_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
	objeto 		                	int8       		NOT NULL,
	uniq_id							int8			DEFAULT nextval('"apex_objeto_dbr_uniq_seq"'::text) 		NOT NULL, 
	columnas						TEXT	NULL,
	CONSTRAINT  "apex_obj_dbr_uniq_pk" PRIMARY KEY ("uniq_id", "objeto", "objeto_proyecto"),
	CONSTRAINT  "apex_obj_dbr_uniq_fk_objeto_dbr" FOREIGN KEY ("objeto", "objeto_proyecto") REFERENCES "apex_objeto_db_registros" ("objeto", "objeto_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE	
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
	objeto      	    	 		int8			NOT NULL,
	debug							smallint		NULL DEFAULT 0,	
	clave							varchar(60)		NULL,
--	Configuracion del AP por defecto
	ap								int8			NULL,
	punto_montaje					int8			NULL,
	ap_clase						varchar(60)		NULL,
	ap_archivo						TEXT	NULL,
--	Opciones de sincronizaci�n
	sinc_susp_constraints			smallint		NULL DEFAULT 0,
	sinc_orden_automatico			smallint		NULL DEFAULT 1,
	sinc_lock_optimista				smallint		NULL DEFAULT 1,
	CONSTRAINT  "apex_objeto_datos_rel_pk" PRIMARY KEY ("objeto", "proyecto"),
	CONSTRAINT  "apex_objeto_datos_rel_fk_ap"  FOREIGN KEY ("ap") REFERENCES   "apex_admin_persistencia" ("ap") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_datos_rel_fk_objeto"  FOREIGN KEY ("objeto", "proyecto") REFERENCES   "apex_objeto" ("objeto", "proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_puntos_montaje" FOREIGN KEY ("proyecto", "punto_montaje")	REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
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
	objeto 		                	int8       			NOT NULL,
	asoc_id							int8				DEFAULT nextval('"apex_objeto_datos_rel_asoc_seq"'::text) 		NOT NULL, 
	identificador    				varchar(60)			NULL, 
--	padre --
	padre_proyecto					varchar(15)			NOT NULL,
	padre_objeto					int8				NOT NULL,
	padre_id						varchar(40)			NOT NULL,
	padre_clave		    			varchar(255)			NULL,   --OBSOLETO
--	hijo --
	hijo_proyecto					varchar(15)			NOT NULL,
	hijo_objeto						int8				NOT NULL,
	hijo_id							varchar(40)			NOT NULL,
	hijo_clave		    			varchar(255)			NULL,	--OBSOLETO
	cascada							smallint			NULL,
	orden							float				NULL,
	CONSTRAINT  "apex_obj_datos_rel_asoc_pk" PRIMARY KEY ("asoc_id","objeto","proyecto"),
	CONSTRAINT  "apex_obj_datos_rel_asoc_fk_objeto" FOREIGN KEY ("objeto","proyecto") REFERENCES "apex_objeto_datos_rel" ("objeto","proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_datos_rel_asoc_fk_padre" FOREIGN KEY ("proyecto","objeto","padre_id") REFERENCES "apex_objeto_dependencias" ("proyecto","objeto_consumidor","identificador") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_datos_rel_asoc_fk_hijo" FOREIGN KEY ("proyecto","objeto","hijo_id") REFERENCES "apex_objeto_dependencias" ("proyecto","objeto_consumidor","identificador") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
CREATE TABLE apex_objeto_rel_columnas_asoc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto
--: clave_elemento: asoc_id, objeto, proyecto, padre_objeto, hijo_objeto, padre_clave, hijo_clave
--: dump_order_by: objeto, asoc_id
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto 		   			   	varchar(15)			NOT NULL,
	objeto							  int8						 NOT NULL,
	asoc_id							int8					   NOT NULL,
	padre_objeto				int8					 NOT NULL,
	padre_clave					int8						NOT NULL,
	hijo_objeto						int8					 NOT NULL,
	hijo_clave						int8						NOT NULL,
	CONSTRAINT "apex_objeto_rel_columnas_asoc_pk" PRIMARY KEY ("asoc_id", "objeto", "proyecto", "padre_objeto", "hijo_objeto", "padre_clave", "hijo_clave"),
	CONSTRAINT "apex_columna_objeto_hijo_fk" FOREIGN KEY ("hijo_clave", "hijo_objeto", "proyecto") REFERENCES "apex_objeto_db_registros_col" ("col_id", "objeto", "objeto_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_columna_objeto_padre_fk" FOREIGN KEY ("padre_objeto", "padre_clave", "proyecto") REFERENCES "apex_objeto_db_registros_col" ("objeto", "col_id", "objeto_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_obj_datos_rel_asoc_fk"  FOREIGN KEY ("asoc_id", "objeto", "proyecto") REFERENCES "apex_objeto_datos_rel_asoc" ("asoc_id", "objeto", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
