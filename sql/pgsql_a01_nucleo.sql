--**************************************************************************************************
--**************************************************************************************************
--*******************************************	General	*******************************************
--**************************************************************************************************
--**************************************************************************************************


CREATE TABLE			apex_elemento_infra
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: elemento_infra
--: zona: general
--: desc: Representa	un	elemento	de	la	infraestructura
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	elemento_infra				varchar(15)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	CONSTRAINT	"apex_elemento_infra_pk" PRIMARY	KEY ("elemento_infra")
);
--#################################################################################################

CREATE TABLE			apex_elemento_infra_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: elemento_infra, tabla
--: zona: general
--: desc: Representa	una tabla donde se almacena parte del elemento
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	elemento_infra				varchar(15)		NOT NULL,
	tabla						varchar(30)		NOT NULL,
	columna_clave_proyecto		varchar(40)		NOT NULL,
	columna_clave				varchar(80)		NOT NULL,
	orden						smallint		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	dependiente					smallint		NULL,
	proc_borrar					smallint		NULL,
	proc_exportar				smallint		NULL,
	proc_clonar					smallint		NULL,
	obligatoria					smallint		NULL,
	CONSTRAINT	"apex_elem_infra_tabla_pk"	PRIMARY KEY	("elemento_infra","tabla","columna_clave_proyecto","columna_clave"),
	CONSTRAINT	"apex_elem_infra_tabla_fk_e" FOREIGN KEY ("elemento_infra")	REFERENCES "apex_elemento_infra"	("elemento_infra") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_elemento_infra_input_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE			apex_elemento_infra_input
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: entrada
--: zona: general
--: desc: En esta tabla se guardan los elementos toba recibidos desde otras instancias
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	entrada						int4			DEFAULT nextval('"apex_elemento_infra_input_seq"'::text) NOT NULL, 
	elemento_infra				varchar(15)		NOT NULL,
	descripcion					varchar(255)	NULL,
	ip_origen					varchar(40)		NULL,
	ip_destino					varchar(40)		NULL,
	datos						text			NOT NULL,
	datos2_test					text			NOT NULL,
	ingreso						timestamp(0) without	time zone	DEFAULT current_timestamp NOT	NULL,
	CONSTRAINT	"apex_elem_infra_input_pk"	PRIMARY KEY	("entrada"),
	CONSTRAINT	"apex_elem_infra_input_fk_e" FOREIGN KEY ("elemento_infra")	REFERENCES "apex_elemento_infra"	("elemento_infra") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE			apex_estilo_paleta
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: estilo_paleta
--: zona: general
--: desc: Representa	una serie de colores
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	estilo_paleta				varchar(15)		NOT NULL,
	color_1						char(6)			NULL,
	color_2						char(6)			NULL,
	color_3						char(6)			NULL,
	color_4						char(6)			NULL,
	color_5						char(6)			NULL,
	color_6						char(6)			NULL,
	CONSTRAINT	"apex_estilo_paleta_pk"	PRIMARY KEY	("estilo_paleta")
);
--#################################################################################################

CREATE TABLE			apex_estilo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: estilo
--: zona: general
--: desc: Estilos	CSS
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	estilo						varchar(15)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	estilo_paleta_p			varchar(15)		NULL,
	estilo_paleta_s			varchar(15)		NULL,
	estilo_paleta_n			varchar(15)		NULL,
	estilo_paleta_e			varchar(15)		NULL,
	CONSTRAINT	"apex_estilo_pk" PRIMARY KEY ("estilo"),
	CONSTRAINT	"apex_estilo_fk_pal_p" FOREIGN KEY ("estilo_paleta_p") REFERENCES	"apex_estilo_paleta"	("estilo_paleta")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_estilo_fk_pal_s" FOREIGN KEY ("estilo_paleta_s") REFERENCES	"apex_estilo_paleta"	("estilo_paleta")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_estilo_fk_pal_n" FOREIGN KEY ("estilo_paleta_n") REFERENCES	"apex_estilo_paleta"	("estilo_paleta")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_estilo_fk_pal_e" FOREIGN KEY ("estilo_paleta_e") REFERENCES	"apex_estilo_paleta"	("estilo_paleta")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

--#################################################################################################

CREATE TABLE	apex_menu
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: menu
--: zona: general
--: desc: Tipos de menues
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	menu						varchar(15)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	archivo						varchar(255)	NOT NULL,
	soporta_frames				smallint		NULL,
	CONSTRAINT	"apex_menu_pk" PRIMARY	KEY ("menu")
);
--#################################################################################################


CREATE TABLE			apex_proyecto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: proyecto
--: zona: general
--: desc: Tabla maestra	de	proyectos
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto						varchar(15)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	descripcion_corta			varchar(40)		NOT NULL, 
	estilo						varchar(15)		NOT NULL,
	con_frames					smallint		DEFAULT 1,
	frames_clase				varchar(40)		NULL,
	frames_archivo				varchar(255)	NULL,
	menu						varchar(15)		NULL,
	path_includes				varchar(255)	NULL,
	path_browser				varchar(255)	NULL,
	administrador				varchar(60)		NULL,--NOT
	listar_multiproyecto		smallint			NULL,
	orden							float				NULL,
	palabra_vinculo_std		varchar(30)		NULL,
	CONSTRAINT	"apex_proyecto_pk" PRIMARY	KEY ("proyecto"),
	CONSTRAINT	"apex_proyecto_fk_estilo" FOREIGN KEY ("estilo") REFERENCES	"apex_estilo" ("estilo") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_proyecto_fk_menu" FOREIGN KEY ("menu") REFERENCES	"apex_menu" ("menu") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE	
);
--#################################################################################################

