--**************************************************************************************************
--**************************************************************************************************
--*******************************************   General  *******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE 			apex_version
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Tabla de manejo de versiones
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   version						char(15)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   fecha							date				NOT NULL,
	observaciones				char		,
   PRIMARY KEY (version)
);
--#################################################################################################

CREATE TABLE 			apex_elemento_infra
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Representa un elemento de la infraestructura
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   elemento_infra          char(15)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   PRIMARY KEY (elemento_infra)
);
--#################################################################################################

CREATE TABLE 			apex_elemento_infra_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Representa una tabla donde se almacena parte del elemento
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   elemento_infra          char(15)    NOT NULL,
	tabla							char(30)		NOT NULL,
	columna_clave_proyecto	char(40)		NOT NULL,
	columna_clave				char(80)		NOT NULL,
	orden							smallint			NOT NULL,
   descripcion             char(255)   NOT NULL,
	dependiente					smallint		,
	proc_borrar					smallint		,
	proc_exportar				smallint		,
	proc_clonar					smallint		,
   PRIMARY KEY (elemento_infra,tabla,columna_clave_proyecto,columna_clave),
   FOREIGN KEY (elemento_infra) REFERENCES apex_elemento_infra (elemento_infra)   
);
--#################################################################################################

CREATE TABLE 			apex_estilo_paleta
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Representa una serie de colores
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   estilo_paleta           char(15)    NOT NULL,
	color_1						char(6)   	,
	color_2						char(6)   	,
	color_3						char(6)   	,
	color_4						char(6)   	,
	color_5						char(6)   	,
	color_6						char(6)   	,
   PRIMARY KEY (estilo_paleta)
);
--#################################################################################################

CREATE TABLE 			apex_estilo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Estilos CSS
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   estilo                  char(15)    NOT NULL,
   descripcion             char(255)   NOT NULL,
	estilo_paleta_p			char(15)  ,
	estilo_paleta_s			char(15)  ,
	estilo_paleta_n			char(15)  ,
	estilo_paleta_e			char(15)  ,
   PRIMARY KEY (estilo),
   FOREIGN KEY (estilo_paleta_p) REFERENCES apex_estilo_paleta (estilo_paleta)   ,
   FOREIGN KEY (estilo_paleta_s) REFERENCES apex_estilo_paleta (estilo_paleta)   ,
   FOREIGN KEY (estilo_paleta_n) REFERENCES apex_estilo_paleta (estilo_paleta)   ,
   FOREIGN KEY (estilo_paleta_e) REFERENCES apex_estilo_paleta (estilo_paleta)   
);
--#################################################################################################

CREATE TABLE 			apex_proyecto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: general
--: desc: Tabla maestra de proyectos
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                char(15)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   descripcion_corta       char(40)  	NOT NULL, 
   estilo		            char(15)    NOT NULL,
   path_includes           char(255) ,
   path_browser            char(255) ,
   administrador           char(60)  ,--NOT
	listar_multiproyecto		smallint		,
   orden                   float		    ,
	palabra_vinculo_std		char(30)	,
   PRIMARY KEY (proyecto),
   FOREIGN KEY (estilo) REFERENCES apex_estilo (estilo)   
);
--#################################################################################################

CREATE TABLE apex_instancia
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: instancia: 1
--: zona: general
--: desc: Datos de la instancia
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   instancia               char(80)    NOT NULL,
   version                 char(15)    NOT NULL,
   institucion             char(255) ,
   observaciones           char(255) ,
   administrador_1         char(60)  ,--NOT
   administrador_2         char(60)  ,--NOT
   administrador_3         char(60)  ,--NOT
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (instancia)
);
--#################################################################################################

CREATE TABLE apex_fuente_datos_motor
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: DBMS soportados
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	fuente_datos_motor      char(30)       NOT NULL,
   nombre                  char(255)      NOT NULL,
   version                 char(30)       NOT NULL,
   PRIMARY KEY (fuente_datos_motor) 
);
--#################################################################################################

CREATE TABLE apex_fuente_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: general
--: desc: Bases de datos a las que se puede acceder
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto                char(15)    NOT NULL,
   fuente_datos            char(20)    NOT NULL,
   fuente_datos_motor      char(30)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   descripcion_corta       char(40)  , -- NOT NULL,
   host                    char(60)  ,
   usuario                 char(30)  ,
   clave                   char(30)  ,
   base                    char(30)  , -- NOT? ODBC e instancia no la utilizan...
   administrador           char(60)  ,
   link_instancia          smallint     ,	-- En vez de abrir una conexion, utilizar la conexion a la intancia
   orden                   smallint     ,
   PRIMARY KEY (proyecto,fuente_datos),
   FOREIGN KEY (fuente_datos_motor) REFERENCES apex_fuente_datos_motor (fuente_datos_motor)   ,
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_grafico
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Tipo de grafico
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	grafico                 char(30)       NOT NULL,
   descripcion_corta       char(40)   	, --NOT
   descripcion             char(255)      NOT NULL,
   parametros              char   		,
   PRIMARY KEY (grafico) 
);
--#################################################################################################--

CREATE TABLE apex_recurso_origen
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Origen del recurso: apex o proyecto
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	recurso_origen          char(10)	      NOT NULL,
   descripcion             char(255)      NOT NULL,
   PRIMARY KEY (recurso_origen) 
);
--#################################################################################################--

CREATE TABLE apex_repositorio
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Listado de repositorios a los que me puedo conectar
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	repositorio             char(80)    NOT NULL,
   descripcion             char(255) ,
   PRIMARY KEY (repositorio)
);
--#################################################################################################

CREATE TABLE apex_nivel_acceso
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc: Categoria organizadora de niveles de seguridad (redobla la cualificaciond e elementos para fortalecer chequeos)
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	nivel_acceso               smallint       NOT NULL,
   nombre                     char(80)    NOT NULL,
   descripcion                char      ,
   PRIMARY KEY (nivel_acceso)
);
--#################################################################################################

CREATE TABLE apex_nivel_ejecucion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	nivel_ejecucion         char(15)    NOT NULL,
   descripcion             char(255)   NOT NULL,
   PRIMARY KEY (nivel_ejecucion)
);
--#################################################################################################

CREATE TABLE apex_solicitud_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_tipo             char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   descripcion_corta       	char(40)  , -- NOT NULL,
   icono                      char(30)  ,
   PRIMARY KEY (solicitud_tipo)
);
--#################################################################################################

CREATE TABLE apex_elemento_formulario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: general
--: desc: Elementos de formulario soportados
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	elemento_formulario        char(30)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   parametros                 char   	, -- Descripcion de los parametros que recibe este EF
	proyecto                   char(15)    NOT NULL,
	exclusivo_toba					smallint		,
   PRIMARY KEY (elemento_formulario),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_solicitud_obs_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto                   char(15)    NOT NULL,
   solicitud_obs_tipo         char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   criterio                   char(20)    NOT NULL,
   PRIMARY KEY (proyecto,solicitud_obs_tipo),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_pagina_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto                     	char(15)    NOT NULL,
   pagina_tipo                  	char(20)    NOT NULL,
   descripcion                  	char(255)   NOT NULL,
   include_arriba               	char(100) ,
   include_abajo                	char(100) ,
	exclusivo_toba						smallint		,
   contexto                    	char(255) , -- Establece variables de CONTEXTO? Cuales?
   PRIMARY KEY (proyecto,pagina_tipo),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--###################################################################################################


CREATE TABLE apex_columna_estilo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   columna_estilo            		serial,
   css			                  char(40)  	NOT NULL,
   descripcion                   char(255) ,
   descripcion_corta             char(40) ,
   PRIMARY KEY (columna_estilo) 
);
--###################################################################################################


CREATE TABLE apex_columna_formato
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   columna_formato           		serial,
   funcion		                  char(40)  	NOT NULL,
   archivo		                  char(80)  ,
   descripcion                   char(255) ,
   descripcion_corta             char(40)  ,
   parametros                    char(255) ,
   PRIMARY KEY (columna_formato) 
);

--###################################################################################################


CREATE TABLE apex_columna_proceso
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   columna_proceso           		serial,
   funcion		                  char(40)  	NOT NULL,
   archivo		                  char(80)  ,
   descripcion                   char(255) ,
   descripcion_corta             char(40)  ,
   parametros                    char(255) ,
   PRIMARY KEY (columna_proceso) 
);

