--**************************************************************************************************
--**************************************************************************************************
--**************************************	MT	- Multietapa  *****************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_mt_me_tipo_nav
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: tipo_navegacion
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	tipo_navegacion							varchar(10)			NOT NULL,
	descripcion								varchar(30)			NOT	NULL,
	CONSTRAINT	"apex_objeto_mt_me_tn_pk" PRIMARY	KEY ("tipo_navegacion")
);
--###################################################################################################

CREATE TABLE apex_objeto_mt_me
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_mt_me_proyecto
--: dump_clave_componente: objeto_mt_me
--: dump_order_by: objeto_mt_me
--: dump_where: (	objeto_mt_me_proyecto =	'%%' )
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_mt_me_proyecto					varchar(15)			NOT NULL,
	objeto_mt_me							int8				NOT NULL,
	ev_procesar_etiq						varchar(30)			NULL,
	ev_cancelar_etiq						varchar(30)			NULL,
	ancho									varchar(20)			NULL,
	alto									varchar(20)			NULL,
	posicion_botonera						varchar(10)			NULL,
	tipo_navegacion							varchar(10)			NULL,
	botonera_barra_item						smallint			NULL,
	con_toc									smallint			NULL,
	incremental								smallint			NULL,
	debug_eventos							smallint			NULL,
	activacion_procesar						varchar(40)			NULL, -- OBSOLETO CN: Indica cuando procesar
	activacion_cancelar						varchar(40)			NULL, -- OBSOLETO CN: Indica cuando se puede cancelar
	ev_procesar								smallint			NULL, -- OBSOLETO
	ev_cancelar								smallint			NULL, -- OBSOLETO
	objetos									varchar(255)		NULL, -- OBSOLETO
	post_procesar							varchar(40)			NULL, --> CN: Informacion posterior al proceso
	metodo_despachador						varchar(40)			NULL,  --> CN: Indica la etapa activa
	metodo_opciones							varchar(40)			NULL,  --> CN: Indica los posibles caminos de la operacion
	CONSTRAINT	"apex_objeto_mt_me_pk" PRIMARY	KEY ("objeto_mt_me_proyecto","objeto_mt_me"),
	CONSTRAINT	"obj_objeto_mt_me_fk_objeto" FOREIGN	KEY ("objeto_mt_me_proyecto","objeto_mt_me")	REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"obj_objeto_mt_me_fk_tnav" FOREIGN	KEY ("tipo_navegacion")	REFERENCES "apex_objeto_mt_me_tipo_nav" ("tipo_navegacion") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--###################################################################################################
--###################################################################################################

CREATE SEQUENCE apex_obj_ci_pantalla_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_ci_pantalla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_ci_proyecto
--: dump_clave_componente: objeto_ci
--: dump_order_by: objeto_ci_proyecto, objeto_ci, pantalla
--: dump_where: (	objeto_ci_proyecto =	'%%' )
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_ci_proyecto					varchar(15)			NOT NULL,
	objeto_ci							int8				NOT NULL,
	pantalla							int8				DEFAULT nextval('"apex_obj_ci_pantalla_seq"'::text) NOT NULL, 
	identificador						varchar(40)			NOT NULL,
	orden								smallint			NULL,	-- Hay que ponerlo como NOT NULL
	etiqueta							varchar(80)			NULL,
	descripcion							TEXT				NULL,
	tip									TEXT				NULL,
	imagen_recurso_origen				varchar(10)			NULL,
	imagen								varchar(60)			NULL,
	objetos								varchar				NULL,	--OBSOLETO
	eventos								varchar				NULL,	--OBSOLETO
	subclase							varchar(80)			NULL,
	subclase_archivo					varchar(255)		NULL,
	template							TEXT				NULL,
	template_impresion					TEXT				NULL,
	punto_montaje						int8				NULL,
	CONSTRAINT	"apex_obj_ci_pan__pk" PRIMARY KEY ("pantalla","objeto_ci","objeto_ci_proyecto"),
   	CONSTRAINT  "apex_obj_ci_pan__uk" UNIQUE ("objeto_ci_proyecto","objeto_ci","identificador"),
	CONSTRAINT	"apex_obj_ci_pan__fk_padre" FOREIGN KEY ("objeto_ci_proyecto","objeto_ci") REFERENCES "apex_objeto_mt_me" ("objeto_mt_me_proyecto","objeto_mt_me") ON DELETE CASCADE ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_obj_ci_pan_fk_rec_orig"	FOREIGN KEY	("imagen_recurso_origen") REFERENCES "apex_recurso_origen" ("recurso_origen")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_puntos_montaje" FOREIGN KEY ("objeto_ci_proyecto", "punto_montaje")	REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--###################################################################################################
CREATE TABLE apex_objetos_pantalla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto_ci
--: clave_elemento: proyecto, objeto_ci, pantalla, dep_id
--: dump_order_by: proyecto, objeto_ci, pantalla, dep_id
--: dump_where: (	proyecto =	'%%' )
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto		VARCHAR(15) NULL,
	pantalla		 BIGINT NULL,
	objeto_ci		BIGINT NULL,
	orden			 SMALLINT NULL,
	dep_id			BIGINT NULL,
	CONSTRAINT "apex_objetos_pantalla_pk"	PRIMARY KEY ("proyecto", "objeto_ci", "pantalla", "dep_id"),
	CONSTRAINT "apex_objetos_pantalla_apex_objeto_ci_pantalla_fk" FOREIGN KEY ("pantalla", "objeto_ci", "proyecto") REFERENCES "apex_objeto_ci_pantalla" ("pantalla", "objeto_ci", "objeto_ci_proyecto") ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_objetos_pantalla_apex_objeto_dependencias_fk"	FOREIGN KEY ("dep_id", "proyecto", "objeto_ci") REFERENCES "apex_objeto_dependencias" ("dep_id", "proyecto", "objeto_consumidor") ON UPDATE NO ACTION ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
CREATE TABLE apex_eventos_pantalla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: proyecto
--: dump_clave_componente: objeto_ci
--: clave_elemento: proyecto, objeto_ci, pantalla, evento_id
--: dump_order_by: proyecto, objeto_ci, pantalla, evento_id
--: dump_where: (	proyecto =	'%%' )
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	pantalla		  BIGINT NULL,
	objeto_ci		 BIGINT NULL,
	evento_id		BIGINT NULL,
	proyecto		 VARCHAR(15) NULL,
	CONSTRAINT "apex_eventos_pantalla_pk"	PRIMARY KEY ("pantalla", "objeto_ci", "proyecto", "evento_id"),
	CONSTRAINT "apex_eventos_pantalla_apex_objeto_ci_pantalla_fk" FOREIGN KEY ("pantalla", "objeto_ci", "proyecto") REFERENCES "apex_objeto_ci_pantalla" ("pantalla", "objeto_ci", "objeto_ci_proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_eventos_pantalla_apex_objeto_eventos_fk" FOREIGN KEY ("evento_id", "proyecto") REFERENCES "apex_objeto_eventos" ("evento_id", "proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--###################################################################################################