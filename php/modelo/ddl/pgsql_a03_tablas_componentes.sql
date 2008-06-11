--**************************************************************************************************
--**************************************************************************************************
--******************	  ELEMENTOS	CENTRALES (item, clase y objeto)	 ***************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_item_zona
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: zona
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	zona							varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	clave_editable					varchar(100)	NULL,		-- Clave	del EDITABLE manejado en la ZONA
	archivo							varchar(80)		NULL, 		-- Archivo	donde	reside la clase que representa la ZONA
	descripcion						varchar			NULL,		-- OBSOLETO
	consulta_archivo				varchar(255)	NULL,
	consulta_clase					varchar(60)		NULL,
	consulta_metodo					varchar(80)		NULL,
	CONSTRAINT	"apex_item_zona_pk" PRIMARY KEY ("proyecto","zona"),
	CONSTRAINT	"apex_item_zona_fk_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_item_seq	INCREMENT 1	MINVALUE	1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: item
--: dump_order_by: item
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	item_id							int4			DEFAULT nextval('"apex_item_seq"'::text) NULL,
	proyecto						varchar(15)		NOT NULL,
	item							varchar(60)		DEFAULT nextval('"apex_item_seq"'::text) NOT NULL,
	padre_id						int4			NULL,	
	padre_proyecto					varchar(15)		NOT NULL,
	padre							varchar(60)		NOT NULL,
	carpeta							smallint		NULL,
	nivel_acceso					smallint		NULL,
	solicitud_tipo					varchar(20)		NULL,
	pagina_tipo_proyecto			varchar(15)		NULL,
	pagina_tipo						varchar(20)		NULL,
	actividad_buffer_proyecto		varchar(15)		NULL,
	actividad_buffer				int4			NULL,
	actividad_patron_proyecto		varchar(15)		NULL,
	actividad_patron				varchar(20)		NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar			NULL,
	actividad_accion				varchar(80)		NULL,
	menu							smallint		NULL,
	orden							float			NULL,
	solicitud_registrar				smallint		NULL,
	solicitud_obs_tipo_proyecto		varchar(15)		NULL,
	solicitud_obs_tipo				varchar(20)		NULL,
	solicitud_observacion			varchar(90)		NULL,
	solicitud_registrar_cron		smallint		NULL,
	prueba_directorios				smallint		NULL,
	zona_proyecto					varchar(15)		NULL,
	zona							varchar(20)		NULL,
	zona_orden						float			NULL,
	zona_listar						smallint		NULL,
	imagen_recurso_origen			varchar(10)		NULL,
	imagen							varchar(60)		NULL,
	parametro_a						varchar(255)	NULL,
	parametro_b						varchar(255)	NULL,
	parametro_c						varchar(255)	NULL,
	publico							smallint		NULL,
	redirecciona					smallint		NULL,
	usuario							varchar(60)		NULL,
	exportable						smallint		NULL,
	creacion						timestamp(0)	without time zone	DEFAULT current_timestamp NULL,
	CONSTRAINT	"apex_item_pk"	PRIMARY KEY	("item", "proyecto"),
	CONSTRAINT	"apex_item_fk_proyecto"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_item_fk_padre"	FOREIGN KEY	("padre_proyecto","padre")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE CASCADE DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_item_fk_solic_tipo" FOREIGN KEY ("solicitud_tipo")	REFERENCES "apex_solicitud_tipo"	("solicitud_tipo") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_item_fk_solic_ot"	FOREIGN KEY	("solicitud_obs_tipo_proyecto","solicitud_obs_tipo") REFERENCES "apex_solicitud_obs_tipo"	("proyecto","solicitud_obs_tipo") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_item_fk_niv_acc" FOREIGN KEY ("nivel_acceso") REFERENCES	"apex_nivel_acceso" ("nivel_acceso") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_item_fk_pag_tipo"	FOREIGN KEY	("pagina_tipo_proyecto","pagina_tipo")	REFERENCES "apex_pagina_tipo"	("proyecto","pagina_tipo")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_item_fk_zona" FOREIGN KEY ("zona_proyecto","zona")	REFERENCES "apex_item_zona" ("proyecto","zona")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
