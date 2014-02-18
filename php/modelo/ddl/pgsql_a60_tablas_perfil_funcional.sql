--*******************************************************************************************
--*******************************************************************************************
--************************************** PERFIL FUNCIONAL ***********************************
--*******************************************************************************************
--*******************************************************************************************

CREATE TABLE apex_usuario_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: usuario_grupo_acc
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	usuario_grupo_acc				varchar(30)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	nivel_acceso					smallint		NULL,
	descripcion						TEXT			NULL,
	vencimiento						date			NULL,
	dias							smallint		NULL,
	hora_entrada					time(0) without time	zone NULL,
	hora_salida						time(0) without time	zone NULL,
	listar							smallint			NULL,
	permite_edicion					smallint 		NOT NULL 	DEFAULT 1,
	menu_usuario						VARCHAR(50)	NULL,
	CONSTRAINT	"apex_usu_g_acc_pk" PRIMARY KEY ("proyecto","usuario_grupo_acc")
	--CONSTRAINT	"apex_usu_g_acc_fk_niv"	FOREIGN KEY	("nivel_acceso") REFERENCES "apex_nivel_acceso"	("nivel_acceso") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
);
--#################################################################################################

CREATE TABLE apex_usuario_grupo_acc_miembros
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: usuario_grupo_acc
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(30)		NOT NULL,
	usuario_grupo_acc_pertenece			varchar(30)		NOT NULL,	-- Perfil al cual pertenece el grupo actual
	CONSTRAINT	"apex_usu_g_acc_miembros_pk" 			PRIMARY KEY ("proyecto","usuario_grupo_acc", "usuario_grupo_acc_pertenece"),
	CONSTRAINT	"apex_usu_g_acc_fk_us_gru_acc"			FOREIGN KEY	("proyecto","usuario_grupo_acc")			REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,	
	CONSTRAINT	"apex_usu_g_acc_fk_us_gru_acc_pertenece"	FOREIGN KEY	("proyecto","usuario_grupo_acc_pertenece")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
	
);
--#################################################################################################

CREATE TABLE apex_usuario_grupo_acc_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: usuario_grupo_acc, item
--: zona: usuario, item
--: desc:
--: columna_grupo_desarrollo: item
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)		NOT NULL,
	usuario_grupo_acc				varchar(30)		NOT NULL,
	item_id							int8				NULL,	
	item							varchar(60)		NOT NULL,
	CONSTRAINT	"apex_usu_item_pk" PRIMARY	KEY ("proyecto","usuario_grupo_acc","item"),
	CONSTRAINT	"apex_usu_item_fk_us_gru_acc"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usu_item_fk_item"	 FOREIGN KEY	("proyecto","item") 
			REFERENCES "apex_item" ("proyecto","item")	
					ON	DELETE CASCADE ON UPDATE	CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);

--*******************************************************************************************
--*******************************************************************************************
--*******************************  Esquema de PERMISOS **************************************
--*******************************************************************************************
--*******************************************************************************************

CREATE TABLE apex_permiso_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: permiso, usuario_grupo_acc
--: zona: usuario
--: desc:
--: columna_grupo_desarrollo: permiso
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(30)		NOT NULL,
	permiso								int8			NOT NULL,
	CONSTRAINT	"apex_per_grupo_acc_pk" 		PRIMARY	KEY ("usuario_grupo_acc","permiso","proyecto"),
	CONSTRAINT  "apex_per_grupo_acc_per_fk" 	FOREIGN KEY ("permiso","proyecto") 	REFERENCES "apex_permiso" ("permiso","proyecto") 	ON	DELETE NO ACTION 	ON UPDATE	NO	ACTION 	DEFERRABLE 	INITIALLY 	IMMEDIATE,
	CONSTRAINT	"apex_per_grupo_acc_grupo_fk"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);

--*******************************************************************************************
--*******************************************************************************************
--*******************************  RESTRICCIONES FUNCIONALES ********************************
--*******************************************************************************************
--*******************************************************************************************

