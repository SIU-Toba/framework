--**************************************************************************************************
--**************************************************************************************************
--***************************   DOCUMENTACION del MODELO de DATOS   ********************************
--**************************************************************************************************
--**************************************************************************************************

--------------------------------------------------------
--%%: zona: mod_datos
--%%: descripcion: Descripcion del modelo de datos
--%%: proyecto: toba
--------------------------------------------------------

-- Estas son las tablas que mantienen la documentacion sobre el modelo de datos.
-- Se utilizan para generar planes de dumpeo y eliminacion.
-- Los registros que poseen se generan dinamicamente parseando scripts SQL (Este mismo por ejemplo...)

CREATE TABLE apex_mod_datos_zona
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: zona
--: zona: modelo_datos
--: desc: Organizadores conceptuales de tablas
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto         		     	varchar(15)    NOT NULL,
	zona 					    	varchar(15)    NOT NULL,
	descripcion  			       	varchar(255)   NULL,
   CONSTRAINT  "apex_md_zona_pk"   PRIMARY KEY ("proyecto","zona"),
   CONSTRAINT  "apex_md_zona_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_mod_datos_dump
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: dump
--: zona: modelo_datos
--: desc: Modalidades de dumpeo
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	dump							     	varchar(20)    NOT NULL,
	descripcion                 	varchar(255)   NULL,    
   CONSTRAINT  "apex_md_dump_pk"   PRIMARY KEY ("dump")
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: tabla
--: zona: modelo_datos
--: desc: Tablas que componen el modelo de datos
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	varchar(15)    NOT NULL,
	tabla									varchar(30)    NOT NULL,
	script								varchar(80)    NULL,
	orden									smallint			NOT NULL,
	descripcion							varchar(255)   NULL,
	version								varchar(15)    NULL,
	historica							smallint			NULL,
	instancia							smallint			NULL,
	dump									varchar(20)    NULL,
	dump_where							varchar(255)   NULL,
	dump_from							varchar(255)   NULL,
	dump_order_by						varchar(255)   NOT NULL,
	dump_order_by_from				varchar(255)   NULL,
	dump_order_by_where				varchar(255)   NULL,
	extra_1								varchar(255)   NULL,
	extra_2								varchar(255)   NULL,
   CONSTRAINT  "apex_md_tabla_pk"   PRIMARY KEY ("proyecto","tabla"),
   CONSTRAINT  "apex_md_tabla_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_md_tabla_fk_dump" FOREIGN KEY ("dump") REFERENCES "apex_mod_datos_dump" ("dump") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla_columna
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: tabla, columna
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Columnas de la tabla
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   tabla_proyecto               	varchar(15)    NOT NULL,
	tabla									varchar(30)    NOT NULL,
	columna								varchar(30)    NOT NULL,
	orden									float				NULL,
	dump									smallint			DEFAULT 1   NULL,
	definicion							varchar		   NULL,
   CONSTRAINT  "apex_md_tabla_col_pk"   PRIMARY KEY ("tabla_proyecto","tabla","columna"),
   CONSTRAINT  "apex_md_tabla_col_fk_tab" FOREIGN KEY ("tabla_proyecto","tabla") REFERENCES "apex_mod_datos_tabla" ("proyecto","tabla") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla_restric
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: tabla, restriccion
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Constraints de la tabla
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   tabla_proyecto               	varchar(15)    NOT NULL,
	tabla									varchar(30)    NOT NULL,
	restriccion							varchar(30)    NULL,
	definicion							varchar		   NULL,
   CONSTRAINT  "apex_md_tabla_cons_pk"   PRIMARY KEY ("tabla_proyecto","tabla","restriccion"),
   CONSTRAINT  "apex_md_tabla_cons_fk_tab" FOREIGN KEY ("tabla_proyecto","tabla") REFERENCES "apex_mod_datos_tabla" ("proyecto","tabla") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_mod_datos_secuencia
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: secuencia
--: zona: modelo_datos
--: desc: Secuencias
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	varchar(15)    NOT NULL,
	secuencia							varchar(30)    NOT NULL,
	definicion							varchar(255)    NULL,
   CONSTRAINT  "apex_md_secu_pk"   PRIMARY KEY ("proyecto","secuencia"),
   CONSTRAINT  "apex_md_secu_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_mod_datos_zona_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: zona, tabla
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Asociacion de tablas con zonas
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	zona_proyecto             		varchar(15)    NOT NULL,
	zona             					varchar(15)    NOT NULL,
   	tabla_proyecto            		varchar(15)    NOT NULL,
	tabla            					varchar(30)    NOT NULL,
   CONSTRAINT  "apex_md_zona_tabla_pk"   PRIMARY KEY ("zona_proyecto","zona","tabla_proyecto","tabla"),
   CONSTRAINT  "apex_md_zona_tabla_fk_zon" FOREIGN KEY ("zona_proyecto","zona") REFERENCES "apex_mod_datos_zona" ("proyecto","zona") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_md_zona_tabla_fk_tab" FOREIGN KEY ("tabla_proyecto","tabla") REFERENCES "apex_mod_datos_tabla" ("proyecto","tabla") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################