--**************************************************************************************************
--**************************************************************************************************
--*********************************************  Usuario  ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_usuario_tipodoc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	usuario_tipodoc   			char(10) 	NOT NULL,
   descripcion                char(40) 	NOT NULL,
   PRIMARY KEY (usuario_tipodoc)
);
--#################################################################################################

CREATE TABLE apex_usuario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: usuario
--: desc:
--: instancia: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	usuario                    	char(20)    NOT NULL,
   clave                      	char(20)    NOT NULL,
   nombre                     	char(80)  ,
   usuario_tipodoc            	char(10)  ,
   pre                        	char(2)   ,
   ciu                        	char(18)  ,
   suf                        	char(1)   ,
   email                      	char(80)  ,
   telefono                   	char(18)  ,
   vencimiento                	date         ,
   dias                       	smallint     ,
   hora_entrada               	datetime HOUR to MINUTE DEFAULT NULL,
   hora_salida                	datetime HOUR to MINUTE DEFAULT NULL,
   ip_permitida               	char(20)  ,
   solicitud_registrar        	smallint     ,
   solicitud_obs_tipo_proyecto	char(15)  ,
   solicitud_obs_tipo         	char(20)  ,
   solicitud_observacion      	char(255) ,
   PRIMARY KEY (usuario),
   FOREIGN KEY (solicitud_obs_tipo_proyecto,solicitud_obs_tipo) REFERENCES apex_solicitud_obs_tipo (proyecto,solicitud_obs_tipo)   ,
   FOREIGN KEY (usuario_tipodoc) REFERENCES apex_usuario_tipodoc (usuario_tipodoc)   
);

--#################################################################################################

CREATE TABLE apex_usuario_perfil_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto                   char(15)    NOT NULL,
   usuario_perfil_datos       char(20)    NOT NULL,
   nombre                     char(80)    NOT NULL,
   descripcion                char      ,
	listar							smallint		,
   PRIMARY KEY (proyecto,usuario_perfil_datos),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_usuario_grupo_acc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto                   char(15)    NOT NULL,
   usuario_grupo_acc          char(20)    NOT NULL,
   nombre                     char(80)    NOT NULL,
   nivel_acceso               smallint       NOT NULL,
   descripcion                char      ,
   vencimiento                date         ,
   dias                       smallint     ,
   hora_entrada               datetime HOUR to MINUTE DEFAULT NULL,
   hora_salida                datetime HOUR to MINUTE DEFAULT NULL,
	listar							smallint		,
   PRIMARY KEY (proyecto,usuario_grupo_acc),
   FOREIGN KEY (nivel_acceso) REFERENCES apex_nivel_acceso (nivel_acceso)   ,
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_usuario_proyecto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: usuario
--: instancia: 1
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto                   char(15)    NOT NULL,
   usuario                    char(20) 	NOT NULL,
   usuario_grupo_acc          char(20)    NOT NULL,
   usuario_perfil_datos       char(20)    NOT NULL,
   PRIMARY KEY (proyecto,usuario),
   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (proyecto,usuario_grupo_acc) REFERENCES apex_usuario_grupo_acc (proyecto,usuario_grupo_acc)   ,
   FOREIGN KEY (proyecto,usuario_perfil_datos) REFERENCES apex_usuario_perfil_datos (proyecto,usuario_perfil_datos)   
);

--**************************************************************************************************
--**************************************************************************************************
--******************   ELEMENTOS CENTRALES (item, patron, clase y objeto)   ************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_patron
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto             char(15)    NOT NULL,
   patron               char(20)    NOT NULL,
   archivo              char(80)    NOT NULL,
   descripcion          char(250) ,
   descripcion_corta    char(40)  , -- NOT NULL,
	exclusivo_toba			smallint		,
	autodoc					smallint		,
   PRIMARY KEY (proyecto,patron),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_patron_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	patron_proyecto   		char(15)    NOT NULL,
   patron                  char(20)    NOT NULL,
   descripcion_breve       char(255) ,
   descripcion_larga       text         ,
   PRIMARY KEY (patron_proyecto,patron),
   FOREIGN KEY (patron_proyecto,patron) REFERENCES apex_patron (proyecto,patron)   
);
--#################################################################################################


CREATE TABLE apex_buffer
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto   		         char(15)    NOT NULL,
   buffer  		            serial,
   descripcion_corta       char(40)  , -- NOT NULL,
   descripcion 		      char(255)   NOT NULL,
   cuerpo            	   text,
   PRIMARY KEY (proyecto,buffer),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_item_zona
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: usuario
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto                   char(15)    NOT NULL,
   zona						      char(20)    NOT NULL,
   nombre                     char(80)    NOT NULL,
	clave_editable					char(100), -- Clave del EDITABLE manejado en la ZONA
   archivo                    char(80)    NOT NULL, -- Archivo donde reside la clase que representa la ZONA
   descripcion                char      ,
   PRIMARY KEY (proyecto,zona),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################


CREATE TABLE apex_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: apex_proyecto.orden, apex_item.padre_proyecto, apex_item.padre, apex_item.item
--: dump_order_by_from: apex_proyecto
--: dump_order_by_where: (apex_proyecto.proyecto = apex_item.proyecto)
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
   item_id				            serial,
	proyecto                   	char(15)    NOT NULL,
   item                       	char(60)    NOT NULL,
   padre_id		      				integer        , 
   padre_proyecto             	char(15)    NOT NULL,
   padre			               	char(60)    NOT NULL,
   carpeta                    	smallint     ,
   nivel_acceso               	smallint       NOT NULL,
   solicitud_tipo             	char(20)    NOT NULL,
   pagina_tipo_proyecto       	char(15)    NOT NULL,
   pagina_tipo                	char(20)    NOT NULL,
   nombre                     	char(80)    NOT NULL,
   descripcion                	char(255) ,
   actividad_buffer_proyecto  	char(15)    NOT NULL,
   actividad_buffer           	integer           NOT NULL,
   actividad_patron_proyecto  	char(15)    NOT NULL,
   actividad_patron           	char(20)    NOT NULL,
   actividad_accion			   	char(80)  ,
   menu                       	smallint     ,
   orden                      	float        ,
   solicitud_registrar        	smallint     ,
   solicitud_obs_tipo_proyecto	char(15)  ,
   solicitud_obs_tipo         	char(20)  ,
   solicitud_observacion      	char(90)	,
   solicitud_registrar_cron   	smallint     ,
   prueba_directorios         	smallint     ,
   zona_proyecto   			    	char(15)  ,
   zona            			    	char(20)  ,
	zona_orden							float			,
   zona_listar                  	smallint     ,
	imagen_recurso_origen			char(10)	,
	imagen								char(60)	,
   parametro_a                	char(100) ,
   parametro_b                	char(100) ,
   parametro_c                	char(100) ,
   publico								smallint		,
   usuario                    	char(20)  ,
   creacion                   	datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (proyecto,item),
   UNIQUE (proyecto,item),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
-- Como el DUMP devuelve a los registros desordenadors este constraint hay que definirlo al final

   FOREIGN KEY (actividad_buffer_proyecto,actividad_buffer) REFERENCES apex_buffer (proyecto,buffer)   ,
   FOREIGN KEY (actividad_patron_proyecto,actividad_patron) REFERENCES apex_patron (proyecto,patron)   ,
   FOREIGN KEY (solicitud_tipo) REFERENCES apex_solicitud_tipo (solicitud_tipo)   ,
   FOREIGN KEY (solicitud_obs_tipo_proyecto,solicitud_obs_tipo) REFERENCES apex_solicitud_obs_tipo (proyecto,solicitud_obs_tipo)   ,
   FOREIGN KEY (nivel_acceso) REFERENCES apex_nivel_acceso (nivel_acceso)   ,
   FOREIGN KEY (pagina_tipo_proyecto,pagina_tipo) REFERENCES apex_pagina_tipo (proyecto,pagina_tipo)   ,
   FOREIGN KEY (zona_proyecto,zona) REFERENCES apex_item_zona (proyecto,zona)   ,

   FOREIGN KEY (imagen_recurso_origen) REFERENCES apex_recurso_origen (recurso_origen)   
);
--#################################################################################################

CREATE TABLE apex_item_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( item_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
----------------------------------------- ----------------------------------------------------------
(  
   item_id							integer         , 
   item_proyecto       		   char(15)    NOT NULL,
   item                		   char(60)    NOT NULL,
   descripcion_breve   		   char(255) ,
   descripcion_larga   		   text         ,
   PRIMARY KEY (item_proyecto,item),
   FOREIGN KEY (item_proyecto,item) REFERENCES apex_item (proyecto,item)   
);
--#################################################################################################


CREATE TABLE apex_clase_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
   clase_tipo            		serial,
   descripcion_corta       	char(40)  		NOT NULL,
   descripcion 		      	char(255)   ,
   icono                      char(30)    ,
   orden                  		float       	,
   PRIMARY KEY (clase_tipo)
);
--#################################################################################################

