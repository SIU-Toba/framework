--**************************************************************************************************
--**************************************************************************************************
--*************************************    db_registros    *****************************************
--**************************************************************************************************
--**************************************************************************************************
--	
--	Falta: FKs, columnas no duplicables, baja logica, y mas
--

CREATE TABLE apex_objeto_db_registros
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto  						varchar(15)		NOT NULL,
	objeto      	    	 		int4			NOT NULL,
	tabla 							varchar(60)		NOT NULL,
	CONSTRAINT  "apex_objeto_dbr_pk" PRIMARY KEY ("proyecto","objeto"),
	CONSTRAINT  "apex_objeto_dbr_fk_objeto"  FOREIGN KEY ("proyecto","objeto") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_tipo_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: tipo
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tipo							varchar(1)			NOT NULL,
	descripcion						varchar(30)			NOT	NULL,
	CONSTRAINT	"apex_tipo_datos_pk" PRIMARY	KEY ("tipo")
);
--###################################################################################################

CREATE SEQUENCE apex_objeto_dbr_columna_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_db_registros_col
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto, col_id
--: dump_where: ( proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto    			    	varchar(15)		NOT NULL,
	objeto 		                	int4       		NOT NULL,
	col_id							int4			DEFAULT nextval('"apex_objeto_dbr_columna_seq"'::text) 		NOT NULL, 
	columna		    				varchar(40)		NOT NULL, 
	tipo							varchar(1)		NULL,
	pk								smallint 		NULL,
	secuencia		    			varchar(60)		NULL, 
	largo							smallint		NULL,
	no_nulo							smallint 		NULL,
	no_nulo_db						smallint 		NULL,
	CONSTRAINT  "apex_obj_dbr_col_pk" PRIMARY KEY ("proyecto","objeto","col_id"),
	CONSTRAINT  "apex_obj_dbr_col_fk_tipo" FOREIGN KEY ("tipo") REFERENCES "apex_tipo_datos" ("tipo") ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_dbr_col_fk_objeto_dbr" FOREIGN KEY ("proyecto","objeto") REFERENCES "apex_objeto_db_registros" ("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
