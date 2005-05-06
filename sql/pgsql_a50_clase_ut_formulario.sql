--**************************************************************************************************
--**************************************************************************************************
--**************************************  UT - Formulario  *****************************************
--**************************************************************************************************
--**************************************************************************************************

CREATE TABLE apex_objeto_ut_formulario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_ut_formulario
--: dump_where: ( objeto_ut_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_ut_formulario_proyecto    	varchar(15)		NOT NULL,
	objeto_ut_formulario       			int4  			NOT NULL,
	tabla                      			varchar(100)   	NULL,
	titulo                     			varchar(80)    	NULL,       -- Titulo de la interface
	ev_agregar							smallint		NULL,		-- Proponer agregar si no hay estado
	ev_agregar_etiq						varchar(30)		NULL,
	ev_mod_modificar					smallint		NULL,
	ev_mod_modificar_etiq				varchar(30)		NULL,
   	ev_mod_eliminar            			smallint       	NULL,       -- Pantalla de modificacion: Se permite eliminar registros ?
	ev_mod_eliminar_etiq				varchar(30)		NULL,
	ev_mod_limpiar	           			smallint       	NULL,       -- Pantalla de modificacion: Se permite limpiar el formulario?
	ev_mod_limpiar_etiq					varchar(30)		NULL,
   	ev_mod_clave      	      			smallint       	NULL,       -- Se permite modificar la clave??
-- Exclusivo MT_ABMS
	clase_proyecto						varchar(15)		NULL,  -- Que tipo de UT hay que wrappear?
	clase								varchar(60)		NULL,
-- Exclusivo MT_ABMS y UT_FORMULARIO_ML            	
	auto_reset							smallint       	NULL,       -- Se resetea el formulario despues de transaccionar
-- Exclusivo UT_FORMULARIO_ML            	
   ancho                   				varchar(10)    	NULL,	
-- Exclusivo UT_FORMULARIO_BL
	campo_bl							varchar(40)		NULL,
-- Exclusivo EI_FORMULARIO_ML
	scroll								smallint		NULL,
	filas								smallint       	NULL,
	filas_agregar						smallint       	NULL,
--	filas_undo							smallint		NULL,
--	filas_ordenar						smallint		NULL,
	alto								varchar(10)		NULL,
	CONSTRAINT  "apex_objeto_ut_f_pk" PRIMARY KEY ("objeto_ut_formulario_proyecto","objeto_ut_formulario"),
	CONSTRAINT	"apex_objeto_ut_f_fk_clase" FOREIGN KEY ("clase_proyecto","clase") REFERENCES "apex_clase" ("proyecto","clase") ON DELETE	NO	ACTION ON UPDATE NO ACTION	NOT DEFERRABLE	INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_objeto_ut_f_fk_objeto" FOREIGN KEY ("objeto_ut_formulario_proyecto","objeto_ut_formulario") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE TABLE apex_objeto_ut_formulario_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: multiproyecto
--: dump_order_by: objeto_ut_formulario, identificador
--: dump_where: ( objeto_ut_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_ut_formulario_proyecto    	varchar(15)		NOT NULL,
	objeto_ut_formulario             	int4			NOT NULL,
	identificador      					varchar(30)    	NOT NULL,
	columnas                			varchar(255)   	NOT NULL,
	clave_primaria          			smallint       	NULL,			-- El contenido de este EF es parte de una clave primaria?
	obligatorio             			smallint       	NULL,			-- El contenido de este EF es obligatorio?
	elemento_formulario     			varchar(30)    	NOT NULL,
	inicializacion          			varchar        	NULL,
	orden                   			float       	NOT NULL,
	etiqueta                			varchar(40)    	NULL,
	descripcion             			varchar        	NULL,
	desactivado             			smallint       	NULL,
	no_sql								smallint		NULL,
-- ATENCION: exclusivo EI_FORMULARIO_ML        
	total								smallint		NULL,			-- Indica si el EF aparece en la fila de total
-- ATENCION: exclusivo UT_FORMULARIO_ML            	
	clave_primaria_padre    			smallint       	NULL,			-- El contenido de este EF es parte de una clave primaria?
	listar		           				smallint       	NULL,
	lista_cabecera          			varchar(40)    	NULL,			-- Titulo del campo en la lista
	lista_orden							float       	NULL,
	lista_columna_estilo    			int4		    NULL,			-- Estilo de la columna
	lista_valor_sql         			varchar(40)    	NULL,			-- Campo SQL alternativo
	lista_valor_sql_formato    			int4		    NULL,			-- El valor del debe ser formateado
	lista_valor_sql_esp					varchar(40)	    NULL,			-- El valor del debe ser formateado CUSTOM
	lista_ancho							varchar(10)		NULL,
	CONSTRAINT  "apex_ut_f_ef_pk" PRIMARY KEY ("objeto_ut_formulario_proyecto","objeto_ut_formulario","identificador"),
	CONSTRAINT  "apex_ut_f_ef_fk_formato" FOREIGN KEY ("lista_valor_sql_formato") REFERENCES "apex_columna_formato" ("columna_formato") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_ut_f_ef_fk_estilo" FOREIGN KEY ("lista_columna_estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_ut_f_ef_fk_padre" FOREIGN KEY ("objeto_ut_formulario_proyecto","objeto_ut_formulario") REFERENCES "apex_objeto_ut_formulario" ("objeto_ut_formulario_proyecto","objeto_ut_formulario") ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_ut_f_ef_fk_ef" FOREIGN KEY ("elemento_formulario") REFERENCES "apex_elemento_formulario" ("elemento_formulario") ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################





