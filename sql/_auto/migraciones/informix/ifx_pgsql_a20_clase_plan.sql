--**************************************************************************************************
--**************************************************************************************************
--******************************************     plan    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_plan
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_plan_proyecto  		char(15)			NOT NULL,
	objeto_plan           		integer					NOT NULL,
	descripcion					char(255)			NOT NULL,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES   apex_objeto (proyecto,objeto)   
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_activ
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	posicion								smallint			NOT NULL,
	descripcion_corta					char(50)		NOT NULL,
	descripcion 						char		,
	fecha_inicio						date				NOT NULL,
	fecha_fin							date			,
	duracion								smallint		,
	anotacion							char(50)	,		
	altura								float			,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,posicion),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES apex_objeto_plan (objeto_plan_proyecto,objeto_plan)   
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_activ_usu
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	posicion							smallint				NOT NULL,
	usuario								char(20)		NOT NULL,
	observaciones						char		,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,posicion,usuario),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan,posicion) REFERENCES apex_objeto_plan_activ (objeto_plan_proyecto,objeto_plan,posicion)   ,
   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_hito
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	posicion								smallint			NOT NULL,
	descripcion_corta					char(50)		NOT NULL,
	descripcion 						char		,
	fecha									date				NOT NULL,
	anotacion							char(50)	,		
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,posicion),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES apex_objeto_plan (objeto_plan_proyecto,objeto_plan)   
);
--#################################################################################################


CREATE TABLE apex_objeto_plan_linea
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	linea	 								serial,
	descripcion_corta					char(50)		NOT NULL,
	descripcion 						char		,
	fecha									date				NOT NULL,
	color									char(20)	,
	ancho									smallint		,
	estilo								char(20)	,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,linea),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES apex_objeto_plan (objeto_plan_proyecto,objeto_plan)   
);
--#################################################################################################
