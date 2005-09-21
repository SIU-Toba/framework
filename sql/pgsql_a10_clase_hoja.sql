--**************************************************************************************************
--**************************************************************************************************
--**************************************  Hoja de Datos  *******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_hoja 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_hoja
--: dump_where: ( objeto_hoja_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_hoja_proyecto          varchar(15)    NOT NULL,
   objeto_hoja                   int4           NOT NULL,
   sql                           text           NOT NULL,
	ancho									varchar(10)		NULL,
   total_y                       smallint       NULL,
   total_x                       smallint       NULL,
   total_x_formato               int4				NULL,
	columna_entrada					varchar(100)	NULL,
   ordenable                     smallint       NULL,
   grafico                       varchar(30)    NULL,
   graf_columnas                 smallint       NULL,
   graf_filas                    smallint       NULL,
   graf_gen_invertir             smallint       NULL,
   graf_gen_invertible           smallint       NULL,
   graf_gen_ancho                smallint       NULL,
   graf_gen_alto                 smallint       NULL,
   CONSTRAINT  "apex_obj_hoj_pk"  PRIMARY KEY ("objeto_hoja_proyecto","objeto_hoja"),
   CONSTRAINT  "apex_obj_hoj_fk_objeto" FOREIGN KEY ("objeto_hoja_proyecto","objeto_hoja") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_obj_hoj_fk_grafico" FOREIGN KEY ("grafico") REFERENCES "apex_grafico" ("grafico") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_obj_hoj_fk_formato" FOREIGN KEY ("total_x_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_objeto_hoja_directiva_ti 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: proyecto
--: dump_order_by: objeto_hoja_directiva_tipo
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_hoja_directiva_tipo    smallint       NOT NULL,
   nombre                        varchar(30)    NOT NULL,
   descripcion                   varchar(255)   NOT NULL,
   CONSTRAINT  "apex_obj_hoja_dir_tipo_pk" PRIMARY KEY ("objeto_hoja_directiva_tipo")
);
--###################################################################################################

CREATE TABLE apex_objeto_hoja_directiva 
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_hoja, columna
--: dump_where: ( objeto_hoja_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
   objeto_hoja_proyecto          varchar(15)    NOT NULL,
   objeto_hoja                   int4           NOT NULL,
   columna                       smallint       NOT NULL,
   objeto_hoja_directiva_tipo    smallint       NOT NULL,
   nombre                        varchar(40)    NULL,
   columna_formato        			int4		      NULL,
   columna_estilo        			int4		      NULL,
   par_dimension_proyecto        varchar(15)    NULL,
   par_dimension                 varchar(30)    NULL,
   par_tabla                     varchar(40)    NULL,
   par_columna                   varchar(80)    NULL,
   CONSTRAINT  "apex_obj_hoja_dir_pk" PRIMARY KEY ("objeto_hoja_proyecto","objeto_hoja","columna"),
   CONSTRAINT  "obj_hoja_dir_fk_objeto_hoja" FOREIGN KEY ("objeto_hoja_proyecto","objeto_hoja") REFERENCES "apex_objeto_hoja" ("objeto_hoja_proyecto","objeto_hoja") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_obj_hoja_dir_fk_tipo" FOREIGN KEY ("objeto_hoja_directiva_tipo") REFERENCES "apex_objeto_hoja_directiva_ti" ("objeto_hoja_directiva_tipo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "obj_hoja_dir_fk_dimension" FOREIGN KEY ("par_dimension_proyecto","par_dimension") REFERENCES "apex_dimension" ("proyecto","dimension") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_obj_hoja_dir_fk_estilo" FOREIGN KEY ("columna_estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
   CONSTRAINT  "apex_obj_hoja_dir_fk_formato" FOREIGN KEY ("columna_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