CREATE TABLE apex_clase
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	proyecto   		            char(15)    NOT NULL,
   clase                      char(60)    NOT NULL,
   clase_tipo            		integer           NOT NULL, 
   archivo                    char(80)    NOT NULL,
   descripcion                char(250)   NOT NULL,
   descripcion_corta    		char(40)  , -- NOT NULL, 
   icono                      char(60)    NOT NULL, --> Icono con el que los objetos de la clase aparecen representados en las listas
	ancestro_proyecto 			char(15)  ,	--> Ancestro a considerar para incluir dependencias
   ancestro			          	char(60)  ,
   instanciador_id		      integer        , 
   instanciador_proyecto      char(15)  ,
   instanciador_item          char(60)  , --> Item del catalogo a invocar como instanciador de objetos de esta clase
   editor_id		      		integer        , 
   editor_proyecto     			char(15)  ,
   editor_item                char(60)  , --> Item del catalogo a invocar como editor de objetos de esta clase
	editor_ancestro_proyecto 	char(15)  ,	--> Ancestro a considerar para el EDITOR
   editor_ancestro          	char(60)  ,
   plan_dump_objeto           char(255)   NOT NULL, --> Lista ordenada de tablas que poseen la definicion del objeto (respetar FK!)
   sql_info                   text           NOT NULL, --> SQL que DUMPEA el estado del objeto
   doc_clase                  char(255) ,       --> GIF donde hay un Diagrama de clases.
   doc_db                     char(255) ,       --> GIF donde hay un DER de las tablas que necesita la clase.
   doc_sql                    char(255) ,       --> path al archivo que crea las tablas.
	vinculos							smallint		,			--> Indica si los objetos generados pueden tener vinculos
	autodoc							smallint		,
   parametro_a                char(255) ,
   parametro_b                char(255) ,
   parametro_c                char(255) ,
	exclusivo_toba					smallint		,
   PRIMARY KEY (proyecto,clase),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (clase_tipo) REFERENCES apex_clase_tipo (clase_tipo)   ,
   FOREIGN KEY (editor_ancestro_proyecto,editor_ancestro) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (ancestro_proyecto,ancestro) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (editor_proyecto,editor_item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (instanciador_proyecto,instanciador_item) REFERENCES apex_item (proyecto,item)   
);
--#################################################################################################

CREATE TABLE apex_clase_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	clase_proyecto             char(15)    NOT NULL,
   clase                      char(60)    NOT NULL,
   descripcion_breve          char(255) ,
   descripcion_larga          text         ,
   PRIMARY KEY (clase_proyecto,clase),
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   
);
--#################################################################################################

CREATE TABLE apex_clase_dependencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( clase_consumidora_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	clase_consumidora_proyecto char(15)    NOT NULL,
   clase_consumidora          char(60)    NOT NULL,
	identificador					char(20)		NOT NULL,
   descripcion                char(250) ,   
   clase_proveedora_proyecto  char(15)    NOT NULL,	-- Las dependencias pueden ser de esta clase o de una heredada
   clase_proveedora           char(60)    NOT NULL,
   PRIMARY KEY (clase_consumidora_proyecto,clase_consumidora,identificador),
   FOREIGN KEY (clase_consumidora_proyecto,clase_consumidora) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (clase_proveedora_proyecto,clase_proveedora) REFERENCES apex_clase (proyecto,clase)   
);
--#################################################################################################

CREATE TABLE apex_patron_dependencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	patron_proyecto            char(15)    NOT NULL,
   patron                     char(20)    NOT NULL,
   clase_proyecto 	         char(15)    NOT NULL,
   clase				            char(60)    NOT NULL,
   cantidad_minima            smallint       NOT NULL,
   cantidad_maxima            smallint       NOT NULL,
   descripcion                char(250) ,
   PRIMARY KEY (patron_proyecto,patron,clase_proyecto,clase),
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (patron_proyecto,patron) REFERENCES apex_patron (proyecto,patron)   
);
--#################################################################################################--

CREATE TABLE apex_objeto_categoria
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   char(15)    NOT NULL,
   objeto_categoria           char(30)    NOT NULL,
   descripcion                char(255) ,
   PRIMARY KEY (proyecto,objeto_categoria),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_solicitud_obj_obs_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   solicitud_obj_obs_tipo     char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   clase_proyecto 	         char(15)  ,
   clase                      char(60)  ,
   PRIMARY KEY (solicitud_obj_obs_tipo),
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   
);
--#################################################################################################


CREATE TABLE apex_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                      char(15)    NOT NULL,
   objeto                        serial,
   anterior                      char(20)  ,
   reflexivo                     smallint     ,
   clase_proyecto     				char(15)    NOT NULL,
   clase                         char(60)    NOT NULL,
	subclase								char(80)  ,
	subclase_archivo					char(80)  ,
   objeto_categoria_proyecto     char(15)  ,
   objeto_categoria              char(30)  ,
   nombre                        char(80)    NOT NULL,
   descripcion                   char(255) ,
   fuente_datos_proyecto         char(15)    NOT NULL,
   fuente_datos                  char(20)    NOT NULL,
   solicitud_registrar           smallint     ,
   solicitud_obj_obs_tipo        char(20)  ,
   solicitud_obj_observacion     char(255) ,
   parametro_a                   char(100) ,
   parametro_b                   char(100) ,
   parametro_c                   char(100) ,
   usuario                       char(20)  ,
   creacion                      datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (proyecto,objeto),
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (fuente_datos_proyecto,fuente_datos) REFERENCES apex_fuente_datos (proyecto,fuente_datos)   ,
   FOREIGN KEY (solicitud_obj_obs_tipo) REFERENCES apex_solicitud_obj_obs_tipo (solicitud_obj_obs_tipo)   ,
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,

   FOREIGN KEY (objeto_categoria_proyecto,objeto_categoria) REFERENCES apex_objeto_categoria (proyecto,objeto_categoria)   
);
--#################################################################################################--

CREATE TABLE apex_objeto_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_proyecto   				char(15)    NOT NULL,
   objeto           			      integer           NOT NULL,
   descripcion_breve			      char(255) ,
   descripcion_larga			      text         ,
   PRIMARY KEY (objeto_proyecto,objeto),
   FOREIGN KEY (objeto_proyecto,objeto) REFERENCES apex_objeto (proyecto,objeto)   
);
--#################################################################################################

CREATE TABLE apex_objeto_dependencias
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where:
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto 							char(15)    NOT NULL,
   objeto_consumidor  		    	integer           NOT NULL,
   objeto_proveedor        		integer           NOT NULL,
	identificador						char(20)		NOT NULL,
   PRIMARY KEY (proyecto,objeto_consumidor,identificador),
   FOREIGN KEY (proyecto,objeto_consumidor) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (proyecto,objeto_proveedor) REFERENCES apex_objeto (proyecto,objeto)   
);
--#################################################################################################

CREATE TABLE apex_item_objeto
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where:
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   item_id      						integer        , 
   proyecto   							char(15)    NOT NULL,
   item                 		   char(60)    NOT NULL,
   objeto               		   integer           NOT NULL,
   orden                		   smallint       NOT NULL,
   inicializar          		   smallint     ,
   PRIMARY KEY (proyecto,item,objeto),
   FOREIGN KEY (proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (proyecto,objeto) REFERENCES apex_objeto (proyecto,objeto)   
);
--#################################################################################################

CREATE TABLE apex_vinculo_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   vinculo_tipo    		        	char(10)    NOT NULL,
   descripcion_corta    			char(40)  , -- NOT NULL,
   descripcion     		        	char(255)   NOT NULL,
   PRIMARY KEY (vinculo_tipo)
);
--#################################################################################################--

