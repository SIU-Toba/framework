--**************************************************************************************************
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