--	  CONSTRAINT  "apex_item_fk_usuario" FOREIGN	KEY ("usuario") REFERENCES	"apex_usuario"	("usuario")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_item_fk_rec_orig"	FOREIGN KEY	("imagen_recurso_origen") REFERENCES "apex_recurso_origen" ("recurso_origen")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_item_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: item_proyecto
--: dump_clave_componente: item
--: dump_order_by: item
--: dump_where: (	item_proyecto = '%%'	)
--: zona: central
--: desc:
--: version: 1.0
-----------------------------------------	----------------------------------------------------------
(	
	item_id							int4				NULL,	
	item_proyecto					varchar(15)		NOT NULL,
	item								varchar(60)		NOT NULL,
	descripcion_breve				varchar(255)	NULL,
	descripcion_larga				text				NULL,
	CONSTRAINT	"apex_item_info_pk"	 PRIMARY	KEY ("item_proyecto","item"),
	CONSTRAINT	"apex_item_info_fk_item" FOREIGN	KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_clase_tipo_seq	INCREMENT 1	MINVALUE	1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_clase_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: clase_tipo
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	clase_tipo						int4				DEFAULT nextval('"apex_clase_tipo_seq"'::text) NOT	NULL,	
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	icono							varchar(60)			NULL,
	orden							float				NULL,
	metodologia						varchar(10)			NULL, --NOT
	CONSTRAINT	"apex_clase_tipo_pk"	 PRIMARY	KEY ("clase_tipo")
);
--#################################################################################################

