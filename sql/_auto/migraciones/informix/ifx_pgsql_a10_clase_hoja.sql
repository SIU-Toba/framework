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
