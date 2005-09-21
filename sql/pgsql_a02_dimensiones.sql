
--**************************************************************************************************
--**************************************************************************************************
--*************************************  Dimension  ************************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_dimension_tipo_perfil
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: dimension_tipo_perfil
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   dimension_tipo_perfil   			varchar(10)    NOT NULL,
   descripcion             			varchar(255)   NOT NULL,
   CONSTRAINT  "apex_dimension_perfil_pk" PRIMARY KEY ("dimension_tipo_perfil")
);
--###################################################################################################

CREATE TABLE apex_dimension_tipo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: dimension_tipo
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                			varchar(15)    NOT NULL,
   dimension_tipo          			varchar(20)    NOT NULL,
   nombre                  			varchar(40)    NOT NULL,
   descripcion             			varchar(255)   NOT NULL,
   parametros                 		varchar   		NULL, 
   dimension_tipo_perfil   			varchar(10)    NOT NULL,
	editor_restric_id   					int4        	NULL, 
	item_editor_restric_proyecto     varchar(15)    NULL,    
	item_editor_restric     			varchar(60)    NULL,    
	ventana_editor_x						smallint			NULL,
	ventana_editor_y						smallint			NULL,
	exclusivo_toba							smallint			NULL,
   CONSTRAINT  "apex_dim_tipo_pk" PRIMARY KEY ("proyecto","dimension_tipo"),
   CONSTRAINT  "apex_dim_tipo_fk_perfil" FOREIGN KEY ("dimension_tipo_perfil") REFERENCES "apex_dimension_tipo_perfil" ("dimension_tipo_perfil") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_dim_tipo_fk_editor" FOREIGN KEY ("item_editor_restric_proyecto","item_editor_restric") REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_dim_tipo_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_dimension_grupo
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: dimension_grupo
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                			varchar(15)    NOT NULL,
   dimension_grupo         			varchar(10)    NOT NULL,
   nombre                  			varchar(80)    NOT NULL,
   descripcion             			varchar(80)    NULL,
   orden                   			float          NULL,
   CONSTRAINT  "apex_dim_grupo_pk" PRIMARY KEY ("proyecto","dimension_grupo"),
   CONSTRAINT  "apex_dim_grupo_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_dimension
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: dimension
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   proyecto                   		varchar(15)    NOT NULL,
   dimension                  		varchar(30)    NOT NULL,
   dimension_tipo_proyecto    		varchar(15)    NOT NULL,
   dimension_tipo             		varchar(20)    NOT NULL,
   dimension_grupo_proyecto   		varchar(15)    NULL,
   dimension_grupo            		varchar(10)    NULL,
   nombre                     		varchar(30)    NOT NULL,
   descripcion                		varchar(255)   NOT NULL,
   inicializacion             		varchar		   NULL,
   fuente_datos_proyecto      		varchar(15)    NOT NULL,
   fuente_datos               		varchar(20)    NOT NULL,
   tabla_ref                  		varchar(80)    NULL,
   tabla_ref_clave            		varchar(80)    NULL,
   tabla_ref_desc             		varchar(80)    NULL,
   tabla_restric              		varchar(80)    NULL,
   CONSTRAINT  "apex_dim_pk" PRIMARY KEY ("proyecto","dimension"),
   CONSTRAINT  "apex_dim_fk_grupo" FOREIGN KEY ("dimension_grupo_proyecto","dimension_grupo") REFERENCES "apex_dimension_grupo" ("proyecto","dimension_grupo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_dim_fk_" FOREIGN KEY ("dimension_tipo_proyecto","dimension_tipo") REFERENCES "apex_dimension_tipo" ("proyecto","dimension_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_dim_fk_proyecto" FOREIGN KEY ("proyecto") REFERENCES "apex_proyecto" ("proyecto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_dim_fk_fuente_datos" FOREIGN KEY ("fuente_datos_proyecto","fuente_datos") REFERENCES "apex_fuente_datos" ("proyecto","fuente_datos") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_comparacion
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: comparacion
--: zona: dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   comparacion             			varchar(20)    NOT NULL,
   descripcion             			varchar(255)   NOT NULL,
   plan_sql                			varchar(255)   NOT NULL,
   valor_1_des             			varchar(255)   NULL, 
   valor_2_des             			varchar(255)   NULL, 
   valor_3_des             			varchar(255)   NULL, 
   valor_4_des             			varchar(255)   NULL, 
   valor_5_des             			varchar(255)   NULL, 
   CONSTRAINT  "apex_comparacion_pk" PRIMARY KEY ("comparacion")
);
--###################################################################################################

CREATE TABLE apex_dimension_perfil_datos
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: usuario_perfil_datos, dimension
--: dump_where: ( usuario_perfil_datos_proyecto = '%%' )
--: zona: dimension dimension
--: desc:
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   usuario_perfil_datos_proyecto		varchar(15)    NOT NULL,
   usuario_perfil_datos    			varchar(20)    NOT NULL,
   dimension_proyecto        			varchar(15)    NOT NULL,
   dimension               			varchar(30)    NOT NULL,
   comparacion             			varchar(20)    NULL,
   valor_1                 			varchar(30)    NULL, 
   valor_2                 			varchar(30)    NULL, 
   valor_3                 			varchar(30)    NULL, 
   valor_4                 			varchar(30)    NULL, 
   valor_5                 			varchar(30)    NULL, 
   CONSTRAINT  "apex_dim_usu_pk"  PRIMARY KEY ("usuario_perfil_datos_proyecto","usuario_perfil_datos","dimension_proyecto","dimension"),
   CONSTRAINT  "apex_dim_usu_fk_dimension" FOREIGN KEY ("dimension_proyecto","dimension") REFERENCES "apex_dimension" ("proyecto","dimension") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_dim_usu_fk_usuario" FOREIGN KEY ("usuario_perfil_datos_proyecto","usuario_perfil_datos") REFERENCES "apex_usuario_perfil_datos" ("proyecto","usuario_perfil_datos") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_dim_usu_fk_comp" FOREIGN KEY ("comparacion") REFERENCES "apex_comparacion" ("comparacion") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
