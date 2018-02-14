--**************************************************************************************************
--**************************************  FORMULARIOS  *****************************************
--**************************************************************************************************


CREATE TABLE apex_objeto_ut_formulario
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_ut_formulario_proyecto
--: dump_clave_componente: objeto_ut_formulario
--: dump_order_by: objeto_ut_formulario
--: dump_where: ( objeto_ut_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_ut_formulario_proyecto    	varchar(15)		NOT NULL,
	objeto_ut_formulario       			int8  			NOT NULL,
	tabla                      			TEXT   	NULL,
	titulo                     			TEXT   	NULL,       -- Titulo de la interface
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
	clase_proyecto						varchar(15)		NULL, 		-- Que tipo de UT hay que wrappear?
	clase								varchar(60)		NULL,
-- Exclusivo MT_ABMS y UT_FORMULARIO_ML            	
	auto_reset							smallint       	NULL,       -- Se resetea el formulario despues de transaccionar
-- Exclusivo FORMULARIO            	
   ancho                   				varchar(10)    	NULL,	
   ancho_etiqueta						varchar(10)		NULL,
   expandir_descripcion					smallint		NULL,
-- Exclusivo UT_FORMULARIO_BL
	campo_bl							varchar(40)		NULL,
-- Exclusivo EI_FORMULARIO_ML
	scroll								smallint		NULL,
	filas								smallint       	NULL,
	filas_agregar						smallint       	NULL,
	filas_agregar_online				smallint		NULL DEFAULT 1,
	filas_agregar_abajo					smallint		NULL DEFAULT 0,
	filas_agregar_texto					TEXT	NULL,
	filas_borrar_en_linea				smallint		NULL DEFAULT 0,
	filas_undo							smallint		NULL,
	filas_ordenar						smallint		NULL,
	filas_ordenar_en_linea				smallint		NULL DEFAULT 0,
	columna_orden						TEXT	NULL,
	filas_numerar						smallint 		NULL,
	ev_seleccion						smallint		NULL,
	alto								varchar(10)		NULL,
	analisis_cambios					varchar(10)		NULL,
-- Comunes
	no_imprimir_efs_sin_estado			smallint		NULL,
	resaltar_efs_con_estado				smallint		NULL,
	template							TEXT			NULL,
	template_impresion		TEXT				NULL,
	CONSTRAINT  "apex_objeto_ut_f_pk" PRIMARY KEY ("objeto_ut_formulario", "objeto_ut_formulario_proyecto"),
	CONSTRAINT  "apex_objeto_ut_f_fk_objeto" FOREIGN KEY ("objeto_ut_formulario", "objeto_ut_formulario_proyecto") REFERENCES "apex_objeto" ("objeto", "proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE
);
--###################################################################################################

CREATE SEQUENCE apex_obj_ei_form_fila_seq INCREMENT	1 MINVALUE 0 MAXVALUE 9223372036854775807	CACHE	1;
CREATE TABLE apex_objeto_ei_formulario_ef
---------------------------------------------------------------------------------------------------
--: proyecto: toba
--: dump: componente
--: dump_clave_proyecto: objeto_ei_formulario_proyecto
--: dump_clave_componente: objeto_ei_formulario
--: dump_order_by: objeto_ei_formulario, objeto_ei_formulario_fila
--: dump_where: ( objeto_ei_formulario_proyecto = '%%' )
--: zona: objeto
--: desc:
--: historica: 0
--: version: 1.0
---------------------------------------------------------------------------------------------------
(
	objeto_ei_formulario_fila			int8			DEFAULT nextval('"apex_obj_ei_form_fila_seq"'::text) NOT NULL, 
	objeto_ei_formulario             	int8			NOT NULL,
	objeto_ei_formulario_proyecto    	varchar(15)		NOT NULL,
	identificador      					varchar(40)    	NOT NULL,
	elemento_formulario     			varchar(30)    	NOT NULL,
	columnas                			TEXT   	NOT NULL,
	obligatorio             			smallint       	NULL,	
	oculto_relaja_obligatorio			smallint		NULL,		
	orden                   			float       	NOT NULL,
	etiqueta                			TEXT    	NULL,
	etiqueta_estilo            			TEXT    	NULL,
	descripcion             			TEXT        	NULL,
	colapsado							smallint		NULL,
	desactivado             			smallint       	NULL,
	estilo   				 			TEXT		    NULL,		
	total								smallint		NULL,		
	inicializacion          			TEXT        	NULL,
	permitir_html						smallint		NULL,	-- Permite estados incluyendo codigo html
	deshabilitar_rest_func				smallint		NULL,	-- No permite aplicar una rest. func a este ef
	--- PARAMETROS
	estado_defecto						TEXT	NULL,
	solo_lectura						smallint		NULL,
	solo_lectura_modificacion	smallint  NOT NULL DEFAULT 0,
	carga_metodo						TEXT	NULL,	-- carga ci
	carga_clase							TEXT	NULL,	-- carga estatico
	carga_include						TEXT	NULL,
	carga_dt							int8			NULL,	--carga datos_tabla
	carga_consulta_php					int8			NULL,	--carga consulta_php
	carga_sql							TEXT			NULL,	--carga sql
	carga_fuente						varchar(30)		NULL,
	carga_lista							TEXT	NULL,	--carga lista
	carga_col_clave						TEXT	NULL,
	carga_col_desc						TEXT	NULL,
	carga_maestros						TEXT	NULL,
	carga_cascada_relaj					smallint		NULL,
	cascada_mantiene_estado		smallint NOT NULL DEFAULT 0,
	carga_permite_no_seteado	smallint		NOT NULL DEFAULT 0,
	carga_no_seteado					TEXT	NULL,
	carga_no_seteado_ocultar			smallint		NULL,
	edit_tamano							smallint		NULL,
	edit_maximo							smallint		NULL,
	edit_mascara						TEXT	NULL,
	edit_unidad							TEXT	NULL,
	edit_rango							TEXT	NULL,
	edit_filas							smallint		NULL,
	edit_columnas						smallint		NULL,
	edit_wrap							varchar(20)		NULL,
	edit_resaltar						smallint		NULL,
	edit_ajustable						smallint		NULL,
	edit_confirmar_clave				smallint		NULL,
	edit_expreg							TEXT	NULL,
	popup_item							varchar(60)		NULL,
	popup_proyecto						varchar(15)		NULL,
	popup_editable						smallint		NULL,
	popup_ventana						TEXT	NULL,
	popup_carga_desc_metodo				TEXT	NULL,
	popup_carga_desc_clase				TEXT	NULL,
	popup_carga_desc_include			TEXT	NULL,
	popup_puede_borrar_estado			smallint 		NULL,
	fieldset_fin						smallint		NULL,
	check_valor_si						varchar(40)		NULL,
	check_valor_no						varchar(40)		NULL,
	check_desc_si						varchar(100)	NULL,
	check_desc_no						varchar(100)	NULL,
	check_ml_toggle						smallint		NULL,
	fijo_sin_estado						smallint		NULL,
	editor_ancho						varchar(10)		NULL,
	editor_alto							varchar(10)		NULL,
	editor_botonera						varchar(50)		NULL,
	selec_cant_minima					smallint		NULL,
	selec_cant_maxima					smallint		NULL,
	selec_utilidades					smallint		NULL,
	selec_tamano						smallint		NULL,
	selec_ancho							varchar(30)		NULL,
	selec_serializar					smallint		NULL,
	selec_cant_columnas					smallint		NULL,
	upload_extensiones					TEXT			NULL,
	punto_montaje						int8			NULL,
	placeholder						TEXT	NULL,
	CONSTRAINT  "apex_ei_f_ef_pk" PRIMARY KEY ("objeto_ei_formulario_fila", "objeto_ei_formulario", "objeto_ei_formulario_proyecto"),
	CONSTRAINT  "apex_ei_f_ef_fk_padre" FOREIGN KEY ("objeto_ei_formulario", "objeto_ei_formulario_proyecto") REFERENCES "apex_objeto_ut_formulario" ("objeto_ut_formulario", "objeto_ut_formulario_proyecto") ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	---CONSTRAINT  "apex_ei_f_ef_fk_estilo" FOREIGN KEY ("estilo") REFERENCES "apex_columna_estilo" ("columna_estilo") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_ei_f_ef_fk_ef" FOREIGN KEY ("elemento_formulario") REFERENCES "apex_elemento_formulario" ("elemento_formulario") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_ei_f_ef_fk_datos_tabla" FOREIGN KEY ("objeto_ei_formulario_proyecto","carga_dt") REFERENCES "apex_objeto" ("proyecto","objeto") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT  "apex_ei_f_ef_fk_consulta_php" FOREIGN KEY ("objeto_ei_formulario_proyecto","carga_consulta_php") REFERENCES "apex_consulta_php" ("proyecto", "consulta_php") ON DELETE NO ACTION ON UPDATE NO ACTION DEFERRABLE INITIALLY IMMEDIATE,
	CONSTRAINT	"apex_ei_f_ef_fk_accion_vinculo" FOREIGN KEY ("popup_proyecto","popup_item") 	REFERENCES	"apex_item"	("proyecto","item")  ON DELETE CASCADE ON UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE,
	CONSTRAINT	"apex_objeto_fk_puntos_montaje" FOREIGN KEY ("objeto_ei_formulario_proyecto", "punto_montaje")	REFERENCES "apex_puntos_montaje"	("proyecto", "id") ON DELETE NO ACTION	ON	UPDATE NO ACTION DEFERRABLE INITIALLY	IMMEDIATE
);
--###################################################################################################