CREATE TABLE apex_clase
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: clase
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	clase							varchar(60)		NOT NULL,
	clase_tipo						int4			NOT NULL, 
	archivo							varchar(80)		NULL,
	descripcion						varchar(250)	NOT NULL,
	icono							varchar(60)		NOT NULL, 		--> Icono con	el	que los objetos de la clase aparecen representados	en	las listas
	descripcion_corta				varchar(40)		NULL,			--	NOT NULL, 
	editor_proyecto					varchar(15)		NOT NULL,
	editor_item						varchar(60)		NOT NULL,			--> Item	del catalogo a	invocar como editor de objetos de esta	clase
	objeto_dr_proyecto				varchar(15)		NOT NULL,		
	objeto_dr						int4			NOT NULL,		
	utiliza_fuente_datos			int4			NULL,
	-----------------------------------------------------------
	screenshot						varchar(60)		NULL,			--> Path a una imagen de la clase
	ancestro_proyecto				varchar(15)		NULL,			--> Ancestro a	considerar para incluir	dependencias
	ancestro						varchar(60)		NULL,
	instanciador_id					int4			NULL,	
	instanciador_proyecto			varchar(15)		NULL,
	instanciador_item				varchar(60)		NULL,			--> Item	del catalogo a	invocar como instanciador de objetos de esta	clase
	editor_id						int4			NULL,	
	editor_ancestro_proyecto		varchar(15)		NULL,			--> Ancestro a	considerar para el EDITOR
	editor_ancestro					varchar(60)		NULL,
	plan_dump_objeto				varchar(255)	NULL, 			--> Lista ordenada de tablas	que poseen la definicion del objeto	(respetar FK!)
	sql_info						text			NULL, 			--> SQL	que DUMPEA el estado	del objeto
	doc_clase						varchar(255)	NULL,			--> GIF donde hay	un	Diagrama	de	clases.
	doc_db							varchar(255)	NULL,			--> GIF donde hay	un	DER de las tablas	que necesita la clase.
	doc_sql							varchar(255)	NULL,			--> path	al	archivo que	crea las	tablas.
	vinculos						smallint		NULL,			--> Indica si los	objetos generados	pueden tener vinculos
	autodoc							smallint		NULL,
	parametro_a						varchar(255)	NULL,
	parametro_b						varchar(255)	NULL,
	parametro_c						varchar(255)	NULL,
	exclusivo_toba					smallint		NULL,
	CONSTRAINT	"apex_clase_pk" PRIMARY	KEY ("proyecto","clase"),
	CONSTRAINT	"apex_clase_uq" UNIQUE 	("clase"),
	CONSTRAINT	"apex_clase_fk_proyecto" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_clase_fk_tipo"	FOREIGN KEY	("clase_tipo")	REFERENCES "apex_clase_tipo" ("clase_tipo") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
	---- Se comenta este constraint porque no permite crear una instancia sin el editor
	-- CONSTRAINT	"apex_clase_fk_editor" FOREIGN KEY ("editor_proyecto","editor_item")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE CASCADE DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_clase_relacion_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_clase_relacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: clase_relacion
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL,
	clase_relacion						int4			DEFAULT nextval('"apex_clase_relacion_seq"'::text) NOT NULL, 
	clase_contenedora					varchar(60)		NOT NULL,
	clase_contenida						varchar(60)		NOT NULL,
	CONSTRAINT	"apex_clase_rel_pk" PRIMARY KEY ("clase_relacion"),
	CONSTRAINT	"apex_clase_rel_fk_clase_padre" FOREIGN KEY ("proyecto","clase_contenedora") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_clase_rel_fk_clase_hijo" FOREIGN KEY ("proyecto","clase_contenida") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE	CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL,
	objeto								int4			DEFAULT nextval('"apex_objeto_seq"'::text) NOT NULL, 	
	anterior							varchar(20)		NULL,
	reflexivo							smallint		NULL,
	clase_proyecto						varchar(15)		NOT NULL,
	clase								varchar(60)		NOT NULL,
	subclase							varchar(80)		NULL,
	subclase_archivo					varchar(80)		NULL,
	objeto_categoria_proyecto			varchar(15)		NULL,
	objeto_categoria					varchar(30)		NULL,
	nombre								varchar(120)	NOT NULL,
	titulo								varchar(120)	NULL,
	colapsable							smallint		NULL,
	descripcion							varchar			NULL,
	fuente_datos_proyecto				varchar(15)		NULL,
	fuente_datos						varchar(20)		NULL,
	solicitud_registrar					smallint		NULL,	-- no mas
	solicitud_obj_obs_tipo				varchar(20)		NULL,	-- no mas
	solicitud_obj_observacion			varchar(255)	NULL,	-- no mas
	parametro_a							varchar(100)	NULL,
	parametro_b							varchar(100)	NULL,
	parametro_c							varchar(100)	NULL,
	parametro_d							varchar(100)	NULL,
	parametro_e							varchar(100)	NULL,
	parametro_f							varchar(100)	NULL,
	usuario								varchar(20)		NULL,
	creacion							timestamp(0)	without time zone	DEFAULT current_timestamp NULL,
	CONSTRAINT	"apex_objeto_pk"	 PRIMARY	KEY ("objeto", "proyecto"),
	CONSTRAINT	"apex_objeto_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_fuente_datos"	FOREIGN KEY	("fuente_datos_proyecto","fuente_datos") REFERENCES "apex_fuente_datos"	("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
--  CONSTRAINT  "apex_objeto_fk_usuario"	FOREIGN KEY	("usuario")	REFERENCES "apex_usuario" ("usuario") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
);
--#################################################################################################

CREATE TABLE apex_objeto_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto						varchar(15)			NOT NULL,
	objeto								int4				NOT NULL,
	descripcion_breve					varchar(255)		NULL,
	descripcion_larga					text				NULL,
	CONSTRAINT	"apex_objeto_info_pk" PRIMARY	KEY ("objeto_proyecto","objeto"),
	CONSTRAINT	"apex_objeto_info_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_dep_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_dependencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto_consumidor
--: dump_order_by: objeto_consumidor, identificador
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)			NOT NULL,
	dep_id								int4				DEFAULT nextval('"apex_objeto_dep_seq"'::text) NOT NULL, 
	objeto_consumidor					int4				NOT NULL,
	objeto_proveedor					int4				NOT NULL,
	identificador						varchar(40)			NOT NULL,
	parametros_a						varchar(255)		NULL,
	parametros_b						varchar(255)		NULL,
	parametros_c						varchar(255)		NULL,
	inicializar							smallint			NULL,
	orden								smallint			NULL,
	CONSTRAINT	"apex_objeto_depen_pk"	 PRIMARY	KEY ("dep_id","proyecto","objeto_consumidor"),
