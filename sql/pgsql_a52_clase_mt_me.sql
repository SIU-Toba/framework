--**************************************************************************************************
--**************************************************************************************************
--**************************************	MT	- Multietapa  *****************************************
--**************************************************************************************************
--**************************************************************************************************

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
	incremental								smallint			NULL,
	debug_eventos							smallint			NULL,
	ev_procesar								smallint			NULL,
	ev_procesar_etiq						varchar(30)			NULL,
	ev_cancelar								smallint			NULL,
	ev_cancelar_etiq						varchar(30)			NULL,
	objetos									varchar(80)			NULL,
	ancho									varchar(20)			NULL,
	alto									varchar(20)			NULL,
	activacion_procesar						varchar(40)			NULL, -- Funcion del CN que indica cuando procesar
	CONSTRAINT	"apex_objeto_mt_me_pk" PRIMARY	KEY ("objeto_mt_me_proyecto","objeto_mt_me"),
	CONSTRAINT	"obj_objeto_mt_me_fk_objeto" FOREIGN	KEY ("objeto_mt_me_proyecto","objeto_mt_me")	REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE
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
	etiqueta							varchar(80)			NOT NULL,
	descripcion							varchar(255)		NULL,
	objetos								varchar(80)			NULL, -- ATENCION: TEMPORAL!!!
	pre_condicion						varchar(40)			NULL,
	post_condicion						varchar(40)			NULL,
	ev_procesar							smallint			NULL, -- Esta etapa muestra el boton procesar
	CONSTRAINT	"apex_mt_me__pk" PRIMARY KEY ("objeto_mt_me_proyecto","objeto_mt_me","posicion"),
	CONSTRAINT	"apex_mt_me__fk_padre" FOREIGN KEY ("objeto_mt_me_proyecto","objeto_mt_me") REFERENCES	"apex_objeto_mt_me" ("objeto_mt_me_proyecto","objeto_mt_me") ON DELETE CASCADE ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE
);
--###################################################################################################