CREATE TABLE apex_vinculo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( origen_item_proyecto = '%%' ) 
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   origen_item_id						integer        , 
   origen_item_proyecto   			char(15)    NOT NULL,
   origen_item                	char(60)    NOT NULL,
   origen_objeto_proyecto   		char(15)    NOT NULL,
   origen_objeto              	integer           NOT NULL,
   destino_item_id					integer        , 
   destino_item_proyecto 			char(15)    NOT NULL,
   destino_item            		char(60)    NOT NULL,
   destino_objeto_proyecto   		char(15)    NOT NULL,	-- Objeto que tiene que recibir el valor
   destino_objeto               	integer           NOT NULL,	-- 
   canal              				char(40)   ,			-- Clave utilizada para expandir el valor
   indice                   		char(20)    NOT NULL,	-- Indice para que el consumidor recupere el vinculo
   vinculo_tipo            		char(10)    NOT NULL, 	-- Como se habre el vinculo? popup, zoom, etc
   inicializacion          		char(100) ,			-- En el caso de un POPUP, tamao, etc.
   operacion               		smallint     , 		-- flag que indica si el vinculo implica una propagacion de la operacion o no (util para determinar permisos en cascada)
   texto                   		char(60)  ,			-- Texto del LINK
	imagen_recurso_origen			char(10)	,			-- Lugar donde se guardo la imagen: toba o proyecto
   imagen                  		char(60)  ,			-- path a la imagen
   PRIMARY KEY (origen_item_proyecto,origen_item,origen_objeto_proyecto,origen_objeto,destino_item_proyecto,destino_item,destino_objeto_proyecto,destino_objeto),

   FOREIGN KEY (origen_item_proyecto,origen_item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (origen_objeto_proyecto,origen_objeto) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (destino_item_proyecto,destino_item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (destino_objeto_proyecto,destino_objeto) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (imagen_recurso_origen) REFERENCES apex_recurso_origen (recurso_origen)   ,
   FOREIGN KEY (vinculo_tipo) REFERENCES apex_vinculo_tipo (vinculo_tipo)   
);
--#################################################################################################

CREATE TABLE apex_usuario_grupo_acc_item
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where:
--: zona: usuario, item
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto		    					char(15)    NOT NULL,
   usuario_grupo_acc  		     	char(20) 	NOT NULL,
   item_id								integer        , 
   item                    		char(60) 	NOT NULL,
   PRIMARY KEY (proyecto,usuario_grupo_acc,item),
   FOREIGN KEY (proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (proyecto,usuario_grupo_acc) REFERENCES apex_usuario_grupo_acc (proyecto,usuario_grupo_acc)   
);
  
 
--**************************************************************************************************
--**************************************************************************************************
--********************************   DOCUMENTACION del NUCLEO   ************************************
--**************************************************************************************************
--**************************************************************************************************



CREATE TABLE apex_nucleo_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   nucleo_tipo   	         		serial,
   descripcion_corta    			char(40)  	NOT NULL,
   descripcion             		char(250)   NOT NULL,
   orden                   		float       ,
   PRIMARY KEY (nucleo_tipo)
);
--#################################################################################################

CREATE TABLE apex_nucleo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto               			char(15)    NOT NULL,
   nucleo                  		char(60)    NOT NULL,
   nucleo_tipo		          		char(15)    NOT NULL,
   archivo                 		char(80)    NOT NULL,
   descripcion             		char(250)   NOT NULL,
   descripcion_corta    			char(40)  , -- NOT NULL,
   doc_nucleo              		char(255) ,       --> GIF donde hay un Diagrama
   doc_db                  		char(60)  ,       --> GIF donde hay un DER de las tablas que necesita la nucleo.
   doc_sql                 		char(60)  ,       --> path al archivo que crea las tablas.
	autodoc								smallint		,
   orden                   		float       ,
   PRIMARY KEY (proyecto,nucleo),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (nucleo_tipo) REFERENCES apex_nucleo_tipo (nucleo_tipo)   
);
--#################################################################################################

CREATE TABLE apex_nucleo_info
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( nucleo_proyecto = '%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   nucleo_proyecto         		char(15)    NOT NULL,
   nucleo                  		char(60)    NOT NULL,
   descripcion_breve       		char(255) ,
   descripcion_larga       		text         ,
   PRIMARY KEY (nucleo_proyecto,nucleo),
   FOREIGN KEY (nucleo_proyecto,nucleo) REFERENCES apex_nucleo (proyecto,nucleo)   
);
--#################################################################################################

--**************************************************************************************************
--**************************************************************************************************
--*************************************  Dimension  ************************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_dimension_tipo_perfil
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   dimension_tipo_perfil   			char(10)    NOT NULL,
   descripcion             			char(255)   NOT NULL,
   PRIMARY KEY (dimension_tipo_perfil)
);
--###################################################################################################

CREATE TABLE apex_dimension_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                			char(15)    NOT NULL,
   dimension_tipo          			char(20)    NOT NULL,
   nombre                  			char(40)    NOT NULL,
   descripcion             			char(255)   NOT NULL,
   parametros                 		char   	, 
   dimension_tipo_perfil   			char(10)    NOT NULL,
	editor_restric_id   					integer        , 
	item_editor_restric_proyecto     char(15)  ,    
	item_editor_restric     			char(60)  ,    
	ventana_editor_x						smallint		,
	ventana_editor_y						smallint		,
	exclusivo_toba							smallint		,
   PRIMARY KEY (proyecto,dimension_tipo),
   FOREIGN KEY (dimension_tipo_perfil) REFERENCES apex_dimension_tipo_perfil (dimension_tipo_perfil)   ,
   FOREIGN KEY (item_editor_restric_proyecto,item_editor_restric) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--###################################################################################################

CREATE TABLE apex_dimension_grupo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                			char(15)    NOT NULL,
   dimension_grupo         			char(10)    NOT NULL,
   nombre                  			char(80)    NOT NULL,
   descripcion             			char(80)  ,
   orden                   			float        ,
   PRIMARY KEY (proyecto,dimension_grupo),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--###################################################################################################

CREATE TABLE apex_dimension
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   		char(15)    NOT NULL,
   dimension                  		char(30)    NOT NULL,
   dimension_tipo_proyecto    		char(15)    NOT NULL,
   dimension_tipo             		char(20)    NOT NULL,
   dimension_grupo_proyecto   		char(15)  ,
   dimension_grupo            		char(10)  ,
   nombre                     		char(30)    NOT NULL,
   descripcion                		char(255)   NOT NULL,
   inicializacion             		char(255) ,
   fuente_datos_proyecto      		char(15)    NOT NULL,
   fuente_datos               		char(20)    NOT NULL,
   tabla_ref                  		char(80)  ,
   tabla_ref_clave            		char(80)  ,
   tabla_ref_desc             		char(80)  ,
   tabla_restric              		char(80)  ,
   PRIMARY KEY (proyecto,dimension),
   FOREIGN KEY (dimension_grupo_proyecto,dimension_grupo) REFERENCES apex_dimension_grupo (proyecto,dimension_grupo)   ,
   FOREIGN KEY (dimension_tipo_proyecto,dimension_tipo) REFERENCES apex_dimension_tipo (proyecto,dimension_tipo)   ,
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (fuente_datos_proyecto,fuente_datos) REFERENCES apex_fuente_datos (proyecto,fuente_datos)   
);
--###################################################################################################

CREATE TABLE apex_comparacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   comparacion             			char(20)    NOT NULL,
   descripcion             			char(255)   NOT NULL,
   plan_sql                			char(255)   NOT NULL,
   valor_1_des             			char(255) , 
   valor_2_des             			char(255) , 
   valor_3_des             			char(255) , 
   valor_4_des             			char(255) , 
   valor_5_des             			char(255) , 
   PRIMARY KEY (comparacion)
);
--###################################################################################################

CREATE TABLE apex_dimension_perfil_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( usuario_perfil_datos_proyecto = '%%' )
--: zona: dimension dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   usuario_perfil_datos_proyecto		char(15)    NOT NULL,
   usuario_perfil_datos    			char(20)    NOT NULL,
   dimension_proyecto        			char(15)    NOT NULL,
   dimension               			char(30)    NOT NULL,
   comparacion             			char(20)  ,
   valor_1                 			char(30)  , 
   valor_2                 			char(30)  , 
   valor_3                 			char(30)  , 
   valor_4                 			char(30)  , 
   valor_5                 			char(30)  , 
   PRIMARY KEY (usuario_perfil_datos_proyecto,usuario_perfil_datos,dimension_proyecto,dimension),
   FOREIGN KEY (dimension_proyecto,dimension) REFERENCES apex_dimension (proyecto,dimension)   ,
   FOREIGN KEY (usuario_perfil_datos_proyecto,usuario_perfil_datos) REFERENCES apex_usuario_perfil_datos (proyecto,usuario_perfil_datos)   ,
   FOREIGN KEY (comparacion) REFERENCES apex_comparacion (comparacion)   
);
--###################################################################################################
--##################################################################################################
--##################################################################################################
--################################  Registro de Solicitudes  #######################################
--##################################################################################################
--##################################################################################################