CREATE SEQUENCE apex_restriccion_funcional_seq	INCREMENT 1	MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_restriccion_funcional
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: restriccion_funcional
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)			NOT NULL,
	restriccion_funcional			int8				DEFAULT nextval('"apex_restriccion_funcional_seq"'::text) NOT NULL,
	descripcion						TEXT		NULL,
	permite_edicion					smallint 			NOT NULL 	DEFAULT 1,
	CONSTRAINT	"restriccion_funcional_pk" PRIMARY	KEY ("proyecto", "restriccion_funcional"),
	CONSTRAINT	"restriccion_funcional_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_grupo_acc_restriccion_funcional
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: usuario_grupo_acc, restriccion_funcional
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(30)		NOT NULL,
	restriccion_funcional				int8			NOT NULL,
	CONSTRAINT	"apex_grupo_acc_restriccion_funcional_pk" 		PRIMARY	KEY ("usuario_grupo_acc","restriccion_funcional","proyecto"),
	CONSTRAINT	"apex_grupo_acc_restriccion_funcional_rf_fk"	FOREIGN KEY	("proyecto","restriccion_funcional")	REFERENCES "apex_restriccion_funcional"	("proyecto","restriccion_funcional")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_restriccion_funcional_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: restriccion_funcional, objeto_ei_formulario_fila
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	restriccion_funcional				int8				NOT NULL,
	item							varchar(60)		NOT NULL,
	objeto_ei_formulario_fila		int8				NOT NULL,
	objeto_ei_formulario			int8				NOT NULL,
	no_visible						smallint			NULL,
	no_editable						smallint			NULL,
	CONSTRAINT	"apex_restriccion_funcional_ef_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","item","objeto_ei_formulario_fila"),
	CONSTRAINT	"apex_restriccion_funcional_ef_fk_pf"	FOREIGN KEY	("proyecto","restriccion_funcional") 
			REFERENCES	"apex_restriccion_funcional" ("proyecto","restriccion_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_restriccion_funcional_ef_fk_ef"	FOREIGN KEY	("proyecto","objeto_ei_formulario","objeto_ei_formulario_fila") 
			REFERENCES	"apex_objeto_ei_formulario_ef" ("objeto_ei_formulario_proyecto","objeto_ei_formulario","objeto_ei_formulario_fila") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"restriccion_funcional_ef_fk_item"	 FOREIGN KEY	("proyecto","item")
			REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE
			CASCADE  DEFERRABLE INITIALLY IMMEDIATE
	
);
--#################################################################################################

CREATE TABLE apex_restriccion_funcional_pantalla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: restriccion_funcional, pantalla
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	restriccion_funcional				int8				NOT NULL,
	item							varchar(60)		NOT NULL,
	pantalla						int8				NOT NULL,
	objeto_ci						int8				NOT NULL,
	no_visible						smallint			NULL,
	CONSTRAINT	"apex_restriccion_funcional_pantalla_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","item", "pantalla"),
	CONSTRAINT	"apex_restriccion_funcional_pantalla_fk_pf"	FOREIGN KEY	("proyecto","restriccion_funcional") 
			REFERENCES	"apex_restriccion_funcional" ("proyecto","restriccion_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_restriccion_funcional_pantalla_fk_pantalla"	FOREIGN KEY	("proyecto","objeto_ci","pantalla") 
			REFERENCES	"apex_objeto_ci_pantalla" ("objeto_ci_proyecto","objeto_ci","pantalla") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"restriccion_funcional_pantalla_fk_item"	 FOREIGN KEY	("proyecto","item")
			REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE
			CASCADE  DEFERRABLE INITIALLY IMMEDIATE
	
);
--#################################################################################################

