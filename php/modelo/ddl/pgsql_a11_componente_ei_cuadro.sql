--**************************************************************************************************
--**************************************************************************************************
--******************************************     Cuadro    ******************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_cuadro
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_cuadro_proyecto
--: dump_clave_componente: objeto_cuadro
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
	clave_dbr					smallint		NULL,
	archivos_callbacks      	varchar(100)	NULL,			-- Archivos donde estan las callbacks llamadas en las columnas
	ancho                   	varchar(10) 	NULL,
	ordenar                 	smallint    	NULL,
	paginar                 	smallint    	NULL,
	tamano_pagina           	smallint    	NULL,
	tipo_paginado				varchar(1)  	NULL,
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
	dao_parametros				varchar(150)	NULL,
	desplegable					smallint		NULL,
	desplegable_activo			smallint		NULL,
	scroll						smallint		NULL,
	scroll_alto					varchar(10)		NULL,
	cc_modo						varchar(1)		NULL,		-- Tipo de cortes de control
	cc_modo_anidado_colap		smallint		NULL,		-- Tipo anidado: colapsar niveles
	cc_modo_anidado_totcol		smallint		NULL,		-- Tipo anidado: Desplegar columnas horizontalmente
	cc_modo_anidado_totcua		smallint		NULL,		-- Tipo anidado: El total del ultimo nivel adosarlo al cuadro
	CONSTRAINT  "apex_objeto_cuadro_pk" PRIMARY KEY ("objeto_cuadro_proyecto","objeto_cuadro"),
	CONSTRAINT  "apex_objeto_cuadro_fk_objeto"  FOREIGN KEY ("objeto_cuadro_proyecto","objeto_cuadro") REFERENCES   "apex_objeto" ("proyecto","objeto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_obj_ei_cuadro_cc_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_cuadro_cc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_cuadro_proyecto
--: dump_clave_componente: objeto_cuadro
--: dump_order_by: objeto_cuadro, objeto_cuadro_cc
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_cuadro_proyecto        	varchar(15)		NOT NULL,
	objeto_cuadro                 	int4       		NOT NULL,
	objeto_cuadro_cc				int4			DEFAULT nextval('"apex_obj_ei_cuadro_cc_seq"'::text) NOT NULL, 
	identificador					varchar(15)		NULL,			-- Para declarar funciones que redefinan la cabecera o el pie del corte
	descripcion						varchar(30)		NULL,
	orden				            float      		NOT NULL,
	columnas_id	    				varchar(200)	NOT NULL,		-- Columnas utilizada para cortar
	columnas_descripcion			varchar(200)	NOT NULL,		-- Columnas utilizada como titulo del corte
	pie_contar_filas				varchar(10)		NULL,
	pie_mostrar_titular				smallint		NULL,			-- Cabecera del PIE
	pie_mostrar_titulos				smallint		NULL,			-- Repetir los titulos de las columnas
	imp_paginar						smallint		NULL,		
	CONSTRAINT  "apex_obj_cuadro_cc_pk" PRIMARY KEY ("objeto_cuadro_proyecto","objeto_cuadro","objeto_cuadro_cc"),
	CONSTRAINT  "apex_obj_cuadro_cc_uq" UNIQUE ("objeto_cuadro_proyecto","objeto_cuadro","identificador"),
	CONSTRAINT  "apex_obj_cuadro_cc_fk_objeto_cuadro" FOREIGN KEY ("objeto_cuadro_proyecto","objeto_cuadro") REFERENCES "apex_objeto_cuadro" ("objeto_cuadro_proyecto","objeto_cuadro") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_obj_ei_cuadro_col_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_ei_cuadro_columna
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_cuadro_proyecto
--: dump_clave_componente: objeto_cuadro
--: dump_order_by: objeto_cuadro, objeto_cuadro_col
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_cuadro_proyecto        	varchar(15)		NOT NULL,
	objeto_cuadro                 	int4       		NOT NULL,
	objeto_cuadro_col				int4			DEFAULT nextval('"apex_obj_ei_cuadro_col_seq"'::text) NOT NULL, 
	clave          					varchar(40)    	NOT NULL,		
	orden				            float      		NOT NULL,
	titulo                        	varchar(100)	NULL,
	estilo_titulo                   varchar(100)	DEFAULT 'ei-cuadro-col-tit' NULL,
	estilo    						int4		    NOT NULL,	
	ancho							varchar(10)		NULL,		
	formateo   						int4		    NULL,		
	vinculo_indice	      			varchar(20) 	NULL,       
	no_ordenar						smallint		NULL,		
	mostrar_xls						smallint		NULL,
	mostrar_pdf						smallint		NULL,
	pdf_propiedades          		varchar			NULL,
	desabilitado					smallint		NULL,
	total							smallint		NULL,		
	total_cc						varchar(100)	NULL,			-- La columna lleva un total al final?
	usar_vinculo					smallint			NULL,
	vinculo_carpeta					varchar(60)			NULL,
	vinculo_item					varchar(60)			NULL,
	vinculo_popup					smallint			NULL,
	vinculo_popup_param				varchar(100)		NULL,
	vinculo_target					varchar(40)			NULL,
	vinculo_celda					varchar(40)			NULL,
	CONSTRAINT  "apex_obj_ei_cuadro_pk" PRIMARY KEY ("objeto_cuadro_proyecto","objeto_cuadro","objeto_cuadro_col"),
	CONSTRAINT  "apex_obj_ei_cuadro_fk_objeto_cuadro" FOREIGN KEY ("objeto_cuadro_proyecto","objeto_cuadro") REFERENCES "apex_objeto_cuadro" ("objeto_cuadro_proyecto","objeto_cuadro") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_ei_cuadro_fk_formato" FOREIGN KEY ("formateo") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_ei_cuadro_fk_estilo" FOREIGN KEY ("estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################