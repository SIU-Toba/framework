--##################################################################################################
--##################################################################################################
--###########################################   General  ###########################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_proyecto
(
   proyecto                varchar(15)    NOT NULL,
   descripcion             varchar(255)   NOT NULL,
   path_includes           varchar(255)   NOT NULL,
   path_browser            varchar(255)   NOT NULL,
   item_inicio             varchar(60)    NOT NULL,
   administrador           varchar(60)    NULL,--NOT
   orden                   smallint       NULL,
   CONSTRAINT  "apl_proyecto_pk" PRIMARY KEY ("proyecto")
-- CONSTRAINT  "apl_proyecto_fk_item" FOREIGN KEY ("item_inicio") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_instancia
(
   instancia               varchar(80)    NOT NULL,
   proyecto_activo         varchar(15)    NOT NULL,
   version                 varchar(15)    NOT NULL,
   institucion             varchar(255)   NULL,
   observaciones           varchar(255)   NULL,
   administrador_1         varchar(60)    NULL,--NOT
   administrador_2         varchar(60)    NULL,--NOT
   administrador_3         varchar(60)    NULL,--NOT
   creacion                timestamp(0) without time zone   DEFAULT current_timestamp NOT NULL,
   CONSTRAINT  "apl_instancia_pk"   PRIMARY KEY ("instancia"),
   CONSTRAINT  "apl_instancia_fk_proyecto" FOREIGN KEY ("proyecto_activo") REFERENCES "apl_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_fuente_datos_motor
(
   fuente_datos_motor      varchar(30)       NOT NULL,
   nombre                  varchar(255)      NOT NULL,
   version                 varchar(30)       NOT NULL,
   CONSTRAINT  "apl_fuente_datos_motor_pk" PRIMARY KEY ("fuente_datos_motor") 
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_fuente_datos
(
   fuente_datos            varchar(20)    NOT NULL,
   descripcion             varchar(255)   NOT NULL,
   fuente_datos_motor      varchar(30)    NOT NULL,
   host                    varchar(60)    NULL,
   usuario                 varchar(30)    NULL,
   clave                   varchar(30)    NULL,
   base                    varchar(30)    NOT NULL,
   administrador           varchar(60)    NULL,
   proyecto                varchar(15)    NOT NULL,
   link_apl                smallint       NULL,
   orden                   smallint       NULL,
   CONSTRAINT  "apl_fuente_datos_pk" PRIMARY KEY ("fuente_datos"),
   CONSTRAINT  "apl_fuente_datos_fk_motor" FOREIGN KEY ("fuente_datos_motor") REFERENCES "apl_fuente_datos_motor" ("fuente_datos_motor") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_fuente_datos_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apl_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_grafico
(
   grafico                 varchar(30)       NOT NULL,
   descripcion             varchar(255)      NOT NULL,
   CONSTRAINT  "apl_tipo_grafico_pk" PRIMARY KEY ("grafico") 
);
-----------------------------------------------------------------------------------------------------

CREATE TABLE apl_repositorio
(
   repositorio             varchar(80)    NOT NULL,
   descripcion             varchar(255)   NULL,
   CONSTRAINT  "apl_repositorio_pk" PRIMARY KEY ("repositorio")
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_nivel_acceso
(
   nivel_acceso               smallint       NOT NULL,
   nombre                     varchar(80)    NOT NULL,
   descripcion                varchar        NULL,
   CONSTRAINT  "apl_nivel_acceso_pk" PRIMARY KEY ("nivel_acceso")
);
---------------------

CREATE TABLE apl_nivel_ejecucion
(
   nivel_ejecucion         varchar(15)    NOT NULL,
   descripcion             varchar(255)   NOT NULL,
   CONSTRAINT  "apl_nivel_ejecucion_pk"   PRIMARY KEY ("nivel_ejecucion")
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_solicitud_tipo
(
   solicitud_tipo             varchar(20)    NOT NULL,
   descripcion                varchar(255)   NOT NULL,
   icono                      varchar(30)    NULL,
   CONSTRAINT  "apl_sol_tipo_pk" PRIMARY KEY ("solicitud_tipo")
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_elemento_formulario
(
   elemento_formulario        varchar(30)    NOT NULL,
   descripcion                varchar(255)   NOT NULL,
   CONSTRAINT  "apl_elform_pk" PRIMARY KEY ("elemento_formulario")
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_solicitud_obs_tipo
(
   solicitud_obs_tipo         varchar(20)    NOT NULL,
   descripcion                varchar(255)   NOT NULL,
   criterio                   varchar(20)    NOT NULL,
   proyecto                   varchar(15)       NOT NULL,
   CONSTRAINT  "apl_sol_obs_tipo_pk" PRIMARY KEY ("solicitud_obs_tipo"),
   CONSTRAINT  "apl_sol_obs_tipo_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apl_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_nota_tipo
(
   nota_tipo                  varchar(20)    NOT NULL,
   descripcion                varchar(255)   NOT NULL,
   icono                      varchar(30)    NULL,
   CONSTRAINT  "apl_nota_tipo_pk" PRIMARY KEY ("nota_tipo")
);

---------------------------------------------------------------------------------------------------

CREATE TABLE apl_pagina_tipo
(
   pagina_tipo                  varchar(20)    NOT NULL,
   descripcion                  varchar(255)   NOT NULL,
   include_arriba               varchar(100)   NULL,
   include_abajo                varchar(100)   NULL,
   contexto                     varchar(255)   NULL, -- Establece variables de CONTEXTO?
   CONSTRAINT  "apl_pagina_tipo_pk" PRIMARY KEY ("pagina_tipo")
);


--##################################################################################################
--##################################################################################################
--#############################################  Usuario  ##########################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_usuario_tipodoc
(
   usuario_tipodoc   varchar(10) NOT NULL,
   descripcion                varchar(40) NOT NULL,
   CONSTRAINT  "apl_usuario_tipodoc_pk"   PRIMARY KEY ("usuario_tipodoc")
);
---------------------

CREATE TABLE apl_usuario_perfil_datos
(
   usuario_perfil_datos       varchar(20)    NOT NULL,
   nombre                     varchar(80)    NOT NULL,
   descripcion                varchar        NULL,
   CONSTRAINT  "apl_usuario_perfil_datos_pk" PRIMARY KEY ("usuario_perfil_datos")
);
---------------------

CREATE TABLE apl_usuario_grupo_acc
(
   usuario_grupo_acc          varchar(20)    NOT NULL,
   nombre                     varchar(80)    NOT NULL,
   descripcion                varchar        NULL,
   nivel_acceso               smallint       NULL,
   vencimiento                date           NULL,
   dias                       smallint       NULL,
   hora_entrada               time(0) without time zone NULL,
   hora_salida                time(0) without time zone NULL,
   CONSTRAINT  "apl_usu_g_acc_fk_niv" FOREIGN KEY ("nivel_acceso") REFERENCES "apl_nivel_acceso" ("nivel_acceso") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_usu_g_acc_pk" PRIMARY KEY ("usuario_grupo_acc")
);
---------------------

CREATE TABLE apl_usuario
(
   usuario                    varchar(20)    NOT NULL,
   clave                      varchar(20)    NOT NULL,
   usuario_grupo_acc          varchar(20)    NOT NULL,
   usuario_perfil_datos        varchar(20)    NOT NULL,
   nombre                     varchar(80)    NULL,
   usuario_tipodoc            varchar(10)    NULL,
   pre                        varchar(2)     NULL,
   ciu                        varchar(18)    NULL,
   suf                        varchar(1)     NULL,
   email                      varchar(80)    NULL,
   telefono                   varchar(18)    NULL,
   vencimiento                date           NULL,
   dias                       smallint       NULL,
   hora_entrada               time(0) without time zone NULL,
   hora_salida                time(0) without time zone NULL,
   ip_permitida               varchar(20)    NULL,
   solicitud_registrar        smallint       NULL,
   solicitud_obs_tipo         varchar(20)    NULL,
   solicitud_observacion      varchar(255)   NULL,
   CONSTRAINT  "apl_usuario_pk"  PRIMARY KEY ("usuario"),
   CONSTRAINT  "apl_usuario_uk"  UNIQUE ("usuario_tipodoc","pre","ciu","suf"),
   CONSTRAINT  "apl_usuario_fk_grupo_acc" FOREIGN KEY ("usuario_grupo_acc") REFERENCES "apl_usuario_grupo_acc" ("usuario_grupo_acc") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_usuario_fk_perf_dat" FOREIGN KEY ("usuario_perfil_datos") REFERENCES "apl_usuario_perfil_datos" ("usuario_perfil_datos") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_usuario_fk_sol_ot" FOREIGN KEY ("solicitud_obs_tipo") REFERENCES "apl_solicitud_obs_tipo" ("solicitud_obs_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_usuario_fk_tipodoc" FOREIGN KEY ("usuario_tipodoc") REFERENCES "apl_usuario_tipodoc" ("usuario_tipodoc") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);

---------------------

CREATE TABLE apl_usuario_proyecto
(
   usuario                 varchar(20) NOT NULL,
   proyecto                varchar(15)    NOT NULL,
   CONSTRAINT  "apl_item_fk_proyecto_pk"  PRIMARY KEY ("usuario","proyecto"),
   CONSTRAINT  "apl_item_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apl_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_proyecto_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apl_usuario" ("usuario") ON DELETE CASCADE ON UPDATE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
-- CONSTRAINT  "apl_item_fk_proyecto_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);

--##################################################################################################
--##################################################################################################
--##################   ELEMENTOS CENTRALES (item, patron, clase y objeto)   ########################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_patron
(
   patron               varchar(20)    NOT NULL,
   archivo              varchar(80)    NOT NULL,
   descripcion          varchar(250)   NULL,
   auto_html            smallint       NULL,
   CONSTRAINT  "apl_patron_pk" PRIMARY KEY ("patron")
);
---------------------------------------------------------------------------------------------------

CREATE SEQUENCE apl_patron_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apl_patron_nota
(
   patron_nota             int4           DEFAULT nextval('"apl_patron_nota_seq"'::text) NOT NULL, 
   nota_tipo               varchar(20)    NOT NULL,
   patron                  varchar(20)    NOT NULL,
   usuario_origen          varchar(20)    NULL,
   usuario_destino         varchar(20)    NULL, 
   titulo                  varchar(50)    NULL,
   texto                   text           NULL,
   creacion                timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apl_patron_nota_pk" PRIMARY KEY ("patron_nota"),
   CONSTRAINT  "apl_patron_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_patron_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_patron_nota_fk_patron" FOREIGN KEY ("patron") REFERENCES "apl_patron" ("patron") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_patron_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apl_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_patron_info
(
   patron                  varchar(20)    NOT NULL,
   descripcion_breve       varchar(255)   NULL,
   descripcion_larga       text           NULL,
   CONSTRAINT  "apl_patron_info_pk" PRIMARY KEY ("patron"),
   CONSTRAINT  "apl_patron_info_fk_patron" FOREIGN KEY ("patron") REFERENCES "apl_patron" ("patron") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE SEQUENCE apl_buffer_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apl_buffer
(
   buffer               int4           DEFAULT nextval('"apl_buffer_seq"'::text) NOT NULL, 
   descripcion          varchar(255)   NOT NULL,
   cuerpo               text  NULL,
   CONSTRAINT  "apl_buffer_pk" PRIMARY KEY ("buffer")
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_item
(
   item                       varchar(60)    NOT NULL,
   padre                      varchar(60)    NOT NULL,
   carpeta                    smallint       NULL,
   nivel_acceso               smallint       NOT NULL,
   solicitud_tipo             varchar(20)    NOT NULL,
   pagina_tipo                varchar(20)    NULL,
   nombre                     varchar(80)    NOT NULL,
   descripcion                varchar(255)   NULL,
   buffer                     int4           NULL,
   patron                     varchar(20)    NOT NULL,
   patron_especifico          varchar(80)    NULL,
   menu                       smallint       NULL,
   orden                      float          NULL,
   solicitud_registrar        smallint       NULL,
   solicitud_obs_tipo         varchar(20)    NULL,
   solicitud_observacion      varchar(255)   NULL,
   solicitud_registrar_cron   smallint       NULL,
   proyecto                   varchar(15)    NOT NULL,
   prueba_directorios         smallint       NULL,
   parametro_a                varchar(255)   NULL,
   parametro_b                varchar(255)   NULL,
   parametro_c                varchar(255)   NULL,
   usuario                    varchar(20)    NULL,
   creacion                   timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apl_item_pk" PRIMARY KEY ("item"),
-- Como el DUMP devuelve a los registros desordenadors este constraint hay que definirlo al final
-- CONSTRAINT  "apl_item_fk_padre" FOREIGN KEY ("padre") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_buffer" FOREIGN KEY ("buffer") REFERENCES "apl_buffer" ("buffer") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_solic_tipo" FOREIGN KEY ("solicitud_tipo") REFERENCES "apl_solicitud_tipo" ("solicitud_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_solic_ot" FOREIGN KEY ("solicitud_obs_tipo") REFERENCES "apl_solicitud_obs_tipo" ("solicitud_obs_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_niv_acc" FOREIGN KEY ("nivel_acceso") REFERENCES "apl_nivel_acceso" ("nivel_acceso") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_pag_tipo" FOREIGN KEY ("pagina_tipo") REFERENCES "apl_pagina_tipo" ("pagina_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_patron" FOREIGN KEY ("patron") REFERENCES "apl_patron" ("patron") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apl_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE SEQUENCE apl_item_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apl_item_nota
(
   item_nota               int4           DEFAULT nextval('"apl_item_nota_seq"'::text) NOT NULL, 
   nota_tipo               varchar(20)    NOT NULL,
   item                    varchar(60)    NOT NULL,
   usuario_origen          varchar(20)    NULL,
   usuario_destino         varchar(20)    NULL, 
   titulo                  varchar(50)    NULL,
   texto                   text           NULL,
   creacion                timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apl_item_nota_pk"   PRIMARY KEY ("item_nota"),
   CONSTRAINT  "apl_item_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_nota_fk_item" FOREIGN KEY ("item") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apl_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_item_info
(
   item                    varchar(60)    NOT NULL,
   descripcion_breve       varchar(255)   NULL,
   descripcion_larga       text           NULL,
   CONSTRAINT  "apl_item_info_pk"   PRIMARY KEY ("item"),
   CONSTRAINT  "apl_item_info_fk_item" FOREIGN KEY ("item") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_clase
(
   clase                      varchar(20)    NOT NULL,
   archivo                    varchar(80)    NOT NULL,
   descripcion                varchar(250)   NOT NULL,
   icono                      varchar(60)    NOT NULL, --> Icono con el que los objetos de la clase aparecen representados en las listas
   instanciador               varchar(60)    NOT NULL, --> Item del catalogo a invocar como instanciador de objetos de esta clase
   editor                     varchar(60)    NOT NULL, --> Item del catalogo a invocar como editor de objetos de esta clase
   plan_dump_objeto           varchar(255)   NOT NULL, --> Lista ordenada de tablas que poseen la definicion del objeto (respetar FK!)
   sql_info                   text           NOT NULL, --> SQL que DUMPEA el estado del objeto
   doc_clase                  varchar(255)    NULL,       --> GIF donde hay un Diagrama de clases.
   doc_db                     varchar(255)    NULL,       --> GIF donde hay un DER de las tablas que necesita la clase.
   doc_sql                    varchar(255)    NULL,       --> path al archivo que crea las tablas.
   parametro_a                varchar(255)   NULL,
   parametro_b                varchar(255)   NULL,
   parametro_c                varchar(255)   NULL,
   CONSTRAINT  "apl_clase_pk" PRIMARY KEY ("clase"),
   CONSTRAINT  "apl_clase_fk_editor" FOREIGN KEY ("editor") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_clase_fk_instan" FOREIGN KEY ("instanciador") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE SEQUENCE apl_clase_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apl_clase_nota
(
   clase_nota              int4           DEFAULT nextval('"apl_clase_nota_seq"'::text) NOT NULL, 
   nota_tipo               varchar(20)    NOT NULL,
   clase                   varchar(20)    NOT NULL,
   usuario_origen          varchar(20)    NULL,
   usuario_destino         varchar(20)    NULL, 
   titulo                  varchar(50)    NULL,
   texto                   text           NULL,
   creacion                timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apl_clase_nota_pk"  PRIMARY KEY ("clase_nota"),
   CONSTRAINT  "apl_clase_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_clase_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_clase_nota_fk_clase" FOREIGN KEY ("clase") REFERENCES "apl_clase" ("clase") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_clase_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apl_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_clase_info
(
   clase                   varchar(20)    NOT NULL,
   descripcion_breve       varchar(255)   NULL,
   descripcion_larga       text           NULL,
   CONSTRAINT  "apl_clase_info_pk"  PRIMARY KEY ("clase"),
   CONSTRAINT  "apl_clase_info_fk_clase" FOREIGN KEY ("clase") REFERENCES "apl_clase" ("clase") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_clase_dependencias
(
   clase_consumidora       varchar(20)    NOT NULL,
   clase_proveedora        varchar(20)    NOT NULL,
   cantidad_minima         smallint       NOT NULL,
   cantidad_maxima         smallint       NOT NULL,
   CONSTRAINT  "apl_clase_depen_pk" PRIMARY KEY ("clase_consumidora","clase_proveedora"),
   CONSTRAINT  "apl_clase_depen_fk_clase_c" FOREIGN KEY ("clase_consumidora") REFERENCES "apl_clase" ("clase") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_clase_depen_fk_clase_p" FOREIGN KEY ("clase_proveedora") REFERENCES "apl_clase" ("clase") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_patron_dependencias
(
   patron                  varchar(20)    NOT NULL,
   clase_utilizada         varchar(20)    NOT NULL,
   cantidad_minima         smallint       NOT NULL,
   cantidad_maxima         smallint       NOT NULL,
   descripcion             varchar(250)   NULL,
   CONSTRAINT  "apl_patron_depen_pk"   PRIMARY KEY ("patron","clase_utilizada"),
   CONSTRAINT  "apl_patron_depen_fk_clase" FOREIGN KEY ("clase_utilizada") REFERENCES "apl_clase" ("clase") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_patron_depen_fk_patron" FOREIGN KEY ("patron") REFERENCES "apl_patron" ("patron") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
-----------------------------------------------------------------------------------------------------

CREATE TABLE apl_objeto_tema
(
   objeto_tema             varchar(30)    NOT NULL,
   descripcion             varchar(255)   NULL,
   proyecto                varchar(15)    NOT NULL,
   CONSTRAINT  "apl_obj_tema_pk" PRIMARY KEY ("objeto_tema"),
   CONSTRAINT  "apl_obj_tema_fk_proy" FOREIGN KEY ("proyecto") REFERENCES "apl_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_solicitud_obj_obs_tipo
(
   solicitud_obj_obs_tipo        varchar(20)    NOT NULL,
   descripcion                   varchar(255)   NOT NULL,
   clase                         varchar(20)    NULL,
   CONSTRAINT  "apl_sol_obj_obs_tipo_pk" PRIMARY KEY ("solicitud_obj_obs_tipo"),
   CONSTRAINT  "apl_sol_obj_obs_fk_clase" FOREIGN KEY ("clase") REFERENCES "apl_clase" ("clase") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE SEQUENCE apl_objeto_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apl_objeto
(
   objeto                        int4           DEFAULT nextval('"apl_objeto_seq"'::text) NOT NULL, 
   anterior                      varchar(20)    NULL,
   reflexivo                     smallint       NULL,
   clase                         varchar(20)    NOT NULL,
   objeto_tema                   varchar(30)    NULL,
   nombre                        varchar(80)    NOT NULL,
   descripcion                   varchar(255)   NULL,
   fuente_datos                  varchar(20)    NOT NULL,
   proyecto                      varchar(15)    NOT NULL,
   solicitud_registrar           smallint       NULL,
   solicitud_obj_obs_tipo        varchar(20)    NULL,
   solicitud_obj_observacion     varchar(255)   NULL,
   parametro_a                   varchar(100)   NULL,
   parametro_b                   varchar(100)   NULL,
   parametro_c                   varchar(100)   NULL,
   usuario                       varchar(20)    NULL,
   creacion                      timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apl_objeto_pk"   PRIMARY KEY ("objeto"),
   CONSTRAINT  "apl_objeto_fk_clase" FOREIGN KEY ("clase") REFERENCES "apl_clase" ("clase") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_fk_fuente_datos" FOREIGN KEY ("fuente_datos") REFERENCES "apl_fuente_datos" ("fuente_datos") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_fk_solic_ot" FOREIGN KEY ("solicitud_obj_obs_tipo") REFERENCES "apl_solicitud_obj_obs_tipo" ("solicitud_obj_obs_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apl_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_fk_tema" FOREIGN KEY ("objeto_tema") REFERENCES "apl_objeto_tema" ("objeto_tema") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_fk_usuario" FOREIGN KEY ("usuario") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
-----------------------------------------------------------------------------------------------------

CREATE SEQUENCE apl_objeto_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apl_objeto_nota
(
   objeto_nota             int4           DEFAULT nextval('"apl_objeto_nota_seq"'::text) NOT NULL, 
   nota_tipo               varchar(20)    NOT NULL,
   objeto                  int4           NOT NULL,
   usuario_origen          varchar(20)    NULL,
   usuario_destino         varchar(20)    NULL, 
   titulo                  varchar(50)    NULL,
   texto                   text           NULL,
   creacion                timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apl_objeto_nota_pk" PRIMARY KEY ("objeto_nota"),
   CONSTRAINT  "apl_objeto_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_nota_fk_objeto" FOREIGN KEY ("objeto") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apl_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_objeto_info
(
   objeto                  int4           NOT NULL,
   descripcion_breve       varchar(255)   NULL,
   descripcion_larga       text           NULL,
   CONSTRAINT  "apl_objeto_info_pk" PRIMARY KEY ("objeto"),
   CONSTRAINT  "apl_objeto_info_fk_objeto" FOREIGN KEY ("objeto") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_objeto_dependencias
(
   objeto_consumidor       int4           NOT NULL,
   objeto_proveedor        int4           NOT NULL,
   orden                   smallint       NOT NULL,
   CONSTRAINT  "apl_objeto_depen_pk"   PRIMARY KEY ("objeto_consumidor","objeto_proveedor"),
   CONSTRAINT  "apl_objeto_depen_fk_objeto_c" FOREIGN KEY ("objeto_consumidor") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_objeto_depen_fk_objeto_p" FOREIGN KEY ("objeto_proveedor") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_item_objeto
(
   item                    varchar(60)    NOT NULL,
   objeto                  int4           NOT NULL,
   orden                   smallint       NOT NULL,
   inicializar             smallint       NULL,
   CONSTRAINT  "apl_item_consumo_obj_pk"  PRIMARY KEY ("item","objeto"),
   CONSTRAINT  "apl_item_consumo_obj_fk_item" FOREIGN KEY ("item") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_item_consumo_obj_fk_objeto" FOREIGN KEY ("objeto") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_vinculo_tipo
(
   vinculo_tipo            varchar(10)    NOT NULL,
   descripcion             varchar(255)   NOT NULL,
   CONSTRAINT  "apl_vinculo_tipo_pk" PRIMARY KEY ("vinculo_tipo")
);
-----------------------------------------------------------------------------------------------------

CREATE TABLE apl_vinculo
(
   item                    varchar(60)    NOT NULL,
   objeto                  int4           NOT NULL,
   item_destino            varchar(60)    NOT NULL,
   vinculo_tipo            varchar(10)    NOT NULL, --> Popup, zoom??
   orden                   smallint       NOT NULL,
   inicializacion          varchar(255)   NULL,
   parametros              varchar(255)   NULL,
   operacion               smallint       NULL, --> flag que indica si el vinculo implica una propagacion de la operacion o no (util para determinar permisos en cascada)
   texto                   varchar(60)    NULL,
   imagen_fuente           varchar(20)    NULL, --> proyecto o apl
   imagen                  varchar(60)    NULL,
   CONSTRAINT  "apl_vinc_pk" PRIMARY KEY ("item","objeto","item_destino"),
   CONSTRAINT  "apl_vinc_fk_item_o" FOREIGN KEY ("item") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_vinc_fk_item_d" FOREIGN KEY ("item_destino") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_vinc_fk_objeto" FOREIGN KEY ("objeto") REFERENCES "apl_objeto" ("objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_vinc_fk_tipo" FOREIGN KEY ("vinculo_tipo") REFERENCES "apl_vinculo_tipo" ("vinculo_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_usuario_grupo_acc_item
(
   usuario_grupo_acc       varchar(20) NOT NULL,
   item                    varchar(60) NOT NULL,
   CONSTRAINT  "apl_usu_item_pk" PRIMARY KEY ("usuario_grupo_acc","item"),
   CONSTRAINT  "apl_usu_item_fk_item" FOREIGN KEY ("item") REFERENCES "apl_item" ("item") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_usu_item_fk_us_gru_acc" FOREIGN KEY ("usuario_grupo_acc") REFERENCES "apl_usuario_grupo_acc" ("usuario_grupo_acc") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);


--##################################################################################################
--##################################################################################################
--################################   DOCUMENTACION del NUCLEO   ####################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_nucleo
(
   nucleo                  varchar(60)    NOT NULL,
   archivo                 varchar(80)    NOT NULL,
   descripcion             varchar(250)   NOT NULL,
   doc_nucleo              varchar(255)   NULL,       --> GIF donde hay un Diagrama
   doc_db                  varchar(60)    NULL,       --> GIF donde hay un DER de las tablas que necesita la nucleo.
   doc_sql                 varchar(60)    NULL,       --> path al archivo que crea las tablas.
   orden                   smallint       NULL,
   CONSTRAINT  "apl_nucleo_pk"   PRIMARY KEY ("nucleo")
);
---------------------------------------------------------------------------------------------------

CREATE SEQUENCE apl_nucleo_nota_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1;
CREATE TABLE apl_nucleo_nota
(
   nucleo_nota             int4           DEFAULT nextval('"apl_nucleo_nota_seq"'::text) NOT NULL, 
   nota_tipo               varchar(20)    NOT NULL,
   nucleo                  varchar(60)    NOT NULL,
   usuario_origen          varchar(20)    NULL,
   usuario_destino         varchar(20)    NULL, 
   titulo                  varchar(50)    NULL,
   texto                   text           NULL,
   creacion                timestamp(0)   without time zone DEFAULT current_timestamp NULL,
   CONSTRAINT  "apl_nucleo_nota_pk" PRIMARY KEY ("nucleo_nota"),
   CONSTRAINT  "apl_nucleo_nota_fk_usuo" FOREIGN KEY ("usuario_origen") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_nucleo_nota_fk_usud" FOREIGN KEY ("usuario_destino") REFERENCES "apl_usuario" ("usuario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_nucleo_nota_fk_nucleo" FOREIGN KEY ("nucleo") REFERENCES "apl_nucleo" ("nucleo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apl_nucleo_nota_fk_tipo" FOREIGN KEY ("nota_tipo") REFERENCES "apl_nota_tipo" ("nota_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_nucleo_info
(
   nucleo                  varchar(60)    NOT NULL,
   descripcion_breve       varchar(255)   NULL,
   descripcion_larga       text           NULL,
   CONSTRAINT  "apl_nucleo_info_pk" PRIMARY KEY ("nucleo"),
   CONSTRAINT  "apl_nucleo_info_fk_nucleo" FOREIGN KEY ("nucleo") REFERENCES "apl_nucleo" ("nucleo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
---------------------------------------------------------------------------------------------------
