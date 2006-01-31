--**************************************************************************************************
--**************************************************************************************************
--***************************   Administracion de PROYECTOS   **************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE 			apex_ap_version
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: version
--: zona: admin_proyectos
--: desc: Tabla de manejo de versiones
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto             		   varchar(15)    NOT NULL,
   version								varchar(15)    NOT NULL,
   descripcion          		   varchar(255)   NOT NULL,
   fecha									date				NOT NULL,
	observaciones						varchar			NULL,
	actual								smallint			NULL,
	cerrada								smallint			NULL,
   CONSTRAINT  "apex_version_pk" PRIMARY KEY ("proyecto","version"),
   CONSTRAINT  "apex_ap_tarea_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_ap_tarea_tipo_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_ap_tarea_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: tarea_tipo
--: zona: admin_proyectos
--: desc: Tipos de tarea
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tarea_tipo  		            int4				DEFAULT nextval('"apex_ap_tarea_tipo_seq"'::text) NOT NULL, 
	descripcion  			       	varchar(70)   	NOT NULL,
   CONSTRAINT  "apex_ap_tarea_tipo_pk"   PRIMARY KEY ("tarea_tipo")
);
--#################################################################################################

CREATE SEQUENCE apex_ap_tarea_estado_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_ap_tarea_estado
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: tarea_estado
--: zona: admin_proyectos
--: desc: Estados de Tarea
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tarea_estado  		            int4           DEFAULT nextval('"apex_ap_tarea_estado_seq"'::text) NOT NULL, 
	descripcion  			       	varchar(70)   NOT NULL,
   CONSTRAINT  "apex_ap_tarea_estado_pk"   PRIMARY KEY ("tarea_estado")
);
--#################################################################################################

CREATE TABLE apex_ap_tarea_prioridad
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: tarea_prioridad
--: zona: admin_proyectos
--: desc: Prioridad de Tarea
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tarea_prioridad  		         smallint			NOT NULL, 
	descripcion  			       	varchar(70)		NOT NULL,
   CONSTRAINT  "apex_ap_tarea_prioridad_pk"   PRIMARY KEY ("tarea_prioridad")
);
--#################################################################################################

CREATE SEQUENCE apex_ap_tarea_tema_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_ap_tarea_tema
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: tarea_tema
--: zona: admin_proyectos
--: desc: Tipos de tarea
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tarea_tema  		            int4				DEFAULT nextval('"apex_ap_tarea_tema_seq"'::text) NOT NULL, 
	descripcion  			       	varchar(70)   	NOT NULL,
   CONSTRAINT  "apex_ap_tarea_tema_pk"   PRIMARY KEY ("tarea_tema")
);
--#################################################################################################

CREATE SEQUENCE apex_ap_tarea_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_ap_tarea
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: tarea
--: zona: admin_proyectos
--: desc: Estados de Tarea
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto 		               varchar(15)   			NOT NULL,
	tarea					            int4           		DEFAULT nextval('"apex_ap_tarea_seq"'::text) NOT NULL, 
	tarea_tipo							int4				NOT NULL,
	tarea_estado						int4				NOT NULL,
	tarea_prioridad						int4				NOT NULL,
	tarea_tema							int4				NULL,
	descripcion  			       		varchar(400)  		NOT NULL,
	version_proyecto					varchar(15)			NULL,
	version								varchar(15)    		NULL,
	grado_avance						smallint			NULL,
   CONSTRAINT  "apex_ap_tarea_pk"   PRIMARY KEY ("tarea"),
   CONSTRAINT  "apex_ap_tarea_fk_tipo" FOREIGN KEY ("tarea_tipo") REFERENCES "apex_ap_tarea_tipo" ("tarea_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_ap_tarea_fk_estado" FOREIGN KEY ("tarea_estado") REFERENCES "apex_ap_tarea_estado" ("tarea_estado") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_ap_tarea_fk_tema" FOREIGN KEY ("tarea_tema") REFERENCES "apex_ap_tarea_tema" ("tarea_tema") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_ap_tarea_fk_priori" FOREIGN KEY ("tarea_prioridad") REFERENCES "apex_ap_tarea_prioridad" ("tarea_prioridad") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_ap_tarea_fk_vers" FOREIGN KEY ("version_proyecto","version") REFERENCES "apex_ap_version" ("proyecto","version") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_ap_tarea_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_ap_tarea_usuario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: tarea, usuario
--: dump_from: apex_ap_tarea
--: dump_where: (apex_ap_tarea.tarea = dd.tarea) AND (apex_ap_tarea.proyecto ='%%')
--: zona: admin_proyectos
--: instancia: 1
--: desc: Prioridad de Tarea
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tarea					            int4           NOT NULL, 
   usuario                    	varchar(20) 	NOT NULL,
	fecha_inicio						date				NULL,
	fecha_fin							date				NULL,
	observacion  			       	varchar(255)	NULL,
   CONSTRAINT  "apex_ap_tarea_usu_pk"   PRIMARY KEY ("tarea","usuario"),
   CONSTRAINT  "apex_ap_tarea_usu_fk_tarea" FOREIGN KEY ("tarea") REFERENCES "apex_ap_tarea" ("tarea") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_ap_tarea_usu_fk_usu" FOREIGN KEY ("usuario") REFERENCES "apex_usuario" ("usuario") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################