CREATE TABLE apex_restriccion_funcional_evt
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: restriccion_funcional, evento_id
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	restriccion_funcional				int8				NOT NULL,
	item							varchar(60)		NOT NULL,
	evento_id						int8				NOT NULL,
	no_visible						smallint			NULL,
	CONSTRAINT	"apex_restriccion_funcional_evt_pk" PRIMARY	KEY ("proyecto","restriccion_funcional", "item", "evento_id"),
	CONSTRAINT	"apex_restriccion_funcional_evt_fk_pf"	FOREIGN KEY	("proyecto","restriccion_funcional") 
			REFERENCES	"apex_restriccion_funcional" ("proyecto","restriccion_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_restriccion_funcional_evt_fk_evt"	FOREIGN KEY	("proyecto","evento_id") 
			REFERENCES	"apex_objeto_eventos" ("proyecto","evento_id") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"restriccion_funcional_evt_fk_item"	 FOREIGN KEY	("proyecto","item")
			REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE
			CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_restriccion_funcional_ei
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: restriccion_funcional, objeto
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	restriccion_funcional				int8				NOT NULL,
	item							varchar(60)		NOT NULL,
	objeto							int8				NOT NULL,
	no_visible						smallint			NULL,
	CONSTRAINT	"apex_restriccion_funcional_ei_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","item", "objeto"),
	CONSTRAINT	"apex_restriccion_funcional_ei_fk_pf"	FOREIGN KEY	("proyecto","restriccion_funcional") 
			REFERENCES	"apex_restriccion_funcional" ("proyecto","restriccion_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_restriccion_funcional_ei_fk_evt"	FOREIGN KEY	("proyecto","objeto") 
			REFERENCES	"apex_objeto" ("proyecto","objeto") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"restriccion_funcional_ei_fk_item"	 FOREIGN KEY	("proyecto","item")
			REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE
			CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_restriccion_funcional_cols
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: restriccion_funcional, objeto_cuadro_col
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	restriccion_funcional				int8				NOT NULL,
	item							varchar(60)		NOT NULL,
	objeto_cuadro					int8				NOT NULL,
	objeto_cuadro_col				int8				NOT NULL,
	no_visible						smallint			NULL,
	CONSTRAINT	"apex_restriccion_funcional_cols_pk" PRIMARY	KEY ("proyecto","restriccion_funcional", "item", "objeto_cuadro_col"),
	CONSTRAINT	"apex_restriccion_funcional_cols_fk_pf"	FOREIGN KEY	("proyecto","restriccion_funcional") 
			REFERENCES	"apex_restriccion_funcional" ("proyecto","restriccion_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_restriccion_funcional_cols_fk_evt"	FOREIGN KEY	("proyecto","objeto_cuadro","objeto_cuadro_col") 
			REFERENCES	"apex_objeto_ei_cuadro_columna" ("objeto_cuadro_proyecto","objeto_cuadro","objeto_cuadro_col") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"restriccion_funcional_cols_fk_item"	 FOREIGN KEY	("proyecto","item")
			REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE
			CASCADE  DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_restriccion_funcional_filtro_cols
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: restriccion_funcional, objeto_ei_filtro_col
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)			NOT NULL,
	restriccion_funcional			int8				NOT NULL,
	item							varchar(60)			NOT NULL,
	objeto_ei_filtro_col			int8				NOT NULL,
	objeto_ei_filtro				int8				NOT NULL,
	no_visible						smallint			NULL,
	CONSTRAINT	"apex_restriccion_funcional_filtro_col_pk" PRIMARY	KEY ("proyecto","restriccion_funcional","item", "objeto_ei_filtro_col"),
	CONSTRAINT	"apex_restriccion_funcional_filtro_col_fk_pf"	FOREIGN KEY	("proyecto","restriccion_funcional") 
			REFERENCES	"apex_restriccion_funcional" ("proyecto","restriccion_funcional") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_restriccion_funcional_filtro_col_fk_col"	FOREIGN KEY	("proyecto","objeto_ei_filtro","objeto_ei_filtro_col") 
			REFERENCES	"apex_objeto_ei_filtro_col" ("objeto_ei_filtro_proyecto","objeto_ei_filtro","objeto_ei_filtro_col") 
			ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"restriccion_funcional_filtro_col_fk_item"	 FOREIGN KEY	("proyecto","item")
			REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE
			CASCADE  DEFERRABLE INITIALLY IMMEDIATE
	
);
--#################################################################################################


CREATE TABLE apex_menu
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: proyecto, menu_id
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						VARCHAR(15)		NOT NULL, 
	menu_id						VARCHAR(50)		NOT NULL, 
	descripcion					TEXT			NULL, 
	tipo_menu					varchar(40)		NOT NULL,
	CONSTRAINT	"apex_menu_pk"	PRIMARY KEY ("proyecto", "menu_id"), 
	CONSTRAINT "apex_menu_proyecto_fk"		FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_menu_menu_tipos_fk"	FOREIGN KEY ("tipo_menu") REFERENCES "apex_menu_tipos" ("menu") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE SEQUENCE apex_menu_operaciones_seq	INCREMENT 1	MINVALUE	1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_menu_operaciones
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: permisos
--: dump_order_by: proyecto, menu_id
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						VARCHAR(15)		NOT NULL, 
	menu_id						VARCHAR(50)		NOT NULL, 
	menu_elemento				BIGINT			NOT NULL DEFAULT nextval('"apex_menu_operaciones_seq"'::text),
	item							VARCHAR(60)		NULL, 
	padre						VARCHAR(60)		NULL,
	descripcion					TEXT			NULL,
	carpeta						SMALLINT		NOT NULL DEFAULT 0,
	CONSTRAINT	"apex_menu_operaciones_pk"	PRIMARY KEY ("proyecto", "menu_id", "menu_elemento"), 
	CONSTRAINT "apex_menu_operaciones_apex_proyecto_fk"	FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_menu_operaciones_item_fk"	FOREIGN KEY ("proyecto", "item") REFERENCES "apex_item" ("proyecto", "item") ON DELETE NO ACTION  ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE, 
	CONSTRAINT "apex_menu_operaciones_auto_fk" FOREIGN KEY ("proyecto", "menu_id", "menu_elemento") REFERENCES "apex_menu_operaciones" ON DELETE NO ACTION  ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE 
);