CREATE TABLE apex_solicitud
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( item_proyecto = '%%' )
--: dump_order_by: apex_solicitud.solicitud
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud	 					serial,
	solicitud_tipo					char(20)		NOT NULL,
	item_proyecto					char(15)		NOT NULL,
	item 								char(60)		NOT NULL,
   item_id							integer        , 
	momento							datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	tiempo_respuesta				float			,
   PRIMARY KEY (solicitud),
   FOREIGN KEY (item_proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (solicitud_tipo) REFERENCES apex_solicitud_tipo (solicitud_tipo)   
);
--###################################################################################################


CREATE TABLE apex_sesion_browser
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: sesion_browser
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	sesion_browser					serial,
	usuario							char(20) 	NOT NULL,
	proyecto							char(15) 	NOT NULL,
	ingreso							datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	egreso							datetime YEAR to SECOND,
	observaciones					char(255),
	php_id							char(100)	NOT NULL,
	ip									char(20)	,
	punto_acceso					char(80) ,
   PRIMARY KEY (sesion_browser), 
   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   ,

   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--###################################################################################################

CREATE TABLE apex_solicitud_browser
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: (apex_solicitud.solicitud = dd.solicitud_browser) AND (apex_solicitud.item_proyecto ='%%')
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_browser				integer				NOT NULL, 
	sesion_browser					integer				NOT NULL,
	ip									char(20)	,
   PRIMARY KEY (solicitud_browser),
   FOREIGN KEY (solicitud_browser) REFERENCES apex_solicitud (solicitud)    ,

   FOREIGN KEY (sesion_browser) REFERENCES apex_sesion_browser (sesion_browser)   

);
--###################################################################################################

CREATE TABLE apex_solicitud_wddx
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_wddx) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_wddx				integer					NOT NULL, 
	usuario						char(20) 		NOT NULL,
	ip								char(20)		,
	instancia					char(80) 		NOT NULL,
	instancia_usuario			char(20) 		NOT NULL,
	paquete						text				,
   PRIMARY KEY (solicitud_wddx),
   FOREIGN KEY (solicitud_wddx) REFERENCES apex_solicitud (solicitud)    ,

   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   

);
--###################################################################################################

CREATE TABLE apex_solicitud_consola
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_consola) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_consola			integer					NOT NULL, 
	usuario						char(20) 		NOT NULL,
	ip								char(20)		,
	llamada						char(255) 	,
	entorno						text				,
   PRIMARY KEY (solicitud_consola),
   FOREIGN KEY (solicitud_consola) REFERENCES apex_solicitud (solicitud)    ,

   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   

);
--###################################################################################################

CREATE TABLE apex_solicitud_cronometro
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud	 				integer				NOT NULL,
	marca							smallint			NOT NULL,
	nivel_ejecucion			char(15)		NOT NULL,
	texto							char(120),
	tiempo						float			,
   PRIMARY KEY (solicitud,marca),
   FOREIGN KEY (nivel_ejecucion) REFERENCES apex_nivel_ejecucion (nivel_ejecucion)    ,
   FOREIGN KEY (solicitud) REFERENCES apex_solicitud (solicitud)    

);
--###################################################################################################


CREATE TABLE apex_solicitud_observacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud_observacion) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_observacion			serial,
	solicitud_obs_tipo_proyecto	char(15)	,
	solicitud_obs_tipo				char(20)	,
	solicitud	 						integer				NOT NULL,
	observacion							char		,
   PRIMARY KEY (solicitud_observacion),
   FOREIGN KEY (solicitud_obs_tipo_proyecto,solicitud_obs_tipo) REFERENCES apex_solicitud_obs_tipo (proyecto,solicitud_obs_tipo)   ,
   FOREIGN KEY (solicitud) REFERENCES apex_solicitud (solicitud)    

);

--###################################################################################################


CREATE TABLE apex_solicitud_obj_observacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_from: apex_solicitud
--: dump_where: ((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.item_proyecto ='%%'))
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	solicitud_obj_observacion			serial,
	solicitud_obj_obs_tipo				char(20)	,
	solicitud		 						integer				NOT NULL,
   objeto_proyecto          			char(15)    NOT NULL,
	objeto									integer				NOT NULL,
	observacion								char		,
   PRIMARY KEY (solicitud_obj_observacion),
   FOREIGN KEY (solicitud_obj_obs_tipo) REFERENCES apex_solicitud_obj_obs_tipo (solicitud_obj_obs_tipo)   ,
   FOREIGN KEY (objeto_proyecto,objeto) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (solicitud) REFERENCES apex_solicitud (solicitud)    

);

--##################################################################################################
--##################################################################################################
--##################################  Monitoreo y control  #########################################
--##################################################################################################
--##################################################################################################

CREATE TABLE apex_log_sistema_tipo 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_sistema_tipo			char(20)		NOT NULL,
	descripcion					char(255)	NOT NULL,
   PRIMARY KEY (log_sistema_tipo)
);
------------------------


CREATE TABLE apex_log_sistema
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_sistema		 			serial,
	momento						datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	usuario						char(20) 	,
	log_sistema_tipo			char(20) 		NOT NULL,
	observaciones				text				,
   PRIMARY KEY (log_sistema),
   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   ,

   FOREIGN KEY (log_sistema_tipo) REFERENCES apex_log_sistema_tipo (log_sistema_tipo)   
);
--###################################################################################################-------------------


CREATE TABLE apex_log_error_login
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	log_error_login 			serial,
	momento						datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
	usuario						char(20) 						,
	clave							char(20) 						,
	ip								char(20)							,
	gravedad						smallint								,
	mensaje						text									,
	punto_acceso				char(80) 						,
   PRIMARY KEY (log_error_login)
);
--###################################################################################################-------------------

CREATE TABLE apex_log_ip_rechazada
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: solicitud
--: desc:
--: historica: 1
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	ip								char(20)								NOT NULL,
	momento						datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (ip)
);

--###################################################################################################-------------------
--**************************************************************************************************
--**************************************************************************************************
--*******************************************  NOTAS  **********************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_nota_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	nota_tipo                  char(20)    NOT NULL,
   descripcion                char(255)   NOT NULL,
   icono                      char(30)  ,
   PRIMARY KEY (nota_tipo)
);
--#################################################################################################


