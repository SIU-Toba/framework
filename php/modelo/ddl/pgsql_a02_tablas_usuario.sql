--**************************************************************************************************
--**************************************************************************************************
--*********************************************	 Usuario	 ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_usuario_tipodoc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: usuario_tipodoc
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	usuario_tipodoc				varchar(10)		NOT NULL,
	descripcion						varchar(40)		NOT NULL,
	CONSTRAINT	"apex_usuario_tipodoc_pk"	 PRIMARY	KEY ("usuario_tipodoc")
);
--#################################################################################################

CREATE TABLE apex_usuario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: usuario
--: zona: usuario
--: desc:
--: instancia:	1
--: usuario:	1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	usuario							varchar(60)		NOT NULL,
	clave							varchar(128)	NOT NULL,
	nombre							TEXT	NULL,
	email							TEXT		NULL,
	autentificacion					varchar(10)		NULL DEFAULT 'plano',
	bloqueado						smallint		DEFAULT 0 NULL,
	parametro_a						TEXT	NULL,
	parametro_b						TEXT	NULL,
	parametro_c						TEXT	NULL,
----
--  Campos no soportados por nucleo y editor (se mantienen por razones historicas)
----
	solicitud_registrar				smallint			NULL,
	solicitud_obs_tipo_proyecto		varchar(15)		NULL,
	solicitud_obs_tipo				varchar(20)		NULL,
	solicitud_observacion			TEXT	NULL,
	usuario_tipodoc					varchar(10)		NULL,
	pre								varchar(2)		NULL,
	ciu								varchar(18)		NULL,
	suf								varchar(1)		NULL,
	telefono						varchar(30)		NULL,
	vencimiento						date				NULL,
	dias							smallint			NULL,
	hora_entrada					time(0) without time	zone NULL,
	hora_salida						time(0) without time	zone NULL,
	ip_permitida					varchar(20)		NULL,
	forzar_cambio_pwd				SMALLINT		NOT NULL DEFAULT 0,
	CONSTRAINT	"apex_usuario_pk"	 PRIMARY	KEY ("usuario"),
	--CONSTRAINT	"apex_usuario_fk_sol_ot" FOREIGN	KEY ("solicitud_obs_tipo_proyecto","solicitud_obs_tipo")	REFERENCES "apex_solicitud_obs_tipo" ("proyecto","solicitud_obs_tipo") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usuario_fk_tipodoc" FOREIGN KEY ("usuario_tipodoc") REFERENCES	"apex_usuario_tipodoc" ("usuario_tipodoc") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_usuario_pwd_reset
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: no_requerido
--: dump_order_by: usuario
--: zona: usuario
--: desc:
--: historica:	1
--: usuario:	1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	usuario							varchar(60)		NOT NULL,
	random							varchar(128)	NOT NULL,
	email								TEXT		NULL,
	validez								timestamp NOT NULL DEFAULT now(),
	bloqueado						smallint		NOT NULL DEFAULT 0,
	CONSTRAINT	"apex_usuario_pwd_pk"	 PRIMARY	KEY ("usuario", "random")		---Quizas no deberia existir.. quien sabe
);

--#################################################################################################
CREATE SEQUENCE apex_usuario_pregunta_secreta_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;
CREATE TABLE apex_usuario_pregunta_secreta

---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: usuario
--: zona: usuario
--: desc:
--: instancia:	1
--: usuario:	1
--: version: 2.0
---------------------------------------------------------------------------------------------------
(
	cod_pregunta_secreta		int8	DEFAULT nextval('"apex_usuario_pregunta_secreta_seq"'::text)	NOT NULL,
	usuario					varchar(60) NOT NULL, 
	pregunta					text NOT NULL, 
	respuesta				text NOT NULL, 
	activa					smallint NOT NULL DEFAULT 1, 
	CONSTRAINT apex_usuario_pregunta_secreta_pk PRIMARY KEY (cod_pregunta_secreta), 
	CONSTRAINT apex_usuario_pregunta_secreta_fk_usuario FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario) ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE
) ;

--#################################################################################################
CREATE SEQUENCE apex_usuario_pwd_usados_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;
CREATE TABLE apex_usuario_pwd_usados

---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: nucleo
--: dump_order_by: usuario
--: zona: usuario
--: desc:
--: instancia:	1
--: usuario:	1
--: version: 2.0
---------------------------------------------------------------------------------------------------
(
	cod_pwd_pasados		int8 DEFAULT nextval('"apex_usuario_pwd_usados_seq"'::text) NOT NULL, 
	usuario		VARCHAR(60)		NOT NULL, 
	clave		VARCHAR(128)	NOT NULL, 
	algoritmo		VARCHAR(10)		NOT NULL,
	fecha_cambio		DATE		NOT NULL DEFAULT ('now'::text)::date,
	CONSTRAINT	apex_usuario_pwd_usados_pk PRIMARY KEY (cod_pwd_pasados), 
	CONSTRAINT	apex_usuario_pwd_usados_fk_usuario FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario) ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE,
	CONSTRAINT apex_usuario_pwd_usados_uk UNIQUE (usuario, clave)
);