CREATE TABLE apex_log_sistema_tipo 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: log_sistema_tipo
--: zona: solicitud
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_sistema_tipo			varchar(20)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	CONSTRAINT	"apex_log_sistema_tipo_pk" PRIMARY KEY ("log_sistema_tipo")
);
--#################################################################################################

CREATE TABLE apex_instancia
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
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

CREATE TABLE apex_fuente_datos_motor
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: fuente_datos_motor
--: zona: general
--: desc: DBMS	soportados
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	fuente_datos_motor			varchar(30)		NOT NULL,
	nombre						varchar(255)	NOT NULL,
	version						varchar(30)		NOT NULL,
	CONSTRAINT	"apex_fuente_datos_motor_pk" PRIMARY KEY ("fuente_datos_motor") 
);
--#################################################################################################

CREATE TABLE apex_fuente_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: fuente_datos
--: zona: general
--: desc: Bases de datos a	las que se puede acceder
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto					varchar(15)		NOT NULL,
	fuente_datos				varchar(20)		NOT NULL,
	fuente_datos_motor			varchar(30)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	descripcion_corta			varchar(40)		NULL,	--	NOT NULL,
	host						varchar(60)		NULL,
	usuario						varchar(30)		NULL,
	clave						varchar(30)		NULL,
	base						varchar(30)		NULL,	--	NOT? ODBC e	instancia no la utilizan...
	administrador				varchar(60)		NULL,
	link_instancia				smallint		NULL,	--	En	vez de abrir una conexion,	utilizar	la	conexion	a la intancia
	instancia_id				varchar(30)	NULL,
	subclase_archivo			varchar(255) 	NULL,
	subclase_nombre				varchar(60) 	NULL,
	orden						smallint		NULL,
	CONSTRAINT	"apex_fuente_datos_pk" PRIMARY KEY ("proyecto","fuente_datos"),
	CONSTRAINT	"apex_fuente_datos_fk_motor" FOREIGN KEY ("fuente_datos_motor") REFERENCES	"apex_fuente_datos_motor" ("fuente_datos_motor") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_fuente_datos_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_grafico
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: grafico
--: zona: general
--: desc: Tipo	de	grafico
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	grafico						varchar(30)			NOT NULL,
	descripcion_corta			varchar(40)			NULL,	--NOT
	descripcion					varchar(255)		NOT NULL,
	parametros					varchar				NULL,
	CONSTRAINT	"apex_tipo_grafico_pk" PRIMARY KEY ("grafico") 
);
--#################################################################################################--

CREATE TABLE apex_recurso_origen
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: recurso_origen 
--: zona: general
--: desc: Origen del	recurso:	apex o proyecto
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	recurso_origen				varchar(10)			NOT NULL,
	descripcion					varchar(255)		NOT NULL,
	CONSTRAINT	"apex_rec_origen_pk"	PRIMARY KEY	("recurso_origen") 
);
--#################################################################################################--

CREATE TABLE apex_repositorio
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: repositorio
--: zona: general
--: desc: Listado	de	repositorios a	los que me puedo conectar
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	repositorio					varchar(80)		NOT NULL,
	descripcion					varchar(255)	NULL,
	CONSTRAINT	"apex_repositorio_pk" PRIMARY	KEY ("repositorio")
);
--#################################################################################################

CREATE TABLE apex_nivel_acceso
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: nivel_acceso
--: zona: general
--: desc: Categoria organizadora	de	niveles de seguridad	(redobla	la	cualificaciond	e elementos	para fortalecer chequeos)
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	nivel_acceso					smallint			NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar			NULL,
	CONSTRAINT	"apex_nivel_acceso_pk" PRIMARY KEY ("nivel_acceso")
);
--#################################################################################################

CREATE TABLE apex_nivel_ejecucion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: nivel_ejecucion
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	nivel_ejecucion			varchar(15)		NOT NULL,
	descripcion					varchar(255)	NOT NULL,
	CONSTRAINT	"apex_nivel_ejecucion_pk"	 PRIMARY	KEY ("nivel_ejecucion")
);
--#################################################################################################

CREATE TABLE apex_solicitud_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: solicitud_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_tipo					varchar(20)		NOT NULL,
	descripcion						varchar(255)	NOT NULL,
	descripcion_corta				varchar(40)		NULL,	--	NOT NULL,
	icono								varchar(30)		NULL,
	CONSTRAINT	"apex_sol_tipo_pk" PRIMARY	KEY ("solicitud_tipo")
);
--#################################################################################################