CREATE TABLE apex_patron_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	patron_nota             serial,
   nota_tipo               char(20)    NOT NULL,
   patron_proyecto   	   char(15)    NOT NULL,
   patron                  char(20)    NOT NULL,
   usuario_origen          char(20)  ,
   usuario_destino         char(20)  , 
   titulo                  char(50)  ,
   texto                   text         ,
   creacion                datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (patron_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (patron_proyecto,patron) REFERENCES apex_patron (proyecto,patron)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_item_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( item_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	item_nota           		   serial,
   nota_tipo           		   char(20)    NOT NULL,
   item_id   						integer        , 
   item_proyecto       		   char(15)    NOT NULL,
   item                		   char(60)    NOT NULL,
   usuario_origen      		   char(20)  ,
   usuario_destino     		   char(20)  , 
   titulo              		   char(50)  ,
   texto               		   text         ,
   creacion            		   datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (item_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (item_proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_clase_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	clase_nota       		      serial,
   nota_tipo            		char(20)    NOT NULL,
   clase_proyecto   	         char(15)    NOT NULL,
   clase                      char(60)    NOT NULL,
   usuario_origen             char(20)  ,
   usuario_destino            char(20)  , 
   titulo                     char(50)  ,
   texto                      text         ,
   creacion                   datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (clase_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_objeto_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_nota             		serial,
   nota_tipo               		char(20)    NOT NULL,
   objeto_proyecto   				char(15)    NOT NULL,
   objeto                  		integer           NOT NULL,
   usuario_origen          		char(20)  ,
   usuario_destino         		char(20)  , 
   titulo                  		char(50)  ,
   texto                   		text         ,
   creacion                		datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (objeto_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (objeto_proyecto,objeto) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################


CREATE TABLE apex_nucleo_nota
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( nucleo_proyecto = '%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   nucleo_nota             		serial,
   nota_tipo               		char(20)    NOT NULL,
   nucleo_proyecto         		char(15)    NOT NULL,
   nucleo                  		char(60)    NOT NULL,
   usuario_origen          		char(20)  ,
   usuario_destino         		char(20)  , 
   titulo                  		char(50)  ,
   texto                   		text         ,
   creacion                		datetime YEAR to SECOND DEFAULT CURRENT YEAR to SECOND,
   PRIMARY KEY (nucleo_nota),
   FOREIGN KEY (usuario_origen) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (usuario_destino) REFERENCES apex_usuario (usuario)   ,
   FOREIGN KEY (nucleo_proyecto,nucleo) REFERENCES apex_nucleo (proyecto,nucleo)   ,
   FOREIGN KEY (nota_tipo) REFERENCES apex_nota_tipo (nota_tipo)   
);
--#################################################################################################
--**************************************************************************************************
--**************************************************************************************************
--**************************************   Manejo de ERRORES  **************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_msg_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: general
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	msg_tipo                	 	char(20)    NOT NULL,
   descripcion                	char(255)   NOT NULL,
   icono                      	char(30)  ,
   PRIMARY KEY (msg_tipo)
);
--#################################################################################################


CREATE TABLE apex_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	msg 			    					serial,
   proyecto  							char(15)    NOT NULL,
   msg_tipo       					char(20)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (proyecto,msg),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_patron_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( patron_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	patron_msg     					serial,
   msg_tipo       					char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   patron_proyecto  					char(15)    NOT NULL,
   patron           					char(20)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (patron_msg),
   FOREIGN KEY (patron_proyecto,patron) REFERENCES apex_patron (proyecto,patron)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_item_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( item_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	item_msg          		   	serial,
   msg_tipo          		   	char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   item_id      						integer        , 
   item_proyecto       		   	char(15)    NOT NULL,
   item                		   	char(60)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (item_msg),
   UNIQUE (indice),
   FOREIGN KEY (item_proyecto,item) REFERENCES apex_item (proyecto,item)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_clase_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( clase_proyecto = '%%' )
--: zona: central
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	clase_msg      		      	serial,
   msg_tipo            				char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   clase_proyecto   	         	char(15)    NOT NULL,
   clase                      	char(60)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (clase_msg),
   UNIQUE (indice),
   FOREIGN KEY (clase_proyecto,clase) REFERENCES apex_clase (proyecto,clase)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################


CREATE TABLE apex_nucleo_msg
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( nucleo_proyecto = '%%' )
--: zona: nucleo
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   nucleo_msg        	     		serial,
   msg_tipo       	        		char(20)    NOT NULL,
	indice          					char(20)    NOT NULL,
   nucleo_proyecto         		char(15)    NOT NULL,
   nucleo                  		char(60)    NOT NULL,
   descripcion_corta            	char(50)  ,
   mensaje		                  char      ,
   mensaje_customizado           char      ,
   PRIMARY KEY (nucleo_msg),
   UNIQUE (indice),
   FOREIGN KEY (nucleo_proyecto,nucleo) REFERENCES apex_nucleo (proyecto,nucleo)   ,
   FOREIGN KEY (msg_tipo) REFERENCES apex_msg_tipo (msg_tipo)   
);
--#################################################################################################--**************************************************************************************************
--**************************************************************************************************
--***************************   DOCUMENTACION del MODELO de DATOS   ********************************
--**************************************************************************************************
--**************************************************************************************************

-- Estas son las tablas que mantienen la documentacion sobre el modelo de datos.
-- Se utilizan para generar planes de dumpeo y eliminacion.
-- Los registros que poseen se generan dinamicamente parseando scripts SQL (Este mismo por ejemplo...)

CREATE TABLE apex_mod_datos_zona
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: modelo_datos
--: desc: Organizadores conceptuales de tablas
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	char(15)    NOT NULL,
	zona 						        	char(15)    NOT NULL,
	descripcion  			       	char(255) ,
   PRIMARY KEY (proyecto,zona),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_dump
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: modelo_datos
--: desc: Modalidades de dumpeo
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	dump							     	char(20)    NOT NULL,
	descripcion                 	char(255) ,    
   PRIMARY KEY (dump)
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: modelo_datos
--: desc: Tablas que componen el modelo de datos
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	char(15)    NOT NULL,
	tabla									char(30)    NOT NULL,
	script								char(80)  ,
	orden									smallint			NOT NULL,
	descripcion							char(255) ,
	version								char(15)  ,
	historica							smallint		,
	instancia							smallint		,
	dump									char(20)  ,
	dump_where							char(255) ,
	dump_from							char(255) ,
	dump_order_by						char(255) ,
	dump_order_by_from				char(255) ,
	dump_order_by_where				char(255) ,
	extra_1								char(255) ,
	extra_2								char(255) ,
   PRIMARY KEY (proyecto,tabla),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   ,
   FOREIGN KEY (dump) REFERENCES apex_mod_datos_dump (dump)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla_columna
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Columnas de la tabla
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   tabla_proyecto               	char(15)    NOT NULL,
	tabla									char(30)    NOT NULL,
	columna								char(30)    NOT NULL,
	orden									float			,
	dump									smallint			DEFAULT 1 ,
	definicion							char		 ,
   PRIMARY KEY (tabla_proyecto,tabla,columna),
   FOREIGN KEY (tabla_proyecto,tabla) REFERENCES apex_mod_datos_tabla (proyecto,tabla)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_tabla_restric
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Constraints de la tabla
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   tabla_proyecto               	char(15)    NOT NULL,
	tabla									char(30)    NOT NULL,
	restriccion							char(30)  ,
	definicion							char		 ,
   PRIMARY KEY (tabla_proyecto,tabla,restriccion),
   FOREIGN KEY (tabla_proyecto,tabla) REFERENCES apex_mod_datos_tabla (proyecto,tabla)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_secuencia
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: zona: modelo_datos
--: desc: Secuencias
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   	char(15)    NOT NULL,
	secuencia							char(30)    NOT NULL,
	definicion							char(255)  ,
   PRIMARY KEY (proyecto,secuencia),
   FOREIGN KEY (proyecto) REFERENCES apex_proyecto (proyecto)   
);
--#################################################################################################

CREATE TABLE apex_mod_datos_zona_tabla
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( tabla_proyecto = '%%' )
--: zona: modelo_datos
--: desc: Asociacion de tablas con zonas
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   zona_proyecto             		char(15)    NOT NULL,
	zona             					char(15)    NOT NULL,
   tabla_proyecto            		char(15)    NOT NULL,
	tabla            					char(30)    NOT NULL,
   PRIMARY KEY (zona_proyecto,zona,tabla_proyecto,tabla),
   FOREIGN KEY (zona_proyecto,zona) REFERENCES apex_mod_datos_zona (proyecto,zona)   ,
   FOREIGN KEY (tabla_proyecto,tabla) REFERENCES apex_mod_datos_tabla (proyecto,tabla)   
);
--#################################################################################################
--**************************************************************************************************
--**************************************************************************************************
--**************************************  Hoja de Datos  *******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_hoja 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_hoja_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_hoja_proyecto          char(15)    NOT NULL,
   objeto_hoja                   integer           NOT NULL,
   sql                           text           NOT NULL,
	ancho									char(10)	,
   total_y                       smallint     ,
   total_x                       smallint     ,
   total_x_formato               integer			,
	columna_entrada					char(100),
   ordenable                     smallint     ,
   grafico                       char(30)  ,
   graf_columnas                 smallint     ,
   graf_filas                    smallint     ,
   graf_gen_invertir             smallint     ,
   graf_gen_invertible           smallint     ,
   graf_gen_ancho                smallint     ,
   graf_gen_alto                 smallint     ,
   PRIMARY KEY (objeto_hoja_proyecto,objeto_hoja),
   FOREIGN KEY (objeto_hoja_proyecto,objeto_hoja) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (grafico) REFERENCES apex_grafico (grafico)   ,
   FOREIGN KEY (total_x_formato) REFERENCES apex_columna_formato (columna_formato)   
);
--###################################################################################################

CREATE TABLE apex_objeto_hoja_directiva_ti 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_hoja_directiva_tipo    smallint       NOT NULL,
   nombre                        char(30)    NOT NULL,
   descripcion                   char(255)   NOT NULL,
   PRIMARY KEY (objeto_hoja_directiva_tipo)
);
--###################################################################################################

CREATE TABLE apex_objeto_hoja_directiva 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_hoja_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_hoja_proyecto          char(15)    NOT NULL,
   objeto_hoja                   integer           NOT NULL,
   columna                       smallint       NOT NULL,
   objeto_hoja_directiva_tipo    smallint       NOT NULL,
   nombre                        char(40)  ,
   columna_formato        			integer		    ,
   columna_estilo        			integer		    ,
   par_dimension_proyecto        char(15)  ,
   par_dimension                 char(30)  ,
   par_tabla                     char(40)  ,
   par_columna                   char(80)  ,
   PRIMARY KEY (objeto_hoja_proyecto,objeto_hoja,columna),
   FOREIGN KEY (objeto_hoja_proyecto,objeto_hoja) REFERENCES apex_objeto_hoja (objeto_hoja_proyecto,objeto_hoja)   ,
   FOREIGN KEY (objeto_hoja_directiva_tipo) REFERENCES apex_objeto_hoja_directiva_ti (objeto_hoja_directiva_tipo)   ,
   FOREIGN KEY (par_dimension_proyecto,par_dimension) REFERENCES apex_dimension (proyecto,dimension)   ,
   FOREIGN KEY (columna_estilo) REFERENCES apex_columna_estilo (columna_estilo)   ,
   FOREIGN KEY (columna_formato) REFERENCES apex_columna_formato (columna_formato)   
);
--###################################################################################################
--**************************************************************************************************
--**************************************************************************************************
--*****************************************  Filtro  ***********************************************
--**************************************************************************************************
--**************************************************************************************************


CREATE TABLE apex_objeto_filtro 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_filtro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_filtro_proyecto  char(15)    NOT NULL,
   objeto_filtro           integer           NOT NULL,
   dimension_proyecto      char(15)    NOT NULL,
   dimension               char(30)    NOT NULL,
   tabla                   char(40)  ,
   columna                 char(80)  ,
   orden                   float          NOT NULL,
   requerido               smallint     ,
   no_interactivo          smallint     ,
   PRIMARY KEY (objeto_filtro_proyecto,objeto_filtro,dimension_proyecto,dimension),
   FOREIGN KEY (objeto_filtro_proyecto,objeto_filtro) REFERENCES apex_objeto (proyecto,objeto)   ,
   FOREIGN KEY (dimension_proyecto,dimension) REFERENCES apex_dimension (proyecto,dimension)   
);
--###################################################################################################
--**************************************************************************************************
--**************************************************************************************************
--******************************************  ABM simple  ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_abms
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_abms_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_abms_proyecto       char(15)		NOT NULL,
   objeto_abms                integer    			NOT NULL,
   tabla                      char(40)    NOT NULL,
   titulo                     char(80)  ,       -- Titulo de la interface
   ev_mod_eliminar            smallint     ,       -- Pantalla de modificacion: Se permite eliminar registros ?
   ev_mod_estado_i            smallint     ,       -- Pantalla de modificacion: Hay un boton para volver al estado inicial?
   auto_reset                 smallint     ,       -- Pasar al estado de INSERT despues de un EVENTO
   PRIMARY KEY (objeto_abms_proyecto,objeto_abms),
   FOREIGN KEY (objeto_abms_proyecto,objeto_abms) REFERENCES apex_objeto (proyecto,objeto)   
);
--###################################################################################################