--	CONSTRAINT	"apex_objeto_depen_pk"	 PRIMARY	KEY ("proyecto","objeto_consumidor","identificador"),
	CONSTRAINT	"apex_objeto_depen_uq"	 UNIQUE  ("proyecto","objeto_consumidor","identificador"),
	CONSTRAINT	"apex_objeto_depen_fk_objeto_c" FOREIGN KEY ("proyecto","objeto_consumidor") REFERENCES "apex_objeto"	("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_depen_fk_objeto_p" FOREIGN KEY ("proyecto","objeto_proveedor") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE CASCADE ON UPDATE NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_eventos_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_eventos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, orden, identificador
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)			NOT NULL,
	evento_id							int4				DEFAULT nextval('"apex_objeto_eventos_seq"'::text) NOT NULL,
	objeto								int4				NOT NULL,
	identificador						varchar(40)			NOT NULL,
	etiqueta							varchar(255)		NULL,
	maneja_datos						smallint			NULL DEFAULT 1,
	sobre_fila							smallint			NULL,
	confirmacion						varchar(255)		NULL,
	estilo								varchar(40)			NULL,
	imagen_recurso_origen				varchar(10)			NULL,
	imagen								varchar(60)			NULL,
	en_botonera							smallint			NULL DEFAULT 1,
	ayuda								varchar				NULL,
	orden								smallint			NULL,
	ci_predep							smallint			NULL, 
	implicito							smallint			NULL,
	defecto								smallint			NULL,
	display_datos_cargados				smallint			NULL, 
	grupo								varchar(80)			NULL,
	accion								varchar(1)			NULL,
	accion_imphtml_debug				smallint			NULL,
	accion_vinculo_carpeta				varchar(60)			NULL,
	accion_vinculo_item					varchar(60)			NULL,
	accion_vinculo_objeto				int4				NULL,
	accion_vinculo_popup				smallint			NULL,
	accion_vinculo_popup_param			varchar(100)		NULL,
	accion_vinculo_target				varchar(40)			NULL,
	accion_vinculo_celda				varchar(40)			NULL,
	CONSTRAINT	"apex_objeto_eventos_pk" PRIMARY KEY ("evento_id","proyecto"),
	CONSTRAINT	"apex_objeto_eventos_uq" UNIQUE ("proyecto","objeto","identificador"),	
	CONSTRAINT	"apex_objeto_eventos_fk_rec_orig" FOREIGN KEY ("imagen_recurso_origen") REFERENCES "apex_recurso_origen" ("recurso_origen")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_eventos_fk_objeto" FOREIGN KEY ("proyecto","objeto") REFERENCES "apex_objeto"	("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_objeto_eventos_fk_accion_vinculo" FOREIGN KEY ("proyecto","accion_vinculo_item") 	REFERENCES	"apex_item"	("proyecto","item")  ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
	
);
--#################################################################################################

