
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