CREATE TABLE apex_objeto_abms_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_abms_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_abms_proyecto    char(15)		NOT NULL,
   objeto_abms             integer			   NOT NULL,
   identificador          	char(30)    NOT NULL,
   columnas                char(255)   NOT NULL,
   clave_primaria          smallint     ,			-- El contenido de este EF es parte de una clave primaria?
   obligatorio             smallint     ,			-- El contenido de este EF es obligatorio?
   elemento_formulario     char(30)    NOT NULL,
   inicializacion          char      ,
   orden                   float       	NOT NULL,
   etiqueta                char(40)  ,
   descripcion             char      ,
   desactivado             smallint     ,
   PRIMARY KEY (objeto_abms_proyecto,objeto_abms,identificador),
   FOREIGN KEY (objeto_abms_proyecto,objeto_abms) REFERENCES apex_objeto_abms (objeto_abms_proyecto,objeto_abms)   ,
   FOREIGN KEY (elemento_formulario) REFERENCES apex_elemento_formulario (elemento_formulario)   
);
--###################################################################################################
--**************************************************************************************************
--**************************************************************************************************
--******************************************     Lista    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_lista
-----------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_lista_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
-----------------------------------------------------------------------------------------------------
(
   objeto_lista_proyecto   char(15)		NOT NULL,
   objeto_lista            integer			   NOT NULL,
   titulo                  char(80)  ,
   subtitulo               char(80)  ,
   sql                     char      ,       -- SQL que arma el cuadro que permite elegir un registro a modificar
   col_ver                 char(255) ,
   col_titulos             char(255) ,
   col_formato             char(255) ,
   ancho                   smallint     ,
   ordenar                 smallint     ,
   exportar                smallint     ,
   vinculo_clave           char(80) ,       -- Columnas que poseen la clave, separadas por comas
   vinculo_indice				char(20)  ,       -- Titulo de la columna que tiene
   PRIMARY KEY (objeto_lista_proyecto,objeto_lista),
   FOREIGN KEY (objeto_lista_proyecto,objeto_lista) REFERENCES   apex_objeto (proyecto,objeto)   
);
--###################################################################################################--**************************************************************************************************
--**************************************************************************************************
--******************************************  GRAFICO  ******************************************
--**************************************************************************************************
--**************************************************************************************************


CREATE TABLE apex_objeto_grafico
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_grafico_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_grafico_proyecto   		char(15)		NOT NULL,
   objeto_grafico                integer			   NOT NULL,
   grafico                       char(30)    NOT NULL,
	sql									char		,
	inicializacion						char		,
   PRIMARY KEY (objeto_grafico_proyecto,objeto_grafico),
   FOREIGN KEY (grafico) REFERENCES apex_grafico (grafico)   ,
   FOREIGN KEY (objeto_grafico_proyecto,objeto_grafico) REFERENCES apex_objeto (proyecto,objeto)   
);
--###################################################################################################--**************************************************************************************************
--**************************************************************************************************
--******************************************     Cuadro    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_cuadro
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_cuadro_proyecto  	char(15)		NOT NULL,
   objeto_cuadro           	integer			   NOT NULL,
   titulo                  	char(80)  ,
   subtitulo               	char(80)  ,
   sql                     	char        NOT NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar
   columnas_clave					char(255)   NOT NULL,   -- Columnas que poseen la clave, separadas por comas
   archivos_callbacks      	char(100)  ,			-- Archivos donde estan las callbacks llamadas en las columnas
   ancho                   	char(10)  ,
   ordenar                 	smallint     ,
   paginar                 	smallint     ,
   tamano_pagina           	smallint     ,   
   eof_invisible           	smallint     ,   
   eof_customizado          	char(255),
   exportar		            	smallint     ,		-- Exportar XLS
   exportar_rtf            	smallint     ,		-- Exportar PDF
   pdf_propiedades          	char		,
   pdf_respetar_paginacion 	smallint     ,
   PRIMARY KEY (objeto_cuadro_proyecto,objeto_cuadro),
   FOREIGN KEY (objeto_cuadro_proyecto,objeto_cuadro) REFERENCES   apex_objeto (proyecto,objeto)   
);
--###################################################################################################


CREATE TABLE apex_objeto_cuadro_columna
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_cuadro_proyecto        char(15)    NOT NULL,
   objeto_cuadro                 integer           NOT NULL,
   orden				               float          NOT NULL,
   titulo                        char(40)    NOT NULL,
   columna_estilo    				integer		      NOT NULL,	-- Estilo de la columna
	columna_ancho						smallint		,			-- Ancho de columna para RTF
	ancho_html							smallint		,
	total									smallint		,			-- La columna lleva un total al final?
   valor_sql              			char(30)  ,			-- El valor de la columna HAY que tomarlo de RECORDSET
   valor_sql_formato    			integer		    ,			-- El valor del RECORDSET debe ser formateado
   valor_fijo                    char(30)  ,			-- La columna tomo un valor FIJO
	valor_proceso						integer			,			-- El valor de la columna es el resultado de procesar el registro
	valor_proceso_esp					char(40)	,			-- La callback de procesamiento es custom
	valor_proceso_parametros		char(155),			-- Parametros al procesamiento del registro
	vinculo_indice	      			char(20)  ,       -- Que vinculo asociado tengo que utilizar??
   par_dimension_proyecto        char(15)  ,			-- Hay una dimension asociada??
   par_dimension                 char(30)  ,
   par_tabla                     char(40)  ,
   par_columna                   char(80)  ,
   no_ordenar							smallint		,			-- No aplicarle interface de orden a la columna
   PRIMARY KEY (objeto_cuadro_proyecto,objeto_cuadro,orden),
   FOREIGN KEY (objeto_cuadro_proyecto,objeto_cuadro) REFERENCES apex_objeto_cuadro (objeto_cuadro_proyecto,objeto_cuadro)   ,
   FOREIGN KEY (par_dimension_proyecto,par_dimension) REFERENCES apex_dimension (proyecto,dimension)   ,
   FOREIGN KEY (valor_sql_formato) REFERENCES apex_columna_formato (columna_formato)   ,
   FOREIGN KEY (valor_proceso) REFERENCES apex_columna_proceso (columna_proceso)   ,
   FOREIGN KEY (columna_estilo) REFERENCES apex_columna_estilo (columna_estilo)   
);
--###################################################################################################

