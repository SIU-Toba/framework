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
	activacion_procesar						varchar(40)			NULL, --> CN: Indica cuando procesar
	ev_procesar								smallint			NULL,
	ev_procesar_etiq						varchar(30)			NULL,
	activacion_cancelar						varchar(40)			NULL, --> CN: Indica cuando se puede cancelar
	ev_cancelar								smallint			NULL,
	ev_cancelar_etiq						varchar(30)			NULL,
	objetos									varchar(80)			NULL,	
	post_procesar							varchar(40)			NULL, --> CN: Informacion posterior al proceso
	ancho									varchar(20)			NULL,
	alto									varchar(20)			NULL,
	metodo_despachador						varchar(40)			NULL,  --> CN: Indica la etapa activa
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
	objetos								varchar(80)			NULL, 	-- ATENCION: TEMPORAL!!!
	pre_condicion						varchar(40)			NULL,	--> CN: Metodo a llamar cuando se entra en una etapa
	post_condicion						varchar(40)			NULL,	--> CN: Metodo a llamar cuando se sale de una etapa
	gen_interface_pre					varchar(40)			NULL,	--> CN: Metodo a llamar para generar interface PRE objetos
	gen_interface_post					varchar(40)			NULL,	--> CN: Metodo a llamar para generar interface POST objetos
	ev_procesar							smallint			NULL, -- Esta etapa muestra el boton procesar
	ev_cancelar							smallint			NULL, -- Esta etapa muestra el boton cancelar
	CONSTRAINT	"apex_mt_me__pk" PRIMARY KEY ("objeto_mt_me_proyecto","objeto_mt_me","posicion"),
	CONSTRAINT	"apex_mt_me__fk_padre" FOREIGN KEY ("objeto_mt_me_proyecto","objeto_mt_me") REFERENCES	"apex_objeto_mt_me" ("objeto_mt_me_proyecto","objeto_mt_me") ON DELETE CASCADE ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE
);
--###################################################################################################