CREATE TABLE apex_ptos_control_x_evento
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto
--: dump_order_by: objeto, evento_id
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  proyecto 					VARCHAR(15) NOT NULL,
  pto_control              	VARCHAR(20) NOT NULL,
  evento_id                	INTEGER     NOT NULL,
  objeto					int4		NOT NULL,
  CONSTRAINT "apex_ptos_ctrl_x_evt__pk" PRIMARY KEY("proyecto", "pto_control", "evento_id"),
  CONSTRAINT "apex_proyecto_fk_ptos_ctrl" FOREIGN KEY ("proyecto", "pto_control") REFERENCES "apex_ptos_control"("proyecto", "pto_control") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
  CONSTRAINT "apex_ptos_ctrl_x_evt_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto"("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE, 
  CONSTRAINT "apex_ptos_ctrl_x_evt_fk_evento" FOREIGN KEY ("evento_id", "proyecto") REFERENCES "apex_objeto_eventos"("evento_id", "proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_item_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: item
--: dump_order_by: item, objeto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	item_id								int4			NULL,	
	proyecto							varchar(15)		NOT NULL,
	item								varchar(60)		NOT NULL,
	objeto								int4			NOT NULL,
	orden								smallint		NOT NULL,
	inicializar							smallint		NULL,
	CONSTRAINT	"apex_item_consumo_obj_pk"	 PRIMARY	KEY ("proyecto","item","objeto"),
	CONSTRAINT	"apex_item_consumo_obj_fk_item" FOREIGN KEY ("proyecto","item") REFERENCES	"apex_item"	("proyecto","item") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_item_consumo_obj_fk_objeto" FOREIGN	KEY ("proyecto","objeto") REFERENCES "apex_objeto"	("proyecto","objeto") ON DELETE CASCADE	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_log_objeto_seq INCREMENT 1 MINVALUE 0 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_log_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: log_objeto
--: dump_where: objeto_proyecto ='%%'
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_objeto							int4			DEFAULT nextval('"apex_log_objeto_seq"'::text) NOT NULL, 
	momento								timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,
	usuario								varchar(60) 	NULL,
	objeto_proyecto          			varchar(15)  	NOT NULL,
	objeto								int4			NULL,
	item								varchar(60)		NULL,
	observacion							varchar			NULL,
	CONSTRAINT	"apex_log_objeto_pk" PRIMARY KEY ("log_objeto")
);
--#################################################################################################

CREATE TABLE apex_arbol_items_fotos

---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario, foto_nombre
--: zona: usuario
--: instancia:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(60)		NOT NULL,
	foto_nombre							varchar(100)	NOT NULL,
	foto_nodos_visibles					varchar			NULL,
	foto_opciones						varchar			NULL,
  CONSTRAINT "apex_arbol_items_fotos_pk" PRIMARY KEY("proyecto", "usuario", "foto_nombre")
  --CONSTRAINT "apex_arbol_items_fotos_fk_proy" 	FOREIGN KEY ("proyecto", "usuario") REFERENCES "apex_usuario_proyecto" ("proyecto", "usuario") ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_admin_album_fotos

---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario, foto_tipo, foto_nombre
--: zona: usuario
--: instancia:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(60)		NOT NULL,
	foto_tipo							varchar(20)		NOT NULL,	--cat_item u cat_objeto
	foto_nombre							varchar(100)	NOT NULL,
	foto_nodos_visibles					varchar			NULL,
	foto_opciones						varchar			NULL,
	predeterminada							smallint	NULL,
  CONSTRAINT "apex_admin_album_fotos_pk" PRIMARY KEY("proyecto", "usuario", "foto_nombre", "foto_tipo")
  --CONSTRAINT "apex_admin_album_fotos_fk_proy" 	FOREIGN KEY ("proyecto", "usuario")	REFERENCES "apex_usuario_proyecto" ("proyecto", "usuario") ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_admin_param_previsualizazion

---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario, proyecto
--: zona: usuario
--: instancia:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(60)		NOT NULL,
	grupo_acceso						varchar(255)	NOT NULL,
	punto_acceso						varchar(100)	NOT NULL,
	perfil_datos						varchar(255)	NULL,
  CONSTRAINT "apex_admin_param_prev_pk" PRIMARY KEY("proyecto", "usuario")
  --CONSTRAINT "apex_admin_param_prev_fk_proy" 	FOREIGN KEY ("proyecto", "usuario")	REFERENCES "apex_usuario_proyecto" ("proyecto", "usuario") ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
 
--#################################################################################################

CREATE TABLE apex_conversion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: dump_where: (	proyecto =	'%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL,
	conversion_aplicada					varchar(60)		NOT NULL,
	fecha								timestamp		NOT NULL,
	CONSTRAINT	"apex_conversion_pk" PRIMARY	KEY ("proyecto","conversion_aplicada"),
	CONSTRAINT	"apex_conversion_proy" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);

