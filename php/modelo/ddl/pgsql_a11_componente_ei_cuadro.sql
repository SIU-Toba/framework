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
	objeto_cuadro           	int8			NOT NULL,
	titulo                  	TEXT 	NULL,
	subtitulo               	TEXT 	NULL,
	sql                     	TEXT     	NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar
	columnas_clave				TEXT			NULL,   -- Columnas que poseen la clave, separadas por comas
	columna_descripcion			TEXT	NULL, --Columna que mantiene la descripcion para respuesta popup
	clave_dbr					smallint		NULL,
	archivos_callbacks      	TEXT	NULL,			-- Archivos donde estan las callbacks llamadas en las columnas
	ancho                   	varchar(10) 	NULL,
	ordenar                 	smallint    	NULL,
	paginar                 	smallint    	NULL,
	tamano_pagina           	smallint    	NULL,
	tipo_paginado				varchar(1)  	NULL,
	mostrar_total_registros		SMALLINT NOT NULL DEFAULT 0,
	eof_invisible           	smallint    	NULL,   
	eof_customizado       		varchar			NULL,
	siempre_con_titulo				SMALLINT	NOT NULL DEFAULT 0,
	exportar_paginado	      	smallint    	NULL,		-- Limita el paginado a la salida html	
	exportar		           	smallint       	NULL,		-- Exportar XLS
	exportar_rtf            	smallint       	NULL,		-- Exportar PDF
	pdf_propiedades         	TEXT			NULL,
	pdf_respetar_paginacion 	smallint       	NULL,  		-- ATENCION - Eliminar a futuro
	asociacion_columnas			TEXT	NULL,
	ev_seleccion				smallint		NULL,		-- EI cuadro, lupa -> seleccion
	ev_eliminar					smallint		NULL,		-- EI cuadro, tacho -> eliminacion
	dao_nucleo_proyecto			varchar(15)		NULL,
	dao_nucleo					varchar(60)		NULL,
	dao_metodo					varchar(80)		NULL,
	dao_parametros				TEXT	NULL,
	desplegable					smallint		NULL,
	desplegable_activo			smallint		NULL,
	scroll						smallint		NULL,
	scroll_alto					varchar(10)		NULL,
	cc_modo						varchar(1)		NULL,		-- Tipo de cortes de control
	cc_modo_anidado_colap		smallint		NULL,		-- Tipo anidado: colapsar niveles
	cc_modo_anidado_totcol		smallint		NULL,		-- Tipo anidado: Desplegar columnas horizontalmente
	cc_modo_anidado_totcua		smallint		NULL,		-- Tipo anidado: El total del ultimo nivel adosarlo al cuadro
	CONSTRAINT  "apex_objeto_cuadro_pk" PRIMARY KEY ("objeto_cuadro", "objeto_cuadro_proyecto"),
	CONSTRAINT  "apex_objeto_cuadro_fk_objeto"  FOREIGN KEY ("objeto_cuadro", "objeto_cuadro_proyecto") REFERENCES   "apex_objeto" ("objeto", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
	objeto_cuadro                 	int8       		NOT NULL,
	objeto_cuadro_cc				int8			DEFAULT nextval('"apex_obj_ei_cuadro_cc_seq"'::text) NOT NULL, 
	identificador					TEXT		NULL,			-- Para declarar funciones que redefinan la cabecera o el pie del corte
	descripcion						TEXT		NULL,
	orden				            float      		NOT NULL,
	columnas_id	    				TEXT	NOT NULL,		-- Columnas utilizada para cortar
	columnas_descripcion			TEXT	NOT NULL,		-- Columnas utilizada como titulo del corte
	pie_contar_filas				varchar(10)		NULL,
	pie_mostrar_titular				smallint		NULL,			-- Cabecera del PIE
	pie_mostrar_titulos				smallint		NULL,			-- Repetir los titulos de las columnas
	imp_paginar						smallint		NULL,		
	modo_inicio_colapsado			smallint		NULL DEFAULT 0,			-- El corte de este nivel se inicia colapsado
	CONSTRAINT  "apex_obj_cuadro_cc_pk" PRIMARY KEY ("objeto_cuadro_cc", "objeto_cuadro_proyecto","objeto_cuadro"),
	CONSTRAINT  "apex_obj_cuadro_cc_uq" UNIQUE ("objeto_cuadro_proyecto","objeto_cuadro","identificador"),
	CONSTRAINT  "apex_obj_cuadro_cc_fk_objeto_cuadro" FOREIGN KEY ("objeto_cuadro", "objeto_cuadro_proyecto") REFERENCES "apex_objeto_cuadro" ("objeto_cuadro", "objeto_cuadro_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
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
	objeto_cuadro                 	int8       		NOT NULL,
	objeto_cuadro_col				int8			DEFAULT nextval('"apex_obj_ei_cuadro_col_seq"'::text) NOT NULL, 
	clave          					varchar(80)    	NOT NULL,		
	orden				            float      		NOT NULL,
	titulo                        	TEXT			NULL,
	estilo_titulo                   TEXT			DEFAULT 'ei-cuadro-col-tit' NULL,
	estilo    						TEXT		    NULL,	
	ancho							varchar(10)		NULL,		
	formateo   						int8		    NULL,		
	vinculo_indice	      			varchar(20) 	NULL,       
	no_ordenar						smallint		NULL,		
	mostrar_xls						smallint		NULL,
	mostrar_pdf						smallint		NULL,
	pdf_propiedades          		TEXT			NULL,
	desabilitado					smallint		NULL,
	total							smallint		NULL,		
	total_cc						TEXT	NULL,			-- La columna lleva un total al final?
	usar_vinculo					smallint			NULL,
	vinculo_carpeta					varchar(60)			NULL,			--OBSOLETO
	vinculo_item					varchar(60)			NULL,				--OBSOLETO
	vinculo_popup					smallint			NULL,				--OBSOLETO
	vinculo_popup_param				varchar(100)		NULL,	--OBSOLETO
	vinculo_target					varchar(40)			NULL,				--OBSOLETO
	vinculo_celda					varchar(40)			NULL,				--OBSOLETO
	vinculo_servicio				varchar(100)		NULL,			 --OBSOLETO
	permitir_html					smallint			NULL,		-- Proteccion contra ataques XSS
	grupo							TEXT		NULL,
	evento_asociado			bigint		NULL,
	CONSTRAINT  "apex_obj_ei_cuadro_pk" PRIMARY KEY ("objeto_cuadro_col", "objeto_cuadro", "objeto_cuadro_proyecto"),
	CONSTRAINT  "apex_obj_ei_cuadro_fk_objeto_cuadro" FOREIGN KEY ("objeto_cuadro", "objeto_cuadro_proyecto") REFERENCES "apex_objeto_cuadro" ("objeto_cuadro", "objeto_cuadro_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_obj_ei_cuadro_fk_formato" FOREIGN KEY ("formateo") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_obj_ei_cuadro_fk_accion_vinculo" FOREIGN KEY ("objeto_cuadro_proyecto","vinculo_item") 	REFERENCES	"apex_item"	("proyecto","item")  ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT "apex_col_cuadro_evento_asoc_fk" FOREIGN KEY ("objeto_cuadro_proyecto", "evento_asociado") REFERENCES "apex_objeto_eventos" ("proyecto", "evento_id") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
CREATE TABLE apex_objeto_cuadro_col_cc
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_cuadro_proyecto
--: dump_clave_componente: objeto_cuadro
--: clave_elemento: objeto_cuadro_cc, objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col
--: dump_order_by: objeto_cuadro, objeto_cuadro_col, objeto_cuadro_cc
--: dump_where: ( objeto_cuadro_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_cuadro_cc BIGINT NULL,
	objeto_cuadro_proyecto VARCHAR(15) NULL,
	objeto_cuadro BIGINT NULL,
	objeto_cuadro_col BIGINT NULL,
	total SMALLINT NULL DEFAULT 0,
	CONSTRAINT "apex_objeto_cuadro_col_cc_pk"	PRIMARY KEY ("objeto_cuadro_cc", "objeto_cuadro_proyecto", "objeto_cuadro", "objeto_cuadro_col"),
	CONSTRAINT "apex_objeto_cuadro_col_cc_fk_apex_objeto_cuadro_cc"	FOREIGN KEY ("objeto_cuadro_cc","objeto_cuadro_proyecto", "objeto_cuadro") REFERENCES "apex_objeto_cuadro_cc" ("objeto_cuadro_cc", "objeto_cuadro_proyecto", "objeto_cuadro") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT "apex_objeto_cuadro_col_cc_fk_apex_objeto_ei_cuadro_columna"	FOREIGN KEY ("objeto_cuadro_col", "objeto_cuadro", "objeto_cuadro_proyecto") REFERENCES "apex_objeto_ei_cuadro_columna" ("objeto_cuadro_col", "objeto_cuadro", "objeto_cuadro_proyecto") ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################
