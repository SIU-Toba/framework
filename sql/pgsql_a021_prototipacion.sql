--#################################################################################################--
--###############   Prototipacion  ################################################################--
--#################################################################################################--

CREATE TABLE apex_item_proto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: item
--: dump_where: (	item_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	item_proyecto						varchar(15)		NOT NULL,
	item								varchar(60)	NOT NULL,
	descripcion							varchar			NULL,
	logica								varchar			NULL,
	CONSTRAINT	"apex_item_proto_pk" PRIMARY	KEY ("item_proyecto","item"),
	CONSTRAINT	"apex_item_proto_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES	"apex_item" ("proyecto","item")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################
--#######   CLASE   ################################################################################
--#################################################################################################

CREATE TABLE apex_clase_proto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase
--: dump_where: (	clase_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	clase_proyecto						varchar(15)		NOT NULL,
	clase								int4			NOT NULL,
	descripcion							varchar			NULL,
	logica								varchar			NULL,
	CONSTRAINT	"apex_clase_proto_pk" PRIMARY	KEY ("clase_proyecto","clase"),
	CONSTRAINT	"apex_clase_proto_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES	"apex_clase" ("proyecto","clase")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_clase_proto_metodo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase
--: dump_where: (	clase_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	clase_proyecto						varchar(15)		NOT NULL,
	clase								int4			NOT NULL,
	metodo								varchar(50)		NOT NULL,
	orden								float			NULL,
	acceso								varchar(40)		NULL,
	descripcion							varchar			NULL,
	parametros							varchar(255)	NULL,
	retorno								varchar(255)	NULL,
	logica								varchar			NULL,
	php									varchar			NULL,
	auto_subclase						smallint		NULL,
	CONSTRAINT	"apex_clase_promet_pk" PRIMARY	KEY ("clase_proyecto","clase","metodo"),
	CONSTRAINT	"apex_clase_promet_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES	"apex_clase" ("proyecto","clase")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_clase_proto_propiedad
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase
--: dump_where: (	clase_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	clase_proyecto						varchar(15)		NOT NULL,
	clase								int4			NOT NULL,
	propiedad							varchar(50)		NOT NULL,
	orden								float			NULL,
	tipo								varchar(40)		NULL,
	descripcion							varchar			NULL,
	CONSTRAINT	"apex_clase_proprop_pk" PRIMARY	KEY ("clase_proyecto","clase","propiedad"),
	CONSTRAINT	"apex_clase_proprop_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES	"apex_clase" ("proyecto","clase")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################
--#######   OBJETO   ################################################################################
--#################################################################################################

CREATE TABLE apex_objeto_proto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto
--: dump_where: (	objeto_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto						varchar(15)		NOT NULL,
	objeto								int4			NOT NULL,
	descripcion							varchar			NULL,
	logica								varchar			NULL,
	CONSTRAINT	"apex_objeto_proto_pk" PRIMARY	KEY ("objeto_proyecto","objeto"),
	CONSTRAINT	"apex_objeto_proto_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_proto_metodo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto
--: dump_where: (	objeto_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto						varchar(15)		NOT NULL,
	objeto								int4			NOT NULL,
	metodo								varchar(50)		NOT NULL,
	orden								float			NULL,
	acceso								varchar(40)		NULL,
	descripcion							varchar			NULL,
	parametros							varchar(255)	NULL,
	retorno								varchar(255)	NULL,
	logica								varchar			NULL,
	php									varchar			NULL,
	CONSTRAINT	"apex_objeto_promet_pk" PRIMARY	KEY ("objeto_proyecto","objeto","metodo"),
	CONSTRAINT	"apex_objeto_promet_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_proto_propiedad
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto
--: dump_where: (	objeto_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_proyecto						varchar(15)		NOT NULL,
	objeto								int4			NOT NULL,
	propiedad							varchar(50)		NOT NULL,
	orden								float			NULL,
	tipo								varchar(40)		NULL,
	descripcion							varchar			NULL,
	CONSTRAINT	"apex_objeto_proprop_pk" PRIMARY	KEY ("objeto_proyecto","objeto","propiedad"),
	CONSTRAINT	"apex_objeto_proprop_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################
--#######   NUCLEO   ################################################################################
--#################################################################################################

CREATE TABLE apex_nucleo_proto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nucleo
--: dump_where: (	nucleo_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	nucleo_proyecto						varchar(15)		NOT NULL,
	nucleo								int4			NOT NULL,
	descripcion							varchar			NULL,
	logica								varchar			NULL,
	CONSTRAINT	"apex_nucleo_proto_pk" PRIMARY	KEY ("nucleo_proyecto","nucleo"),
	CONSTRAINT	"apex_nucleo_proto_fk_nucleo" FOREIGN KEY ("nucleo_proyecto","nucleo") REFERENCES	"apex_nucleo" ("proyecto","nucleo")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_nucleo_proto_metodo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nucleo
--: dump_where: (	nucleo_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	nucleo_proyecto						varchar(15)		NOT NULL,
	nucleo								int4			NOT NULL,
	metodo								varchar(50)		NOT NULL,
	orden								float			NULL,
	acceso								varchar(40)		NULL,
	descripcion							varchar			NULL,
	parametros							varchar(255)	NULL,
	retorno								varchar(255)	NULL,
	logica								varchar			NULL,
	php									varchar			NULL,
	CONSTRAINT	"apex_nucleo_promet_pk" PRIMARY	KEY ("nucleo_proyecto","nucleo","metodo"),
	CONSTRAINT	"apex_nucleo_promet_fk_nucleo" FOREIGN KEY ("nucleo_proyecto","nucleo") REFERENCES	"apex_nucleo" ("proyecto","nucleo")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_nucleo_proto_propiedad
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nucleo
--: dump_where: (	nucleo_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	nucleo_proyecto						varchar(15)		NOT NULL,
	nucleo								int4			NOT NULL,
	propiedad							varchar(50)		NOT NULL,
	orden								float			NULL,
	tipo								varchar(40)		NULL,
	descripcion							varchar			NULL,
	CONSTRAINT	"apex_nucleo_proprop_pk" PRIMARY	KEY ("nucleo_proyecto","nucleo","propiedad"),
	CONSTRAINT	"apex_nucleo_proprop_fk_nucleo" FOREIGN KEY ("nucleo_proyecto","nucleo") REFERENCES	"apex_nucleo" ("proyecto","nucleo")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################
