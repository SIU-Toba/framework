--**************************************************************************************************
--**************************************************************************************************
--********************************   Tareas PROGRAMADAS   ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE SEQUENCE apex_tp_tarea_tipo_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_tp_tarea_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: tarea_tipo
--: zona: admin_proyectos
--: desc: Tipos de tarea
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tarea_tipo  		            int4			DEFAULT nextval('"tpex_tp_tarea_tipo_seq"'::text) NOT NULL, 
	descripcion  			       	varchar(70)   	NOT NULL,
   CONSTRAINT  "apex_tp_tarea_tipo_pk"   PRIMARY KEY ("tarea_tipo")
);
--#################################################################################################


CREATE SEQUENCE apex_tp_tarea_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_tp_tarea
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: tarea
--: zona: admin_proyectos
--: desc: Tabla de manejo de versiones
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto             		varchar(15)    		NOT NULL,
	tarea	  		            int4          		DEFAULT nextval('"apex_tp_seq"'::text) NOT NULL, 
	item_id						int4				NULL,	
	item_proyecto				varchar(15)			NULL,
	item						varchar(60)			NULL,	--> Item	del catalogo a	invocar como instanciador de objetos de esta	clase
	activada					smallint			NULL,
   	descripcion          	 	varchar(255)   		NOT NULL,
	tarea_tipo					int4				NOT NULL,
	fecha						date				NULL,
	hora						time				NOT NULL,
   	CONSTRAINT  "apex_tp_tarea_pk" PRIMARY KEY ("proyecto","tarea"),
	CONSTRAINT	"apex_tp_tarea_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE NO ACTION NOT	DEFERRABLE INITIALLY	IMMEDIATE,
   	CONSTRAINT  "apex_tp_tarea_fk_tt" FOREIGN KEY ("tarea_tipo") REFERENCES "apex_tp_tarea_tipo" ("tarea_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   	CONSTRAINT  "apex_tp_tarea_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################
