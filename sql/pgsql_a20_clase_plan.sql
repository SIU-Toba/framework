--**************************************************************************************************
--**************************************************************************************************
--******************************************     plan    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_plan
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_plan
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_plan_proyecto  		varchar(15)					NOT NULL,
	objeto_plan           		int4						NOT NULL,
	descripcion						varchar(255)			NOT NULL,
	CONSTRAINT  "apex_objeto_plan_pk" PRIMARY KEY ("objeto_plan_proyecto","objeto_plan"),
	CONSTRAINT  "apex_objeto_plan_fk_objeto"  FOREIGN KEY ("objeto_plan_proyecto","objeto_plan") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_activ
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_plan, posicion
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				varchar(15)			NOT NULL,
	objeto_plan     					int4				NOT NULL,
	posicion							smallint			NOT NULL,
	descripcion_corta					varchar(50)			NOT NULL,
	descripcion 						varchar				NULL,
	fecha_inicio						date				NOT NULL,
	fecha_fin							date				NULL,
	duracion							smallint			NULL,
	anotacion							varchar(50)			NULL,		
	altura								float				NULL,
	CONSTRAINT  "apex_obj_plan_activ_pk" PRIMARY KEY ("objeto_plan_proyecto","objeto_plan","posicion"),
	CONSTRAINT  "apex_obj_plan_activ_fk_op" FOREIGN KEY ("objeto_plan_proyecto","objeto_plan") REFERENCES "apex_objeto_plan" ("objeto_plan_proyecto","objeto_plan") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_activ_usu
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_plan, posicion
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				varchar(15)			NOT NULL,
	objeto_plan     					int4				NOT NULL,
	posicion							smallint			NOT NULL,
	usuario								varchar(20)			NOT NULL,
	observaciones						varchar				NULL,
	CONSTRAINT  "apex_obj_plan_activ_usu_pk" PRIMARY KEY ("objeto_plan_proyecto","objeto_plan","posicion","usuario"),
	CONSTRAINT  "apex_obj_plan_activ_usu_fk_o" FOREIGN KEY ("objeto_plan_proyecto","objeto_plan","posicion") REFERENCES "apex_objeto_plan_activ" ("objeto_plan_proyecto","objeto_plan","posicion") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_plan_activ_usu_fk_u" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_hito
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_plan, posicion
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				varchar(15)			NOT NULL,
	objeto_plan     					int4				NOT NULL,
	posicion							smallint			NOT NULL,
	descripcion_corta					varchar(50)			NOT NULL,
	descripcion 						varchar				NULL,
	fecha								date				NOT NULL,
	anotacion							varchar(50)			NULL,		
	CONSTRAINT  "apex_obj_plan_hito_pk" PRIMARY KEY ("objeto_plan_proyecto","objeto_plan","posicion"),
	CONSTRAINT  "apex_obj_plan_hito_fk_op" FOREIGN KEY ("objeto_plan_proyecto","objeto_plan") REFERENCES "apex_objeto_plan" ("objeto_plan_proyecto","objeto_plan") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_plan_linea_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_objeto_plan_linea
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_plan, linea
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				varchar(15)			NOT NULL,
	objeto_plan     					int4				NOT NULL,
	linea	 							int4				DEFAULT nextval('"apex_objeto_plan_linea_seq"'::text) NOT NULL, 
	descripcion_corta					varchar(50)			NOT NULL,
	descripcion 						varchar				NULL,
	fecha								date				NOT NULL,
	color								varchar(20)			NULL,
	ancho								smallint			NULL,
	estilo								varchar(20)			NULL,
	CONSTRAINT  "apex_obj_plan_linea_pk" PRIMARY KEY ("objeto_plan_proyecto","objeto_plan","linea"),
	CONSTRAINT  "apex_obj_plan_linea_fk_op" FOREIGN KEY ("objeto_plan_proyecto","objeto_plan") REFERENCES "apex_objeto_plan" ("objeto_plan_proyecto","objeto_plan") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################