CREATE TABLE apex_elemento_formulario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: padre, elemento_formulario
--: zona: general
--: desc: Elementos de formulario soportados
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	elemento_formulario				varchar(30)		NOT NULL,
	padre							varchar(30)		NULL,
	descripcion						text			NOT NULL,
	parametros						varchar			NULL,	--	Lista de los parametros	que recibe este EF
	proyecto						varchar(15)		NOT NULL,
	exclusivo_toba					smallint		NULL,
	CONSTRAINT	"apex_elform_pk" PRIMARY KEY ("elemento_formulario"),
	CONSTRAINT	"apex_elform_fk_padre" FOREIGN KEY ("padre") REFERENCES "apex_elemento_formulario"	("elemento_formulario") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_elform_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_solicitud_obs_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_obs_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	solicitud_obs_tipo				varchar(20)		NOT NULL,
	descripcion						varchar(255)	NOT NULL,
	criterio						varchar(20)		NOT NULL,
	CONSTRAINT	"apex_sol_obs_tipo_pk" PRIMARY KEY ("proyecto","solicitud_obs_tipo"),
	CONSTRAINT	"apex_sol_obs_tipo_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_pagina_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: pagina_tipo
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	pagina_tipo							varchar(20)		NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	clase_nombre						varchar(40)		NULL,
	clase_archivo						varchar(255)	NULL,
	include_arriba						varchar(100)	NULL,
	include_abajo						varchar(100)	NULL,
	exclusivo_toba						smallint			NULL,
	contexto								varchar(255)	NULL,	--	Establece variables de CONTEXTO?	Cuales?
	CONSTRAINT	"apex_pagina_tipo_pk" PRIMARY	KEY ("proyecto","pagina_tipo"),
	CONSTRAINT	"apex_pagina_tipo_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_columna_estilo_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;
CREATE TABLE apex_columna_estilo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: columna_estilo
--: zona: general
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	columna_estilo						int4				DEFAULT nextval('"apex_columna_estilo_seq"'::text)	NOT NULL, 
	css									varchar(40)		NOT NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)	  NULL,
	CONSTRAINT	"apex_columna_estilo_pk" PRIMARY	KEY ("columna_estilo") 
);
--###################################################################################################

CREATE SEQUENCE apex_columna_formato_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_columna_formato
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: columna_formato
--: zona: general
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	columna_formato					int4				DEFAULT nextval('"apex_columna_formato_seq"'::text) NOT NULL, 
	funcion								varchar(40)		NOT NULL,
	archivo								varchar(80)		NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)		NULL,
	parametros							varchar(255)	NULL,
	CONSTRAINT	"apex_columna_formato_pk" PRIMARY KEY ("columna_formato") 
);

--###################################################################################################

CREATE SEQUENCE apex_columna_proceso_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_columna_proceso
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: columna_proceso
--: zona: general
--: desc:
--: historica:	0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	columna_proceso					int4				DEFAULT nextval('"apex_columna_proceso_seq"'::text) NOT NULL, 
	funcion								varchar(40)		NOT NULL,
	archivo								varchar(80)		NULL,
	descripcion							varchar(255)	NULL,
	descripcion_corta					varchar(40)		NULL,
	parametros							varchar(255)	NULL,
	CONSTRAINT	"apex_columna_proceso_pk" PRIMARY KEY ("columna_proceso") 
);
--###################################################################################################

CREATE TABLE apex_pdf_propiedad 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: pdf_propiedad
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
  pdf_propiedad 						varchar(30) 	NOT NULL, 
  descripcion   						varchar(255) 	NOT NULL, 
  requerido     						varchar(20)		NULL, 
  proyecto 								varchar(15) 	NOT NULL, 
  exclusiva_columna						smallint 		NULL,
  exclusiva_tabla						smallint 		NULL,
  CONSTRAINT apex_pdfprop_pk PRIMARY KEY (pdf_propiedad), 
  CONSTRAINT apex_pdfprop_fk_proyecto FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto) ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--**************************************************************************************************
--**************************************************************************************************
--*********************************************	 Usuario	 ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_usuario_tipodoc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
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
--: dump: proyecto
--: dump_order_by: usuario
--: zona: usuario
--: desc:
--: instancia:	1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	usuario							varchar(20)		NOT NULL,
	clave							varchar(20)		NOT NULL,
	nombre							varchar(80)		NULL,
	usuario_tipodoc					varchar(10)		NULL,
	pre								varchar(2)		NULL,
	ciu								varchar(18)		NULL,
	suf								varchar(1)		NULL,
	email							varchar(80)		NULL,
	telefono						varchar(18)		NULL,
	vencimiento						date				NULL,
	dias							smallint			NULL,
	hora_entrada					time(0) without time	zone NULL,
	hora_salida						time(0) without time	zone NULL,
	ip_permitida					varchar(20)		NULL,
	solicitud_registrar				smallint			NULL,
	solicitud_obs_tipo_proyecto		varchar(15)		NULL,
	solicitud_obs_tipo				varchar(20)		NULL,
	solicitud_observacion			varchar(255)	NULL,
	parametro_a						varchar(100)	NULL,
	parametro_b						varchar(100)	NULL,
	parametro_c						varchar(100)	NULL,
	CONSTRAINT	"apex_usuario_pk"	 PRIMARY	KEY ("usuario"),
	CONSTRAINT	"apex_usuario_fk_sol_ot" FOREIGN	KEY ("solicitud_obs_tipo_proyecto","solicitud_obs_tipo")	REFERENCES "apex_solicitud_obs_tipo" ("proyecto","solicitud_obs_tipo") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usuario_fk_tipodoc" FOREIGN KEY ("usuario_tipodoc") REFERENCES	"apex_usuario_tipodoc" ("usuario_tipodoc") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_usuario_perfil_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario_perfil_datos
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar			NULL,
	listar							smallint			NULL,
	CONSTRAINT	"apex_usuario_perfil_datos_pk" PRIMARY	KEY ("proyecto","usuario_perfil_datos"),
	CONSTRAINT	"apex_usuario_perfil_da_fk_proy"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_usuario_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario_grupo_acc
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario_grupo_acc				varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	nivel_acceso					smallint			NOT NULL,
	descripcion						varchar			NULL,
	vencimiento						date				NULL,
	dias								smallint			NULL,
	hora_entrada					time(0) without time	zone NULL,
	hora_salida						time(0) without time	zone NULL,
	listar							smallint			NULL,
	CONSTRAINT	"apex_usu_g_acc_pk" PRIMARY KEY ("proyecto","usuario_grupo_acc"),
	CONSTRAINT	"apex_usu_g_acc_fk_niv"	FOREIGN KEY	("nivel_acceso") REFERENCES "apex_nivel_acceso"	("nivel_acceso") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usu_g_acc_fk_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_usuario_proyecto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario
--: zona: usuario
--: instancia:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto							varchar(15)		NOT NULL,
	usuario							varchar(20)		NOT NULL,
	usuario_grupo_acc				varchar(20)		NOT NULL,
	usuario_perfil_datos			varchar(20)		NOT NULL,
	CONSTRAINT	"apex_usu_proy_pk"  PRIMARY KEY ("proyecto","usuario"),
	CONSTRAINT	"apex_usu_proy_fk_usuario"	FOREIGN KEY	("usuario")	REFERENCES "apex_usuario" ("usuario") ON DELETE	CASCADE ON UPDATE	CASCADE DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_usu_proy_fk_proyecto" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usu_proy_fk_grupo_acc" FOREIGN KEY ("proyecto","usuario_grupo_acc") REFERENCES "apex_usuario_grupo_acc" ("proyecto","usuario_grupo_acc") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usu_proy_fk_perf_dat" FOREIGN	KEY ("proyecto","usuario_perfil_datos") REFERENCES	"apex_usuario_perfil_datos" ("proyecto","usuario_perfil_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);

--**************************************************************************************************
--**************************************************************************************************
--******************	  ELEMENTOS	CENTRALES (item, patron, clase y	objeto)	 ************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_patron
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: patron
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto					varchar(15)		NOT NULL,
	patron					varchar(20)		NOT NULL,
	archivo					varchar(80)		NOT NULL,
	descripcion				varchar(250)	NULL,
	descripcion_corta		varchar(40)		NULL,	--	NOT NULL,
	exclusivo_toba			smallint			NULL,
	autodoc					smallint			NULL,
	CONSTRAINT	"apex_patron_pk" PRIMARY KEY ("proyecto","patron"),
	CONSTRAINT	"apex_patron_fk_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_patron_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: patron
