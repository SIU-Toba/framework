--**************************************************************************************************
--**************************************************************************************************
--***************************   DOCUMENTACION del MODELO de DATOS   ********************************
--**************************************************************************************************
--**************************************************************************************************

-- Estas son las tablas que mantienen la documentacion sobre el modelo de datos.
-- Se utilizan para generar planes de dumpeo y eliminacion.
-- Los registros que poseen se generan dinamicamente parseando scripts SQL (Este mismo por ejemplo...)

CREATE TABLE apex_mod_datos_zona
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: modelo_datos
--: desc: Organizadores conceptuales de tablas
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	char(15)    NOT NULL,
	zona 						        	char(15)    NOT NULL,
	descripcion  			       	char(255) ,
   PRIMARY KEY (proyecto,zona),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_dump
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: modelo_datos
--: desc: Modalidades de dumpeo
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	dump							     	char(20)    NOT NULL,
	descripcion                 	char(255) ,    
   PRIMARY KEY (dump)
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: modelo_datos
--: desc: Tablas que componen el modelo de datos
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	char(15)    NOT NULL,
	tabla									char(30)    NOT NULL,
	script								char(80)  ,
	orden									smallint			NOT NULL,
	descripcion							char(255) ,
	version								char(15)  ,
	historica							smallint		,
	instancia							smallint		,
	dump									char(20)  ,
	dump_where							char(255) ,
	dump_from							char(255) ,
	dump_order_by						char(255) ,
	dump_order_by_from				char(255) ,
	dump_order_by_where				char(255) ,
	extra_1								char(255) ,
	extra_2								char(255) ,
   PRIMARY KEY (proyecto,tabla),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (dump) REFERENCES apex_mod_datos_dump (dump)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla_columna
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Columnas de la tabla
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   tabla_proyecto               	char(15)    NOT NULL,
	tabla									char(30)    NOT NULL,
	columna								char(30)    NOT NULL,
	orden									float			,
	dump									smallint			DEFAULT 1 ,
	definicion							char		 ,
   PRIMARY KEY (tabla_proyecto,tabla,columna),
   FOREIGN KEY (tabla_proyecto,tabla) REFERENCES apex_mod_datos_tabla (proyecto,tabla)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla_restric
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Constraints de la tabla
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   tabla_proyecto               	char(15)    NOT NULL,
	tabla									char(30)    NOT NULL,
	restriccion							char(30)  ,
	definicion							char		 ,
   PRIMARY KEY (tabla_proyecto,tabla,restriccion),
   FOREIGN KEY (tabla_proyecto,tabla) REFERENCES apex_mod_datos_tabla (proyecto,tabla)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_secuencia
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: modelo_datos
--: desc: Secuencias
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	char(15)    NOT NULL,
	secuencia							char(30)    NOT NULL,
	definicion							char(255)  ,
   PRIMARY KEY (proyecto,secuencia),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_zona_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Asociacion de tablas con zonas
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   zona_proyecto             		char(15)    NOT NULL,
	zona             					char(15)    NOT NULL,
   tabla_proyecto            		char(15)    NOT NULL,
	tabla            					char(30)    NOT NULL,
   PRIMARY KEY (zona_proyecto,zona,tabla_proyecto,tabla),
   FOREIGN KEY (zona_proyecto,zona) REFERENCES apex_mod_datos_zona (proyecto,zona)   ,
   FOREIGN KEY (tabla_proyecto,tabla) REFERENCES apex_mod_datos_tabla (proyecto,tabla)   
);
--#################################################################################################
