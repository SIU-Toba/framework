--DISCONNECT CURRENT;
--DROP DATABASE apex_test;
--CREATE DATABASE apex_test IN ol_robotobor;

--##################################################################################################
--##################################################################################################
--###########################################   General  ###########################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_proyecto
(
   proyecto                char(15)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   path_includes           char(255)   NOT NULL,
   path_browser            char(255)   NOT NULL,
   item_inicio             char(60)    NOT NULL,
   administrador           char(60)  ,--NOT
   orden                   smallint     ,
   PRIMARY KEY (proyecto)

);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_instancia
(
   instancia               char(80)    NOT NULL,
   proyecto_activo         char(15)    NOT NULL,
   version                 char(15)    NOT NULL,
   institucion             char(255) ,
   observaciones           char(255) ,
   administrador_1         char(60)  ,--NOT
   administrador_2         char(60)  ,--NOT
   administrador_3         char(60)  ,--NOT
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (instancia),
   FOREIGN KEY (proyecto_activo) REFERENCES apl_proyecto (proyecto)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_fuente_datos_motor
(
   fuente_datos_motor      char(30)       NOT NULL,
   nombre                  char(255)      NOT NULL,
   version                 char(30)       NOT NULL,
   PRIMARY KEY (fuente_datos_motor) 
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_fuente_datos
(
   fuente_datos            char(20)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   fuente_datos_motor      char(30)    NOT NULL,
   host                    char(60)  ,
   usuario                 char(30)  ,
   clave                   char(30)  ,
   base                    char(30)    NOT NULL,
   administrador           char(60)  ,
   proyecto                char(15)    NOT NULL,
   link_apl                smallint     ,
   orden                   smallint     ,
   PRIMARY KEY (fuente_datos),
   FOREIGN KEY (fuente_datos_motor) REFERENCES apl_fuente_datos_motor (fuente_datos_motor)   ,
   FOREIGN KEY (proyecto) REFERENCES apl_proyecto (proyecto)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_grafico
(
   grafico                 char(30)       NOT NULL,
   descripcion             char(255)      NOT NULL,
   PRIMARY KEY (grafico) 
);
-----------------------------------------------------------------------------------------------------

CREATE TABLE apl_repositorio
(
   repositorio             char(80)    NOT NULL,
   descripcion             char(255) ,
   PRIMARY KEY (repositorio)
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_nivel_acceso
(
   nivel_acceso               smallint       NOT NULL,
   nombre                     char(80)    NOT NULL,
   descripcion                char      ,
   PRIMARY KEY (nivel_acceso)
);
---------------------

CREATE TABLE apl_nivel_ejecucion
(
   nivel_ejecucion         char(15)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   PRIMARY KEY (nivel_ejecucion)
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_solicitud_tipo
(
   solicitud_tipo             char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   icono                      char(30)  ,
   PRIMARY KEY (solicitud_tipo)
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_elemento_formulario
(
   elemento_formulario        char(30)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   PRIMARY KEY (elemento_formulario)
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_solicitud_obs_tipo
(
   solicitud_obs_tipo         char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   criterio                   char(20)    NOT NULL,
   proyecto                   char(15)       NOT NULL,
   PRIMARY KEY (solicitud_obs_tipo),
   FOREIGN KEY (proyecto) REFERENCES apl_proyecto (proyecto)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_nota_tipo
(
   nota_tipo                  char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   icono                      char(30)  ,
   PRIMARY KEY (nota_tipo)
);

---------------------------------------------------------------------------------------------------

CREATE TABLE apl_pagina_tipo
(
   pagina_tipo                  char(20)    NOT NULL,
   descripcion                  char(255)   NOT NULL,
   include_arriba               char(100) ,
   include_abajo                char(100) ,
   contexto                     char(255) , -- Establece variables de CONTEXTO?
   PRIMARY KEY (pagina_tipo)
);


--##################################################################################################
--##################################################################################################
--#############################################  Usuario  ##########################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_usuario_tipodoc
(
   usuario_tipodoc   char(10) NOT NULL,
   descripcion                char(40) NOT NULL,
   PRIMARY KEY (usuario_tipodoc)
);
---------------------

CREATE TABLE apl_usuario_perfil_datos
(
   usuario_perfil_datos       char(20)    NOT NULL,
   nombre                     char(80)    NOT NULL,
   descripcion                char      ,
   PRIMARY KEY (usuario_perfil_datos)
);
---------------------

CREATE TABLE apl_usuario_grupo_acc
(
   usuario_grupo_acc          char(20)    NOT NULL,
   nombre                     char(80)    NOT NULL,
   descripcion                char      ,
   nivel_acceso               smallint     ,
   vencimiento                date         ,
   dias                       smallint     ,
   hora_entrada               datetime HOUR to MINUTE DEFAULT NULL,
   hora_salida                datetime HOUR to MINUTE DEFAULT NULL,
   FOREIGN KEY (nivel_acceso) REFERENCES apl_nivel_acceso (nivel_acceso)   ,
   PRIMARY KEY (usuario_grupo_acc)
);
---------------------

CREATE TABLE apl_usuario
(
   usuario                    char(20)    NOT NULL,
   clave                      char(20)    NOT NULL,
   usuario_grupo_acc          char(20)    NOT NULL,
   usuario_perfil_datos        char(20)    NOT NULL,
   nombre                     char(80)  ,
   usuario_tipodoc            char(10)  ,
   pre                        char(2)   ,
   ciu                        char(18)  ,
   suf                        char(1)   ,
   email                      char(80)  ,
   telefono                   char(18)  ,
   vencimiento                date         ,
   dias                       smallint     ,
   hora_entrada               datetime HOUR to MINUTE DEFAULT NULL,
   hora_salida                datetime HOUR to MINUTE DEFAULT NULL,
   ip_permitida               char(20)  ,
   solicitud_registrar        smallint     ,
   solicitud_obs_tipo         char(20)  ,
   solicitud_observacion      char(255) ,
   PRIMARY KEY (usuario),
   UNIQUE (usuario_tipodoc,pre,ciu,suf),
   FOREIGN KEY (usuario_grupo_acc) REFERENCES apl_usuario_grupo_acc (usuario_grupo_acc)   ,
   FOREIGN KEY (usuario_perfil_datos) REFERENCES apl_usuario_perfil_datos (usuario_perfil_datos)   ,
   FOREIGN KEY (solicitud_obs_tipo) REFERENCES apl_solicitud_obs_tipo (solicitud_obs_tipo)   ,
   FOREIGN KEY (usuario_tipodoc) REFERENCES apl_usuario_tipodoc (usuario_tipodoc)   
);

---------------------

CREATE TABLE apl_usuario_proyecto
(
   usuario                 char(20) NOT NULL,
   proyecto                char(15)    NOT NULL,
   PRIMARY KEY (usuario,proyecto),
   FOREIGN KEY (proyecto) REFERENCES apl_proyecto (proyecto)   ,
   FOREIGN KEY (usuario) REFERENCES apl_usuario (usuario)   

);

--##################################################################################################
--##################################################################################################
--##################   ELEMENTOS CENTRALES (item, patron, clase y objeto)   ########################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_patron
(
   patron               char(20)    NOT NULL,
   archivo              char(80)    NOT NULL,
   descripcion          char(250) ,
   auto_html            smallint     ,
   PRIMARY KEY (patron)
);
---------------------------------------------------------------------------------------------------


CREATE TABLE apl_patron_nota
(
   patron_nota             serial,
   nota_tipo               char(20)    NOT NULL,
   patron                  char(20)    NOT NULL,
   usuario_origen          char(20)  ,
   usuario_destino         char(20)  , 
   titulo                  char(50)  ,
   texto                   text         ,
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (patron_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (patron) REFERENCES apl_patron (patron)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apl_nota_tipo (nota_tipo)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_patron_info
(
   patron                  char(20)    NOT NULL,
   descripcion_breve       char(255) ,
   descripcion_larga       text         ,
   PRIMARY KEY (patron),
   FOREIGN KEY (patron) REFERENCES apl_patron (patron)   
);
---------------------------------------------------------------------------------------------------


CREATE TABLE apl_buffer
(
   buffer               serial,
   descripcion          char(255)   NOT NULL,
   cuerpo               text,
   PRIMARY KEY (buffer)
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_item
(
   item                       char(60)    NOT NULL,
   padre                      char(60)    NOT NULL,
   carpeta                    smallint     ,
   nivel_acceso               smallint       NOT NULL,
   solicitud_tipo             char(20)    NOT NULL,
   pagina_tipo                char(20)  ,
   nombre                     char(80)    NOT NULL,
   descripcion                char(255) ,
   buffer                     integer         ,
   patron                     char(20)    NOT NULL,
   patron_especifico          char(80)  ,
   menu                       smallint     ,
   orden                      float        ,
   solicitud_registrar        smallint     ,
   solicitud_obs_tipo         char(20)  ,
   solicitud_observacion      char(255) ,
   solicitud_registrar_cron   smallint     ,
   proyecto                   char(15)    NOT NULL,
   prueba_directorios         smallint     ,
   parametro_a                char(255) ,
   parametro_b                char(255) ,
   parametro_c                char(255) ,
   usuario                    char(20)  ,
   creacion                   datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (item),
-- Como el DUMP devuelve a los registros desordenadors este constraint hay que definirlo al final

   FOREIGN KEY (buffer) REFERENCES apl_buffer (buffer)   ,
   FOREIGN KEY (solicitud_tipo) REFERENCES apl_solicitud_tipo (solicitud_tipo)   ,
   FOREIGN KEY (solicitud_obs_tipo) REFERENCES apl_solicitud_obs_tipo (solicitud_obs_tipo)   ,
   FOREIGN KEY (nivel_acceso) REFERENCES apl_nivel_acceso (nivel_acceso)   ,
   FOREIGN KEY (pagina_tipo) REFERENCES apl_pagina_tipo (pagina_tipo)   ,
   FOREIGN KEY (patron) REFERENCES apl_patron (patron)   ,
   FOREIGN KEY (proyecto) REFERENCES apl_proyecto (proyecto)   ,
   FOREIGN KEY (usuario) REFERENCES apl_usuario (usuario)   
);
---------------------------------------------------------------------------------------------------


CREATE TABLE apl_item_nota
(
   item_nota               serial,
   nota_tipo               char(20)    NOT NULL,
   item                    char(60)    NOT NULL,
   usuario_origen          char(20)  ,
   usuario_destino         char(20)  , 
   titulo                  char(50)  ,
   texto                   text         ,
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (item_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (item) REFERENCES apl_item (item)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apl_nota_tipo (nota_tipo)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_item_info
(
   item                    char(60)    NOT NULL,
   descripcion_breve       char(255) ,
   descripcion_larga       text         ,
   PRIMARY KEY (item),
   FOREIGN KEY (item) REFERENCES apl_item (item)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_clase
(
   clase                      char(20)    NOT NULL,
   archivo                    char(80)    NOT NULL,
   descripcion                char(250)   NOT NULL,
   icono                      char(60)    NOT NULL, --> Icono con el que los objetos de la clase aparecen representados en las listas
   instanciador               char(60)    NOT NULL, --> Item del catalogo a invocar como instanciador de objetos de esta clase
   editor                     char(60)    NOT NULL, --> Item del catalogo a invocar como editor de objetos de esta clase
   plan_dump_objeto           char(255)   NOT NULL, --> Lista ordenada de tablas que poseen la definicion del objeto (respetar FK!)
   sql_info                   text           NOT NULL, --> SQL que DUMPEA el estado del objeto
   doc_clase                  char(255)  ,       --> GIF donde hay un Diagrama de clases.
   doc_db                     char(255)  ,       --> GIF donde hay un DER de las tablas que necesita la clase.
   doc_sql                    char(255)  ,       --> path al archivo que crea las tablas.
   parametro_a                char(255) ,
   parametro_b                char(255) ,
   parametro_c                char(255) ,
   PRIMARY KEY (clase),
   FOREIGN KEY (editor) REFERENCES apl_item (item)   ,
   FOREIGN KEY (instanciador) REFERENCES apl_item (item)   
);
---------------------------------------------------------------------------------------------------


CREATE TABLE apl_clase_nota
(
   clase_nota              serial,
   nota_tipo               char(20)    NOT NULL,
   clase                   char(20)    NOT NULL,
   usuario_origen          char(20)  ,
   usuario_destino         char(20)  , 
   titulo                  char(50)  ,
   texto                   text         ,
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (clase_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (clase) REFERENCES apl_clase (clase)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apl_nota_tipo (nota_tipo)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_clase_info
(
   clase                   char(20)    NOT NULL,
   descripcion_breve       char(255) ,
   descripcion_larga       text         ,
   PRIMARY KEY (clase),
   FOREIGN KEY (clase) REFERENCES apl_clase (clase)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_clase_dependencias
(
   clase_consumidora       char(20)    NOT NULL,
   clase_proveedora        char(20)    NOT NULL,
   cantidad_minima         smallint       NOT NULL,
   cantidad_maxima         smallint       NOT NULL,
   PRIMARY KEY (clase_consumidora,clase_proveedora),
   FOREIGN KEY (clase_consumidora) REFERENCES apl_clase (clase)   ,
   FOREIGN KEY (clase_proveedora) REFERENCES apl_clase (clase)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_patron_dependencias
(
   patron                  char(20)    NOT NULL,
   clase_utilizada         char(20)    NOT NULL,
   cantidad_minima         smallint       NOT NULL,
   cantidad_maxima         smallint       NOT NULL,
   descripcion             char(250) ,
   PRIMARY KEY (patron,clase_utilizada),
   FOREIGN KEY (clase_utilizada) REFERENCES apl_clase (clase)   ,
   FOREIGN KEY (patron) REFERENCES apl_patron (patron)   
);
-----------------------------------------------------------------------------------------------------

CREATE TABLE apl_objeto_tema
(
   objeto_tema             char(30)    NOT NULL,
   descripcion             char(255) ,
   proyecto                char(15)    NOT NULL,
   PRIMARY KEY (objeto_tema),
   FOREIGN KEY (proyecto) REFERENCES apl_proyecto (proyecto)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_solicitud_obj_obs_tipo
(
   solicitud_obj_obs_tipo        char(20)    NOT NULL,
   descripcion                   char(255)   NOT NULL,
   clase                         char(20)  ,
   PRIMARY KEY (solicitud_obj_obs_tipo),
   FOREIGN KEY (clase) REFERENCES apl_clase (clase)   
);
---------------------------------------------------------------------------------------------------


CREATE TABLE apl_objeto
(
   objeto                        serial,
   anterior                      char(20)  ,
   reflexivo                     smallint     ,
   clase                         char(20)    NOT NULL,
   objeto_tema                   char(30)  ,
   nombre                        char(80)    NOT NULL,
   descripcion                   char(255) ,
   fuente_datos                  char(20)    NOT NULL,
   proyecto                      char(15)    NOT NULL,
   solicitud_registrar           smallint     ,
   solicitud_obj_obs_tipo        char(20)  ,
   solicitud_obj_observacion     char(255) ,
   parametro_a                   char(100) ,
   parametro_b                   char(100) ,
   parametro_c                   char(100) ,
   usuario                       char(20)  ,
   creacion                      datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (objeto),
   FOREIGN KEY (clase) REFERENCES apl_clase (clase)   ,
   FOREIGN KEY (fuente_datos) REFERENCES apl_fuente_datos (fuente_datos)   ,
   FOREIGN KEY (solicitud_obj_obs_tipo) REFERENCES apl_solicitud_obj_obs_tipo (solicitud_obj_obs_tipo)   ,
   FOREIGN KEY (proyecto) REFERENCES apl_proyecto (proyecto)   ,
   FOREIGN KEY (objeto_tema) REFERENCES apl_objeto_tema (objeto_tema)   ,
   FOREIGN KEY (usuario) REFERENCES apl_usuario (usuario)   
);
-----------------------------------------------------------------------------------------------------


CREATE TABLE apl_objeto_nota
(
   objeto_nota             serial,
   nota_tipo               char(20)    NOT NULL,
   objeto                  integer           NOT NULL,
   usuario_origen          char(20)  ,
   usuario_destino         char(20)  , 
   titulo                  char(50)  ,
   texto                   text         ,
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (objeto_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (objeto) REFERENCES apl_objeto (objeto)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apl_nota_tipo (nota_tipo)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_objeto_info
(
   objeto                  integer           NOT NULL,
   descripcion_breve       char(255) ,
   descripcion_larga       text         ,
   PRIMARY KEY (objeto),
   FOREIGN KEY (objeto) REFERENCES apl_objeto (objeto)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_objeto_dependencias
(
   objeto_consumidor       integer           NOT NULL,
   objeto_proveedor        integer           NOT NULL,
   orden                   smallint       NOT NULL,
   PRIMARY KEY (objeto_consumidor,objeto_proveedor),
   FOREIGN KEY (objeto_consumidor) REFERENCES apl_objeto (objeto)   ,
   FOREIGN KEY (objeto_proveedor) REFERENCES apl_objeto (objeto)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_item_objeto
(
   item                    char(60)    NOT NULL,
   objeto                  integer           NOT NULL,
   orden                   smallint       NOT NULL,
   inicializar             smallint     ,
   PRIMARY KEY (item,objeto),
   FOREIGN KEY (item) REFERENCES apl_item (item)   ,
   FOREIGN KEY (objeto) REFERENCES apl_objeto (objeto)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_vinculo_tipo
(
   vinculo_tipo            char(10)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   PRIMARY KEY (vinculo_tipo)
);
-----------------------------------------------------------------------------------------------------

CREATE TABLE apl_vinculo
(
   item                    char(60)    NOT NULL,
   objeto                  integer           NOT NULL,
   item_destino            char(60)    NOT NULL,
   vinculo_tipo            char(10)    NOT NULL, --> Popup, zoom??
   orden                   smallint       NOT NULL,
   inicializacion          char(255) ,
   parametros              char(255) ,
   operacion               smallint     , --> flag que indica si el vinculo implica una propagacion de la operacion o no (util para determinar permisos en cascada)
   texto                   char(60)  ,
   imagen_fuente           char(20)  , --> proyecto o apl
   imagen                  char(60)  ,
   PRIMARY KEY (item,objeto,item_destino),
   FOREIGN KEY (item) REFERENCES apl_item (item)   ,
   FOREIGN KEY (item_destino) REFERENCES apl_item (item)   ,
   FOREIGN KEY (objeto) REFERENCES apl_objeto (objeto)   ,
   FOREIGN KEY (vinculo_tipo) REFERENCES apl_vinculo_tipo (vinculo_tipo)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_usuario_grupo_acc_item
(
   usuario_grupo_acc       char(20) NOT NULL,
   item                    char(60) NOT NULL,
   PRIMARY KEY (usuario_grupo_acc,item),
   FOREIGN KEY (item) REFERENCES apl_item (item)   ,
   FOREIGN KEY (usuario_grupo_acc) REFERENCES apl_usuario_grupo_acc (usuario_grupo_acc)   
);


--##################################################################################################
--##################################################################################################
--################################   DOCUMENTACION del NUCLEO   ####################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apl_nucleo
(
   nucleo                  char(60)    NOT NULL,
   archivo                 char(80)    NOT NULL,
   descripcion             char(250)   NOT NULL,
   doc_nucleo              char(255) ,       --> GIF donde hay un Diagrama
   doc_db                  char(60)  ,       --> GIF donde hay un DER de las tablas que necesita la nucleo.
   doc_sql                 char(60)  ,       --> path al archivo que crea las tablas.
   orden                   smallint     ,
   PRIMARY KEY (nucleo)
);
---------------------------------------------------------------------------------------------------


CREATE TABLE apl_nucleo_nota
(
   nucleo_nota             serial,
   nota_tipo               char(20)    NOT NULL,
   nucleo                  char(60)    NOT NULL,
   usuario_origen          char(20)  ,
   usuario_destino         char(20)  , 
   titulo                  char(50)  ,
   texto                   text         ,
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (nucleo_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apl_usuario (usuario)   ,
   FOREIGN KEY (nucleo) REFERENCES apl_nucleo (nucleo)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apl_nota_tipo (nota_tipo)   
);
---------------------------------------------------------------------------------------------------

CREATE TABLE apl_nucleo_info
(
   nucleo                  char(60)    NOT NULL,
   descripcion_breve       char(255) ,
   descripcion_larga       text         ,
   PRIMARY KEY (nucleo),
   FOREIGN KEY (nucleo) REFERENCES apl_nucleo (nucleo)   
);
---------------------------------------------------------------------------------------------------
