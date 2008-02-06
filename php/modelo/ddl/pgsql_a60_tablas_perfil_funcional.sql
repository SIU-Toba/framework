--*******************************************************************************************
--*******************************************************************************************
--************************************** PERFIL FUNCIONAL ***********************************
--*******************************************************************************************
--*******************************************************************************************

CREATE SEQUENCE apex_perfil_funcional_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_perfil_funcional
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: perfil_funcional
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)			NOT NULL,
	perfil_funcional				int4				DEFAULT nextval('"apex_perfil_funcional_seq"'::text) NOT NULL,
	item							int4				NOT NULL,
	descripcion						varchar(255)		NULL,
	CONSTRAINT	"perfil_funcional_pk" PRIMARY	KEY ("proyecto", "perfil_funcional"),
	CONSTRAINT	"perfil_funcional_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"perfil_funcional_fk_item"	 FOREIGN KEY	("proyecto","item") 
			REFERENCES "apex_item" ("proyecto","item")	
					ON	DELETE CASCADE ON UPDATE	CASCADE  DEFERRABLE INITIALLY IMMEDIATE

);
--#################################################################################################

CREATE TABLE apex_perfil_funcional_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: perfil_funcional, objeto_ei_formulario_fila
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	perfil_funcional				int4				NOT NULL,
	objeto_ei_formulario_fila		int4				NOT NULL,
	objeto_ei_formulario			int4				NOT NULL,
	no_visible						smallint			NULL,
	no_editable						smallint			NULL,
	CONSTRAINT	"apex_perfil_funcional_ef_pk" PRIMARY	KEY ("proyecto","perfil_funcional","objeto_ei_formulario_fila"),
	CONSTRAINT	"apex_perfil_funcional_ef_fk_pf"	FOREIGN KEY	("proyecto","perfil_funcional") 
			REFERENCES	"apex_perfil_funcional" ("proyecto","perfil_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_perfil_funcional_ef_fk_ef"	FOREIGN KEY	("proyecto","objeto_ei_formulario","objeto_ei_formulario_fila") 
			REFERENCES	"apex_objeto_ei_formulario_ef" ("objeto_ei_formulario_proyecto","objeto_ei_formulario","objeto_ei_formulario_fila") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
	
);
--#################################################################################################

CREATE TABLE apex_perfil_funcional_pantalla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: perfil_funcional, pantalla
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	perfil_funcional				int4				NOT NULL,
	pantalla						int4				NOT NULL,
	objeto_ci						int4				NOT NULL,
	no_visible						smallint			NULL,
	CONSTRAINT	"apex_perfil_funcional_pantalla_pk" PRIMARY	KEY ("proyecto","perfil_funcional","pantalla"),
	CONSTRAINT	"apex_perfil_funcional_pantalla_fk_pf"	FOREIGN KEY	("proyecto","perfil_funcional") 
			REFERENCES	"apex_perfil_funcional" ("proyecto","perfil_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_perfil_funcional_pantalla_fk_pantalla"	FOREIGN KEY	("proyecto","objeto_ci","pantalla") 
			REFERENCES	"apex_objeto_ci_pantalla" ("objeto_ci_proyecto","objeto_ci","pantalla") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
	
);
--#################################################################################################

CREATE TABLE apex_perfil_funcional_evt
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: perfil_funcional, evento_id
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	perfil_funcional				int4				NOT NULL,
	evento_id						int4				NOT NULL,
	no_visible						smallint			NULL,
	CONSTRAINT	"apex_perfil_funcional_evt_pk" PRIMARY	KEY ("proyecto","evento_id"),
	CONSTRAINT	"apex_perfil_funcional_evt_fk_pf"	FOREIGN KEY	("proyecto","perfil_funcional") 
			REFERENCES	"apex_perfil_funcional" ("proyecto","perfil_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_perfil_funcional_evt_fk_evt"	FOREIGN KEY	("proyecto","evento_id") 
			REFERENCES	"apex_objeto_eventos" ("proyecto","evento_id") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--#################################################################################################
