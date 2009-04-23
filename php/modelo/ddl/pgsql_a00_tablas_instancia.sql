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

CREATE TABLE			apex_proyecto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo_multiproyecto
--: dump_order_by: proyecto
--: zona: general
--: desc: Tabla maestra	de	proyectos
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	descripcion_corta					varchar(60)		NOT NULL, 
	estilo								varchar(30)		NOT NULL,
	con_frames							smallint		DEFAULT 1 NULL,
	frames_clase						varchar(40)		NULL,
	frames_archivo						varchar(255)	NULL,
	salida_impr_html_c					varchar(40)		NULL,
	salida_impr_html_a					varchar(255)	NULL,
	menu								varchar(15)		NULL,
	path_includes						varchar(255)	NULL,
	path_browser						varchar(255)	NULL,
	administrador						varchar(60)		NULL,
	listar_multiproyecto				smallint		NULL,
	orden								float			NULL,
	palabra_vinculo_std					varchar(30)		NULL,
	version_toba						varchar(15)		NULL,
	requiere_validacion					smallint		NULL,
	usuario_anonimo						varchar(60)		NULL,
	usuario_anonimo_desc				varchar(60)		NULL,
	usuario_anonimo_grupos_acc			varchar(255)	NULL,
	validacion_intentos					smallint		NULL,
	validacion_intentos_min				smallint		NULL,
	validacion_bloquear_usuario			smallint		DEFAULT 1 NULL,
	validacion_debug					smallint		NULL,
	sesion_tiempo_no_interac_min		smallint		NULL,
	sesion_tiempo_maximo_min			smallint		NULL,
	sesion_subclase						varchar(60)		NULL,
	sesion_subclase_archivo				varchar(255)	NULL,
	contexto_ejecucion_subclase			varchar(60)		NULL,
	contexto_ejecucion_subclase_archivo	varchar(255)	NULL,
	usuario_subclase					varchar(60)		NULL,
	usuario_subclase_archivo			varchar(255)	NULL,
	encriptar_qs						smallint		NULL,
	registrar_solicitud					varchar(1)		NULL,
	registrar_cronometro				varchar(1)		NULL,
	item_inicio_sesion      			varchar(60)		NULL,--NOT
	item_pre_sesion		          		varchar(60)		NULL,--NOT
	item_set_sesion						varchar(60)		NULL,
	log_archivo							smallint		NULL,
	log_archivo_nivel					smallint		NULL,
	fuente_datos						varchar(20)		NULL,--NOT
	pagina_tipo							varchar(20)		NULL,
	version								varchar(20)		NULL,
	version_fecha						date			NULL,
	version_detalle						varchar			NULL,
	version_link						varchar(255)	NULL,
	tiempo_espera_ms					integer			NULL,
	CONSTRAINT	"apex_proyecto_pk" PRIMARY	KEY ("proyecto")
	--CONSTRAINT	"apex_proyecto_item_is" FOREIGN	KEY ("proyecto","item_inicio_sesion") REFERENCES	"apex_item"	("proyecto","item") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE,
	--CONSTRAINT	"apex_proyecto_item_ps" FOREIGN	KEY ("proyecto","item_pre_sesion")	REFERENCES "apex_item" ("proyecto","item") ON DELETE CASCADE ON	UPDATE CASCADE DEFERRABLE INITIALLY	IMMEDIATE,
	--CONSTRAINT	"apex_proyecto_fk_fuente" FOREIGN KEY ("proyecto", "fuente_datos") REFERENCES	"apex_fuente_datos" ("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	--CONSTRAINT	"apex_proyecto_fk_estilo" FOREIGN KEY ("estilo") REFERENCES	"apex_estilo" ("estilo") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
);

--#################################################################################################
