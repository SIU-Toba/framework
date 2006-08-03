CREATE TABLE			apex_revision
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: revision
--: zona: general
--: desc: Especifica la revision del SVN con que se creo el proyecto
--: version: 1.0
--: instancia: 1
---------------------------------------------------------------------------------------------------
(
	revision					varchar(20)	NOT NULL,
	creacion					timestamp(0) without	time zone	DEFAULT current_timestamp NOT	NULL
);
--#################################################################################################

CREATE TABLE apex_instancia
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: instancia
--: instancia:	1
--: zona: general
--: desc: Datos de la instancia
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	instancia					varchar(80)		NOT NULL,
	version						varchar(15)		NOT NULL,
	institucion					varchar(255)	NULL,
	observaciones				varchar(255)	NULL,
	administrador_1				varchar(60)		NULL,--NOT
	administrador_2				varchar(60)		NULL,--NOT
	administrador_3				varchar(60)		NULL,--NOT
	creacion					timestamp(0) without	time zone	DEFAULT current_timestamp NOT	NULL,
	CONSTRAINT	"apex_instancia_pk"	 PRIMARY	KEY ("instancia")
);
--#################################################################################################