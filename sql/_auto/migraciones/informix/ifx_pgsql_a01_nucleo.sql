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