--**************************************************************************************************
--**************************************************************************************************
--******************************************     Cuadro    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_cuadro2
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_cuadro_proyecto  char(15)		NOT NULL,
   objeto_cuadro           integer			   NOT NULL,
   titulo                  char(80)  ,
   subtitulo               char(80)  ,
   sql                     char        NOT NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar
   columnas_clave				char(255)   NOT NULL,   -- Columnas que poseen la clave, separadas por comas
   archivos_callbacks      char(100)  ,			-- Archivos donde estan las callbacks llamadas en las columnas
   ancho                   char(10)  ,
   ordenar                 smallint     ,
   exportar                smallint     ,
   paginar                 smallint     ,
   exportar_rtf            smallint     ,
   tamano_pagina           smallint     ,   
   eof_invisible           smallint     ,   
   PRIMARY KEY (objeto_cuadro_proyecto,objeto_cuadro),
   FOREIGN KEY (objeto_cuadro_proyecto,objeto_cuadro) REFERENCES   apex_objeto (proyecto,objeto)   
);
--###################################################################################################


CREATE TABLE apex_objeto_cuadro2_columna
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_cuadro_proyecto        char(15)    NOT NULL,
   objeto_cuadro                 integer           NOT NULL,
   orden				               float          NOT NULL,
   titulo                        char(40)    NOT NULL,
   columna_estilo    				integer		      NOT NULL,	-- Estilo de la columna
	columna_ancho						smallint		,			-- Ancho de columna para RTF
	ancho_html							smallint		,
	total									smallint		,			-- La columna lleva un total al final?
   valor_sql              			char(30)  ,			-- El valor de la columna HAY que tomarlo de RECORDSET
   valor_sql_formato    			integer		    ,			-- El valor del RECORDSET debe ser formateado
   valor_fijo                    char(30)  ,			-- La columna tomo un valor FIJO
	valor_proceso						integer			,			-- El valor de la columna es el resultado de procesar el registro
	valor_proceso_esp					char(40)	,			-- La callback de procesamiento es custom
	valor_proceso_parametros		char(155),			-- Parametros al procesamiento del registro
	vinculo_indice	      			char(20)  ,       -- Que vinculo asociado tengo que utilizar??
   par_dimension_proyecto        char(15)  ,			-- Hay una dimension asociada??
   par_dimension                 char(30)  ,
   par_tabla                     char(40)  ,
   par_columna                   char(80)  ,
   no_ordenar							smallint		,			-- No aplicarle interface de orden a la columna
   PRIMARY KEY (objeto_cuadro_proyecto,objeto_cuadro,orden),
   FOREIGN KEY (objeto_cuadro_proyecto,objeto_cuadro) REFERENCES apex_objeto_cuadro2 (objeto_cuadro_proyecto,objeto_cuadro)   ,
   FOREIGN KEY (par_dimension_proyecto,par_dimension) REFERENCES apex_dimension (proyecto,dimension)   ,
   FOREIGN KEY (valor_sql_formato) REFERENCES apex_columna_formato (columna_formato)   ,
   FOREIGN KEY (valor_proceso) REFERENCES apex_columna_proceso (columna_proceso)   ,
   FOREIGN KEY (columna_estilo) REFERENCES apex_columna_estilo (columna_estilo)   
);
--###################################################################################################

--**************************************************************************************************
--**************************************************************************************************
--******************************************     plan    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_plan
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_plan_proyecto  		char(15)			NOT NULL,
	objeto_plan           		integer					NOT NULL,
	descripcion					char(255)			NOT NULL,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES   apex_objeto (proyecto,objeto)   
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_activ
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	posicion								smallint			NOT NULL,
	descripcion_corta					char(50)		NOT NULL,
	descripcion 						char		,
	fecha_inicio						date				NOT NULL,
	fecha_fin							date			,
	duracion								smallint		,
	anotacion							char(50)	,		
	altura								float			,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,posicion),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES apex_objeto_plan (objeto_plan_proyecto,objeto_plan)   
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_activ_usu
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	posicion							smallint				NOT NULL,
	usuario								char(20)		NOT NULL,
	observaciones						char		,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,posicion,usuario),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan,posicion) REFERENCES apex_objeto_plan_activ (objeto_plan_proyecto,objeto_plan,posicion)   ,
   FOREIGN KEY (usuario) REFERENCES apex_usuario (usuario)   
);
--#################################################################################################

CREATE TABLE apex_objeto_plan_hito
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	posicion								smallint			NOT NULL,
	descripcion_corta					char(50)		NOT NULL,
	descripcion 						char		,
	fecha									date				NOT NULL,
	anotacion							char(50)	,		
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,posicion),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES apex_objeto_plan (objeto_plan_proyecto,objeto_plan)   
);
--#################################################################################################


CREATE TABLE apex_objeto_plan_linea
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_plan_proyecto = '%%' )
--: zona: plan
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(  
	objeto_plan_proyecto				char(15)		NOT NULL,
	objeto_plan     					integer				NOT NULL,
	linea	 								serial,
	descripcion_corta					char(50)		NOT NULL,
	descripcion 						char		,
	fecha									date				NOT NULL,
	color									char(20)	,
	ancho									smallint		,
	estilo								char(20)	,
   PRIMARY KEY (objeto_plan_proyecto,objeto_plan,linea),
   FOREIGN KEY (objeto_plan_proyecto,objeto_plan) REFERENCES apex_objeto_plan (objeto_plan_proyecto,objeto_plan)   
);
--#################################################################################################
--**************************************************************************************************
--**************************************************************************************************
--**************************************  UT - Formulario  *****************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_ut_formulario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_ut_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_ut_formulario_proyecto       char(15)		NOT NULL,
   objeto_ut_formulario                integer    			NOT NULL,
   tabla                      			char(40)    NOT NULL,
   titulo                     			char(80)  ,       -- Titulo de la interface
   ev_mod_eliminar            			smallint     ,       -- Pantalla de modificacion: Se permite eliminar registros ?
   ev_mod_clave      	      			smallint     ,       -- Pantalla de modificacion: Hay un boton para volver al estado inicial?
   ev_mod_limpiar	            			smallint     ,       -- Pantalla de modificacion: Se permite limpiar el formulario?
	auto_reset									smallint     ,       -- Se resetea el formulario despues de transaccionar
   PRIMARY KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario),
   FOREIGN KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario) REFERENCES apex_objeto (proyecto,objeto)   
);
--###################################################################################################

CREATE TABLE apex_objeto_ut_formulario_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_where: ( objeto_ut_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_ut_formulario_proyecto    char(15)		NOT NULL,
   objeto_ut_formulario             integer			   NOT NULL,
   identificador          				char(30)    NOT NULL,
   columnas                			char(255)   NOT NULL,
   clave_primaria          			smallint     ,			-- El contenido de este EF es parte de una clave primaria?
   obligatorio             			smallint     ,			-- El contenido de este EF es obligatorio?
   elemento_formulario     			char(30)    NOT NULL,
   inicializacion          			char      ,
   orden                   			float       	NOT NULL,
   etiqueta                			char(40)  ,
   descripcion             			char      ,
   desactivado             			smallint     ,
   PRIMARY KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario,identificador),
   FOREIGN KEY (objeto_ut_formulario_proyecto,objeto_ut_formulario) REFERENCES apex_objeto_ut_formulario (objeto_ut_formulario_proyecto,objeto_ut_formulario)   ,
   FOREIGN KEY (elemento_formulario) REFERENCES apex_elemento_formulario (elemento_formulario)   
);
--###################################################################################################
