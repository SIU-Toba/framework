--**************************************************************************************************
--**************************************************************************************************
--**************************************	MT	- Multietapa  *****************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_mt_me_tipo_nav
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
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
--: dump: multiproyecto
--: dump_order_by: objeto_mt_me
--: dump_where: (	objeto_mt_me_proyecto =	'%%' )
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_mt_me_proyecto					varchar(15)			NOT NULL,
	objeto_mt_me							int4				NOT NULL,
	ev_procesar_etiq						varchar(30)			NULL,
	ev_cancelar_etiq						varchar(30)			NULL,
	ancho									varchar(20)			NULL,
	alto									varchar(20)			NULL,
	posicion_botonera						varchar(10)			NULL,
	tipo_navegacion							varchar(10)			NULL,
	con_toc									smallint			NULL,
	incremental								smallint			NULL,
	debug_eventos							smallint			NULL,
	activacion_procesar						varchar(40)			NULL, --> DEPRECADO CN: Indica cuando procesar
	activacion_cancelar						varchar(40)			NULL, --> DEPRECADO CN: Indica cuando se puede cancelar
	ev_procesar								smallint			NULL,
	ev_cancelar								smallint			NULL,
	objetos									varchar(255)		NULL,	
	post_procesar							varchar(40)			NULL, --> CN: Informacion posterior al proceso
	metodo_despachador						varchar(40)			NULL,  --> CN: Indica la etapa activa
	metodo_opciones							varchar(40)			NULL,  --> CN: Indica los posibles caminos de la operacion
	CONSTRAINT	"apex_objeto_mt_me_pk" PRIMARY	KEY ("objeto_mt_me_proyecto","objeto_mt_me"),
	CONSTRAINT	"obj_objeto_mt_me_fk_objeto" FOREIGN	KEY ("objeto_mt_me_proyecto","objeto_mt_me")	REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"obj_objeto_mt_me_fk_tnav" FOREIGN	KEY ("tipo_navegacion")	REFERENCES "apex_objeto_mt_me_tipo_nav" ("tipo_navegacion") ON DELETE	NO	ACTION ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_objeto_mt_me_etapa
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_mt_me,	posicion
--: dump_where: (	objeto_mt_me_proyecto =	'%%' )
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_mt_me_proyecto				varchar(15)			NOT NULL,
	objeto_mt_me						int4				NOT NULL,
	posicion							smallint			NOT NULL,
	orden								smallint			NULL,	-- Hay que ponerlo como NOT NULL
	etiqueta							varchar(80)			NULL,
	descripcion							varchar(255)		NULL,
	tip									varchar(80)			NULL,
	imagen_recurso_origen				varchar(10)			NULL,
	imagen								varchar(60)			NULL,
	objetos								varchar(80)			NULL, 	-- ya no se usan!
	objetos_adhoc						varchar(80)			NULL, 	-- ya no se usan!
	pre_condicion						varchar(40)			NULL,	-- ya no se usan!
	post_condicion						varchar(40)			NULL,	-- ya no se usan!
	gen_interface_pre					varchar(40)			NULL,	-- ya no se usan!
	gen_interface_post					varchar(40)			NULL,	-- ya no se usan!
	ev_procesar							smallint			NULL, 	-- Esta etapa muestra el boton procesar
	ev_cancelar							smallint			NULL, 	-- Esta etapa muestra el boton cancelar
	CONSTRAINT	"apex_mt_me__pk" PRIMARY KEY ("objeto_mt_me_proyecto","objeto_mt_me","posicion"),
	CONSTRAINT	"apex_mt_me__fk_padre" FOREIGN KEY ("objeto_mt_me_proyecto","objeto_mt_me") REFERENCES	"apex_objeto_mt_me" ("objeto_mt_me_proyecto","objeto_mt_me") ON DELETE CASCADE ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_item_fk_rec_orig"	FOREIGN KEY	("imagen_recurso_origen") REFERENCES "apex_recurso_origen" ("recurso_origen")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
--###################################################################################################

CREATE SEQUENCE apex_obj_ci_pantalla_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_ci_pantalla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_ci_proyecto, objeto_ci, pantalla
--: dump_where: (	objeto_ci_proyecto =	'%%' )
--: zona: objeto
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_ci_proyecto					varchar(15)			NOT NULL,
	objeto_ci							int4				NOT NULL,
	pantalla							int4				DEFAULT nextval('"apex_obj_ci_pantalla_seq"'::text) NOT NULL, 
	identificador						varchar(20)			NOT NULL,
	orden								smallint			NULL,	-- Hay que ponerlo como NOT NULL
	etiqueta							varchar(80)			NULL,
	descripcion							varchar(255)		NULL,
	tip									varchar(80)			NULL,
	imagen_recurso_origen				varchar(10)			NULL,
	imagen								varchar(60)			NULL,
	objetos								varchar(80)			NULL,
	eventos								varchar(80)			NULL,
	CONSTRAINT	"apex_obj_ci_pan__pk" PRIMARY KEY ("objeto_ci_proyecto","objeto_ci","pantalla"),
	CONSTRAINT	"apex_obj_ci_pan__fk_padre" FOREIGN KEY ("objeto_ci_proyecto","objeto_ci") REFERENCES "apex_objeto_mt_me" ("objeto_mt_me_proyecto","objeto_mt_me") ON DELETE CASCADE ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_obj_ci_pan_fk_rec_orig"	FOREIGN KEY	("imagen_recurso_origen") REFERENCES "apex_recurso_origen" ("recurso_origen")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################