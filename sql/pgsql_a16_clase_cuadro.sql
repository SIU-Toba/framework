--**************************************************************************************************
--**************************************************************************************************
--******************************************     Cuadro    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_cuadro
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_cuadro
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_cuadro_proyecto  	varchar(15)		NOT NULL,
	objeto_cuadro           	int4			NOT NULL,
	titulo                  	varchar(80) 	NULL,
	subtitulo               	varchar(80) 	NULL,
	sql                     	varchar     	NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar
	columnas_clave				varchar(255)	NULL,   -- Columnas que poseen la clave, separadas por comas
	archivos_callbacks      	varchar(100)	NULL,			-- Archivos donde estan las callbacks llamadas en las columnas
	ancho                   	varchar(10) 	NULL,
	ordenar                 	smallint    	NULL,
	paginar                 	smallint    	NULL,
	tamano_pagina           	smallint    	NULL,   
	eof_invisible           	smallint    	NULL,   
	eof_customizado       		varchar(255)	NULL,
	exportar		           	smallint       	NULL,		-- Exportar XLS
	exportar_rtf            	smallint       	NULL,		-- Exportar PDF
	pdf_propiedades         	varchar			NULL,
	pdf_respetar_paginacion 	smallint       	NULL,  		-- ATENCION - Eliminar a futuro
	asociacion_columnas			varchar(100)	NULL,
	ev_seleccion				smallint		NULL,		-- EI cuadro, lupa -> seleccion
	ev_eliminar					smallint		NULL,		-- EI cuadro, tacho -> eliminacion
	dao_nucleo_proyecto			varchar(15)		NULL,
	dao_nucleo					varchar(60)		NULL,
	dao_metodo					varchar(80)		NULL,
	desplegable					smallint		NULL,
	desplegable_activo			smallint		NULL,
	scroll						smallint		NULL,
	scroll_alto					varchar(10)		NULL,
	CONSTRAINT  "apex_objeto_cuadro_pk" PRIMARY KEY ("objeto_cuadro_proyecto","objeto_cuadro"),
	CONSTRAINT  "apex_objeto_cuadro_fk_objeto"  FOREIGN KEY ("objeto_cuadro_proyecto","objeto_cuadro") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_objeto_cuadro_fk_nucleo" FOREIGN KEY ("dao_nucleo_proyecto","dao_nucleo") REFERENCES	"apex_nucleo" ("proyecto","nucleo")	ON	DELETE NO ACTION ON UPDATE	NO	ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_objeto_cuadro_columna
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_cuadro, orden
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_cuadro_proyecto        	varchar(15)		NOT NULL,
	objeto_cuadro                 	int4       		NOT NULL,
	orden				            float      		NOT NULL,
	titulo                        	varchar(40)		NOT NULL,
	columna_estilo    				int4		    NOT NULL,	-- Estilo de la columna
	columna_ancho					varchar(10)		NULL,			-- Ancho de columna para RTF
	ancho_html						varchar(10)		NULL,
	total							smallint		NULL,			-- La columna lleva un total al final?
	valor_sql              			varchar(30)    	NULL,			-- El valor de la columna HAY que tomarlo de RECORDSET
	valor_sql_formato    			int4		    NULL,			-- El valor del RECORDSET debe ser formateado
	valor_fijo                    	varchar(30)    	NULL,			-- La columna tomo un valor FIJO
	valor_proceso					int4			NULL,			-- El valor de la columna es el resultado de procesar el registro
	valor_proceso_esp				varchar(40)		NULL,			-- La callback de procesamiento es custom
	valor_proceso_parametros		varchar(155)	NULL,			-- Parametros al procesamiento del registro
	vinculo_indice	      			varchar(20) 	NULL,       -- Que vinculo asociado tengo que utilizar??
	par_dimension_proyecto        	varchar(15) 	NULL,			-- Hay una dimension asociada??
	par_dimension                 	varchar(30) 	NULL,
	par_tabla                     	varchar(40) 	NULL,
	par_columna                   	varchar(80) 	NULL,
	no_ordenar						smallint		NULL,			-- No aplicarle interface de orden a la columna
	mostrar_xls						smallint		NULL,
	mostrar_pdf						smallint		NULL,
	pdf_propiedades          		varchar			NULL,
	desabilitado					smallint		NULL,
	CONSTRAINT  "apex_obj_cuadro_pk" PRIMARY KEY ("objeto_cuadro_proyecto","objeto_cuadro","orden"),
	CONSTRAINT  "apex_obj_cuadro_fk_objeto_cuadro" FOREIGN KEY ("objeto_cuadro_proyecto","objeto_cuadro") REFERENCES "apex_objeto_cuadro" ("objeto_cuadro_proyecto","objeto_cuadro") ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_cuadro_fk_dimension" FOREIGN KEY ("par_dimension_proyecto","par_dimension") REFERENCES "apex_dimension" ("proyecto","dimension") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_cuadro_fk_formato" FOREIGN KEY ("valor_sql_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_cuadro_fk_proceso" FOREIGN KEY ("valor_proceso") REFERENCES "apex_columna_proceso" ("columna_proceso") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_cuadro_fk_estilo" FOREIGN KEY ("columna_estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################