--: dump_where: (	patron_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	patron_proyecto					varchar(15)		NOT NULL,
	patron							varchar(20)		NOT NULL,
	descripcion_breve				varchar(255)	NULL,
	descripcion_larga				text			NULL,
	CONSTRAINT	"apex_patron_info_pk" PRIMARY	KEY ("patron_proyecto","patron"),
	CONSTRAINT	"apex_patron_info_fk_patron" FOREIGN KEY ("patron_proyecto","patron") REFERENCES	"apex_patron" ("proyecto","patron")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_buffer_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_buffer
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: buffer
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	buffer							int4			DEFAULT nextval('"apex_buffer_seq"'::text) NOT NULL, 
	descripcion_corta				varchar(40)		NULL,	--	NOT NULL,
	descripcion						varchar(255)	NOT NULL,
	cuerpo							text			NULL,
	archivo_origen					varchar(150)	NULL,
	CONSTRAINT	"apex_buffer_pk" PRIMARY KEY ("proyecto","buffer"),
	CONSTRAINT	"apex_buffer_fk_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

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
	clave_editable					varchar(100)	NULL,	--	Clave	del EDITABLE manejado en la ZONA
	archivo							varchar(80)		NOT NULL, -- Archivo	donde	reside la clase que representa la ZONA
	descripcion						varchar			NULL,
	CONSTRAINT	"apex_item_zona_pk" PRIMARY KEY ("proyecto","zona"),
	CONSTRAINT	"apex_item_zona_fk_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_item_seq	INCREMENT 1	MINVALUE	1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
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
	nivel_acceso					smallint		NOT NULL,
	solicitud_tipo					varchar(20)		NOT NULL,
	pagina_tipo_proyecto			varchar(15)		NOT NULL,
	pagina_tipo						varchar(20)		NOT NULL,
	nombre							varchar(80)		NOT NULL,
	descripcion						varchar(255)	NULL,
	actividad_buffer_proyecto		varchar(15)		NOT NULL,
	actividad_buffer				int4			NOT NULL,
	actividad_patron_proyecto		varchar(15)		NOT NULL,
	actividad_patron				varchar(20)		NOT NULL,
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
	parametro_a						varchar(100)	NULL,
	parametro_b						varchar(100)	NULL,
	parametro_c						varchar(100)	NULL,
	publico							smallint		NULL,
	usuario							varchar(20)		NULL,
	creacion						timestamp(0)	without time zone	DEFAULT current_timestamp NULL,
	CONSTRAINT	"apex_item_pk"	PRIMARY KEY	("proyecto","item"),
	CONSTRAINT	"apex_item_uq_path" UNIQUE	("proyecto","item"),
	CONSTRAINT	"apex_item_fk_proyecto"	FOREIGN KEY	("proyecto") REFERENCES	"apex_proyecto" ("proyecto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
--	Como el DUMP devuelve a	los registros desordenadors este	constraint hay	que definirlo al final
--	CONSTRAINT	"apex_item_fk_padre"	FOREIGN KEY	("padre_proyecto","padre")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_item_fk_buffer" FOREIGN	KEY ("actividad_buffer_proyecto","actividad_buffer") REFERENCES "apex_buffer"	("proyecto","buffer") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_item_fk_patron" FOREIGN	KEY ("actividad_patron_proyecto","actividad_patron") REFERENCES "apex_patron"	("proyecto","patron") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
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
--: dump: multiproyecto
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
	CONSTRAINT	"apex_item_info_fk_item" FOREIGN	KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_clase_tipo_seq	INCREMENT 1	MINVALUE	1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apex_clase_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: clase_tipo
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	clase_tipo						int4				DEFAULT nextval('"apex_clase_tipo_seq"'::text) NOT	NULL,	
	descripcion_corta				varchar(40)			NOT NULL,
	descripcion						varchar(255)		NULL,
	icono							varchar(30)			NULL,
	orden							float				NULL,
	metodologia						varchar(10)			NULL, --NOT
	CONSTRAINT	"apex_clase_tipo_pk"	 PRIMARY	KEY ("clase_tipo")
);
--#################################################################################################

CREATE TABLE apex_clase
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	proyecto						varchar(15)		NOT NULL,
	clase							varchar(60)		NOT NULL,
	clase_tipo						int4			NOT NULL, 
	archivo							varchar(80)		NOT NULL,
	descripcion						varchar(250)	NOT NULL,
	descripcion_corta				varchar(40)		NULL,	--	NOT NULL, 
	icono							varchar(60)		NOT NULL, --> Icono con	el	que los objetos de la clase aparecen representados	en	las listas
	screenshot						varchar(60)		NULL,	--> Path a una imagen de la clase
	ancestro_proyecto				varchar(15)		NULL,	--> Ancestro a	considerar para incluir	dependencias
	ancestro						varchar(60)		NULL,
	instanciador_id					int4			NULL,	
	instanciador_proyecto			varchar(15)		NULL,
	instanciador_item				varchar(60)		NULL,	--> Item	del catalogo a	invocar como instanciador de objetos de esta	clase
	editor_id						int4			NULL,	
	editor_proyecto					varchar(15)		NULL,
	editor_item						varchar(60)		NULL,	--> Item	del catalogo a	invocar como editor de objetos de esta	clase
	editor_ancestro_proyecto		varchar(15)		NULL,	--> Ancestro a	considerar para el EDITOR
	editor_ancestro					varchar(60)		NULL,
	plan_dump_objeto				varchar(255)	NULL, --> Lista ordenada de tablas	que poseen la definicion del objeto	(respetar FK!)
	sql_info						text			NULL, --> SQL	que DUMPEA el estado	del objeto
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
	CONSTRAINT	"apex_clase_uq" UNIQUE	("clase"),
	CONSTRAINT	"apex_clase_fk_proyecto" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_clase_fk_tipo"	FOREIGN KEY	("clase_tipo")	REFERENCES "apex_clase_tipo" ("clase_tipo") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_clase_fk_editor_anc"	FOREIGN KEY	("editor_ancestro_proyecto","editor_ancestro") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
--	CONSTRAINT	"apex_clase_fk_ancestro" FOREIGN	KEY ("ancestro_proyecto","ancestro") REFERENCES	"apex_clase" ("proyecto","clase") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_clase_fk_editor" FOREIGN KEY ("editor_proyecto","editor_item")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_clase_fk_instan" FOREIGN KEY ("instanciador_proyecto","instanciador_item")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_clase_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase
--: dump_where: (	clase_proyecto	= '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	clase_proyecto					varchar(15)		NOT NULL,
	clase							varchar(60)		NOT NULL,
	descripcion_breve				varchar(255)	NULL,
	descripcion_larga				text			NULL,
	CONSTRAINT	"apex_clase_info_pk"	 PRIMARY	KEY ("clase_proyecto","clase"),
	CONSTRAINT	"apex_clase_info_fk_clase"	FOREIGN KEY	("clase_proyecto","clase")	REFERENCES "apex_clase"	("proyecto","clase")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_clase_dependencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: clase_consumidora, identificador
--: dump_where: (	clase_consumidora_proyecto	= '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	clase_consumidora_proyecto	varchar(15)			NOT NULL,
	clase_consumidora				varchar(60)		NOT NULL,
	identificador					varchar(20)		NOT NULL,
	descripcion						varchar(250)	NULL,	  
	clase_proveedora_proyecto	varchar(15)			NOT NULL,	--	Las dependencias pueden	ser de esta	clase	o de una	heredada
	clase_proveedora				varchar(60)		NOT NULL,
	CONSTRAINT	"apex_clase_depen_pk" PRIMARY	KEY ("clase_consumidora_proyecto","clase_consumidora","identificador"),
	CONSTRAINT	"apex_clase_depen_fk_clase_c"	FOREIGN KEY	("clase_consumidora_proyecto","clase_consumidora")	REFERENCES "apex_clase"	("proyecto","clase")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_clase_depen_fk_clase_p"	FOREIGN KEY	("clase_proveedora_proyecto","clase_proveedora") REFERENCES	"apex_clase" ("proyecto","clase") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_patron_dependencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: patron, clase
--: dump_where: (	patron_proyecto =	'%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(	
	patron_proyecto					varchar(15)		NOT NULL,
	patron							varchar(20)		NOT NULL,
	clase_proyecto					varchar(15)		NOT NULL,
	clase							varchar(60)		NOT NULL,
	cantidad_minima					smallint		NOT NULL,
	cantidad_maxima					smallint		NOT NULL,
	descripcion						varchar(250)	NULL,
	CONSTRAINT	"apex_patron_depen_pk"	 PRIMARY	KEY ("patron_proyecto","patron","clase_proyecto","clase"),
	CONSTRAINT	"apex_patron_depen_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_patron_depen_fk_patron"	FOREIGN KEY	("patron_proyecto","patron") REFERENCES "apex_patron"	("proyecto","patron") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################--

CREATE TABLE apex_objeto_categoria
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_categoria
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL,
	objeto_categoria				varchar(30)		NOT NULL,
	descripcion						varchar(255)	NULL,
	CONSTRAINT	"apex_obj_categoria_pk"	PRIMARY KEY	("proyecto","objeto_categoria"),
	CONSTRAINT	"apex_obj_categoria_fk_proy" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_solicitud_obj_obs_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: solicitud_obj_obs_tipo
--: dump_where: (	clase_proyecto	= '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_obj_obs_tipo				varchar(20)		NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	clase_proyecto						varchar(15)		NULL,
	clase								varchar(60)		NULL,
	CONSTRAINT	"apex_sol_obj_obs_tipo_pk"	PRIMARY KEY	("solicitud_obj_obs_tipo"),
	CONSTRAINT	"apex_sol_obj_obs_tipo_fk_clase"	FOREIGN KEY	("clase_proyecto","clase")	REFERENCES "apex_clase"	("proyecto","clase")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE SEQUENCE apex_objeto_seq INCREMENT	1 MINVALUE 1 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
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
	nombre								varchar(80)		NOT NULL,
	titulo								varchar(80)		NULL,
	colapsable							smallint		NULL,
	descripcion							varchar(255)	NULL,
	fuente_datos_proyecto				varchar(15)		NOT NULL,
	fuente_datos						varchar(20)		NOT NULL,
	solicitud_registrar					smallint		NULL,
	solicitud_obj_obs_tipo				varchar(20)		NULL,
	solicitud_obj_observacion			varchar(255)	NULL,
	parametro_a							varchar(100)	NULL,
	parametro_b							varchar(100)	NULL,
	parametro_c							varchar(100)	NULL,
	parametro_d							varchar(100)	NULL,
	parametro_e							varchar(100)	NULL,
	parametro_f							varchar(100)	NULL,
	usuario								varchar(20)		NULL,
	creacion							timestamp(0)	without time zone	DEFAULT current_timestamp NULL,
	CONSTRAINT	"apex_objeto_pk"	 PRIMARY	KEY ("proyecto","objeto"),
	CONSTRAINT	"apex_objeto_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_fuente_datos"	FOREIGN KEY	("fuente_datos_proyecto","fuente_datos") REFERENCES "apex_fuente_datos"	("proyecto","fuente_datos") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_solic_ot" FOREIGN KEY ("solicitud_obj_obs_tipo") REFERENCES "apex_solicitud_obj_obs_tipo" ("solicitud_obj_obs_tipo") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_proyecto" FOREIGN KEY ("proyecto")	REFERENCES "apex_proyecto"	("proyecto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
--  CONSTRAINT  "apex_objeto_fk_usuario"	FOREIGN KEY	("usuario")	REFERENCES "apex_usuario" ("usuario") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_categ" FOREIGN KEY ("objeto_categoria_proyecto","objeto_categoria")	REFERENCES "apex_objeto_categoria" ("proyecto","objeto_categoria") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_info
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
	objeto_proyecto						varchar(15)			NOT NULL,
	objeto								int4				NOT NULL,
	descripcion_breve					varchar(255)		NULL,
	descripcion_larga					text				NULL,
	CONSTRAINT	"apex_objeto_info_pk" PRIMARY	KEY ("objeto_proyecto","objeto"),
	CONSTRAINT	"apex_objeto_info_fk_objeto" FOREIGN KEY ("objeto_proyecto","objeto") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_dependencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_consumidor, identificador
--: dump_where:
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)			NOT NULL,
	objeto_consumidor					int4				NOT NULL,
	objeto_proveedor					int4				NOT NULL,
	identificador						varchar(20)			NOT NULL,
	parametros_a						varchar(255)		NULL,
	parametros_b						varchar(255)		NULL,
	parametros_c						varchar(255)		NULL,
	inicializar							smallint			NULL,
	CONSTRAINT	"apex_objeto_depen_pk"	 PRIMARY	KEY ("proyecto","objeto_consumidor","identificador"),
	CONSTRAINT	"apex_objeto_depen_fk_objeto_c" FOREIGN KEY ("proyecto","objeto_consumidor") REFERENCES "apex_objeto"	("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_depen_fk_objeto_p" FOREIGN KEY ("proyecto","objeto_proveedor") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE CASCADE ON UPDATE NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_objeto_eventos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto, orden, identificador
--: dump_where:
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)			NOT NULL,
	objeto								int4				NOT NULL,
	identificador						varchar(20)			NOT NULL,
	etiqueta							varchar(60)			NULL,
	maneja_datos						smallint			NULL DEFAULT 1,
	sobre_fila							smallint			NULL,
	confirmacion						varchar(60)			NULL,
	estilo								varchar(40)			NULL,
	imagen_recurso_origen				varchar(10)			NULL,
	imagen								varchar(60)			NULL,
	en_botonera							smallint			NULL,
	ayuda								varchar(255)		NULL,
	orden								smallint			NULL,
	CONSTRAINT	"apex_objeto_eventos_pk" PRIMARY KEY ("proyecto","objeto","identificador"),
	CONSTRAINT	"apex_objeto_eventos_fk_rec_orig" FOREIGN KEY ("imagen_recurso_origen") REFERENCES "apex_recurso_origen" ("recurso_origen")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_eventos_fk_objeto" FOREIGN KEY ("proyecto","objeto") REFERENCES "apex_objeto"	("proyecto","objeto") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_item_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
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
	CONSTRAINT	"apex_item_consumo_obj_fk_item" FOREIGN KEY ("proyecto","item") REFERENCES	"apex_item"	("proyecto","item") ON DELETE CASCADE ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_item_consumo_obj_fk_objeto" FOREIGN	KEY ("proyecto","objeto") REFERENCES "apex_objeto"	("proyecto","objeto") ON DELETE CASCADE	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_vinculo_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: vinculo_tipo
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	vinculo_tipo						varchar(10)		NOT NULL,
	descripcion_corta					varchar(40)		NULL,	--	NOT NULL,
	descripcion							varchar(255)	NOT NULL,
	CONSTRAINT	"apex_vinculo_tipo_pk" PRIMARY KEY ("vinculo_tipo")
);
--#################################################################################################--

CREATE TABLE apex_vinculo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: origen_item, origen_objeto, destino_item, destino_objeto
--: dump_where: (	origen_item_proyecto	= '%%' )	
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	origen_item_id						int4				NULL,	
	origen_item_proyecto				varchar(15)		NOT NULL,
	origen_item							varchar(60)		NOT NULL,
	origen_objeto_proyecto			varchar(15)		NOT NULL,
	origen_objeto						int4				NOT NULL,
	destino_item_id					int4				NULL,	
	destino_item_proyecto			varchar(15)		NOT NULL,
	destino_item						varchar(60)		NOT NULL,
	destino_objeto_proyecto			varchar(15)		NOT NULL,	--	Objeto que tiene que	recibir el valor
	destino_objeto						int4				NOT NULL,	--	
	frame									varchar(60)		NULL,
	canal									varchar(40)		NULL,			--	Clave	utilizada para	expandir	el	valor
	indice								varchar(20)		NOT NULL,	--	Indice para	que el consumidor	recupere	el	vinculo
	vinculo_tipo						varchar(10)		NOT NULL,	--	Como se habre el vinculo? popup,	zoom,	etc
	inicializacion						varchar(100)	NULL,			--	En	el	caso de un POPUP,	tamao, etc.
	operacion							smallint			NULL,			--	flag que	indica si el vinculo	implica una	propagacion	de	la	operacion o	no	(util	para determinar permisos en cascada)
	texto									varchar(60)		NULL,			--	Texto	del LINK
	imagen_recurso_origen			varchar(10)		NULL,			--	Lugar	donde	se	guardo la imagen:	toba o proyecto
	imagen								varchar(60)		NULL,			--	path a la imagen
	CONSTRAINT	"apex_vinc_pk"	PRIMARY KEY	("origen_item_proyecto","origen_item","origen_objeto_proyecto","origen_objeto","destino_item_proyecto","destino_item","destino_objeto_proyecto","destino_objeto"),
--	  CONSTRAINT  "apex_vinc_pk" UNIQUE	KEY ("origen_item_proyecto","origen_item","origen_objeto_proyecto","origen_objeto","destino_item_proyecto","destino_item","destino_objeto_proyecto","destino_objeto","indice"),
	CONSTRAINT	"apex_vinc_fk_item_o" FOREIGN	KEY ("origen_item_proyecto","origen_item") REFERENCES	"apex_item"	("proyecto","item") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_vinc_fk_obj_o"	FOREIGN KEY	("origen_objeto_proyecto","origen_objeto") REFERENCES	"apex_objeto" ("proyecto","objeto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_vinc_fk_item_d" FOREIGN	KEY ("destino_item_proyecto","destino_item")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_vinc_fk_obj_d"	FOREIGN KEY	("destino_objeto_proyecto","destino_objeto")	REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE	NO	ACTION ON UPDATE NO ACTION	DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_vinc_fk_rec_orig"	FOREIGN KEY	("imagen_recurso_origen") REFERENCES "apex_recurso_origen" ("recurso_origen")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_vinc_fk_tipo" FOREIGN KEY ("vinculo_tipo") REFERENCES	"apex_vinculo_tipo" ("vinculo_tipo") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_usuario_grupo_acc_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario_grupo_acc, item
--: dump_where:
--: zona: usuario, item
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto								varchar(15)		NOT NULL,
	usuario_grupo_acc					varchar(20)		NOT NULL,
	item_id								int4				NULL,	
	item									varchar(60)		NOT NULL,
	CONSTRAINT	"apex_usu_item_pk" PRIMARY	KEY ("proyecto","usuario_grupo_acc","item"),
	CONSTRAINT	"apex_usu_item_fk_item"	FOREIGN KEY	("proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_usu_item_fk_us_gru_acc"	FOREIGN KEY	("proyecto","usuario_grupo_acc")	REFERENCES "apex_usuario_grupo_acc"	("proyecto","usuario_grupo_acc")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
  
--#################################################################################################

CREATE TABLE apex_arbol_items_fotos

---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario, foto_nombre
--: dump_where:
--: zona: usuario
--: instancia:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(20)		NOT NULL,
	foto_nombre							varchar(100)	NOT NULL,
	foto_nodos_visibles					varchar			NULL,
	foto_opciones						varchar			NULL,
  CONSTRAINT "apex_arbol_items_fotos_pk" PRIMARY KEY("proyecto", "usuario", "foto_nombre"),
  CONSTRAINT "apex_arbol_items_fotos_fk_proy" 	FOREIGN KEY ("proyecto", "usuario")
    											REFERENCES "apex_usuario_proyecto" ("proyecto", "usuario") ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);

--#################################################################################################

CREATE TABLE apex_admin_album_fotos

---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario, foto_tipo, foto_nombre
--: dump_where:
--: zona: usuario
--: instancia:	1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto							varchar(15)		NOT NULL, 
	usuario								varchar(20)		NOT NULL,
	foto_tipo							varchar(20)		NOT NULL,	--cat_item u cat_objeto
	foto_nombre							varchar(100)	NOT NULL,
	foto_nodos_visibles					varchar			NULL,
	foto_opciones						varchar			NULL,
	predeterminada							smallint	NULL,
  CONSTRAINT "apex_admin_album_fotos_pk" PRIMARY KEY("proyecto", "usuario", "foto_nombre", "foto_tipo"),
  CONSTRAINT "apex_admin_album_fotos_fk_proy" 	FOREIGN KEY ("proyecto", "usuario")
    											REFERENCES "apex_usuario_proyecto" ("proyecto", "usuario") ON	DELETE CASCADE ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);

 
--**************************************************************************************************
--**************************************************************************************************
--********************************	 DOCUMENTACION	del NUCLEO	 ************************************
--**************************************************************************************************
--**************************************************************************************************


CREATE SEQUENCE apex_nucleo_tipo_seq INCREMENT 1 MINVALUE 1	MAXVALUE	9223372036854775807 CACHE 1;
CREATE TABLE apex_nucleo_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: nucleo_tipo
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	nucleo_tipo							int4				DEFAULT nextval('"apex_nucleo_tipo_seq"'::text)	NOT NULL, 
	descripcion_corta					varchar(40)		NOT NULL,
	descripcion							varchar(250)	NOT NULL,
	orden									float				NULL,
	CONSTRAINT	"apex_nucleo_tipo_pk"	PRIMARY KEY	("nucleo_tipo")
);
--#################################################################################################

CREATE TABLE apex_nucleo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nucleo
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	proyecto								varchar(15)		NOT NULL,
	nucleo								varchar(60)		NOT NULL,
	nucleo_tipo							varchar(15)		NOT NULL,
	archivo								varchar(80)		NOT NULL,
	descripcion							varchar(250)	NOT NULL,
	descripcion_corta					varchar(40)		NULL,	--	NOT NULL,
	doc_nucleo							varchar(255)	NULL,			--> GIF donde hay	un	Diagrama
	doc_db								varchar(60)		NULL,			--> GIF donde hay	un	DER de las tablas	que necesita la nucleo.
	doc_sql								varchar(60)		NULL,			--> path	al	archivo que	crea las	tablas.
	autodoc								smallint			NULL,
	orden									float				NULL,
	CONSTRAINT	"apex_nucleo_pk"	 PRIMARY	KEY ("proyecto","nucleo"),
	CONSTRAINT	"apex_nucleo_fk_proy" FOREIGN	KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_nucleo_fk_tipo" FOREIGN	KEY ("nucleo_tipo") REFERENCES "apex_nucleo_tipo" ("nucleo_tipo")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################

CREATE TABLE apex_nucleo_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: nucleo
--: dump_where: (	nucleo_proyecto =	'%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	nucleo_proyecto					varchar(15)		NOT NULL,
	nucleo								varchar(60)		NOT NULL,
	descripcion_breve					varchar(255)	NULL,
	descripcion_larga					text				NULL,
	CONSTRAINT	"apex_nucleo_info_pk" PRIMARY	KEY ("nucleo_proyecto","nucleo"),
	CONSTRAINT	"apex_nucleo_info_fk_nucleo" FOREIGN KEY ("nucleo_proyecto","nucleo") REFERENCES	"apex_nucleo" ("proyecto","nucleo")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--#################################################################################################
