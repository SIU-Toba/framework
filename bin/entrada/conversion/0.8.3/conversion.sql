DELETE FROM apex_objeto_ci_pantalla;
DELETE FROM apex_objeto_ei_cuadro_columna;
DELETE FROM apex_objeto_ei_formulario_ef;

-- 1: Migracion de datos de tablas
-- 2: Migracion de la definicion de los eventos
-- 3: Migracion de la asociacion de dependencias a pantallas?

--**********************************************************
--** 1 - Migracion de tablas  ******************************
--**********************************************************

---------------------------------------------------
-- objeto_ei_cuadro
---------------------------------------------------

INSERT INTO  apex_objeto_ei_cuadro_columna 
(
	objeto_cuadro_proyecto	,
	objeto_cuadro         	,
	clave          			,	
	orden				  	,
	titulo                	,
	estilo    				,	
	ancho					,	
	formateo   				,	
	vinculo_indice	      	,
	no_ordenar				,	
	mostrar_xls				,	
	mostrar_pdf				,	
	pdf_propiedades       	,
	desabilitado			,	
	total			
)
SELECT 	
	x.objeto_cuadro_proyecto	,
	x.objeto_cuadro         	,
	x.valor_sql      			,	
	x.orden				  	,
	x.titulo                	,
	x.columna_estilo  		,	
	x.columna_ancho			,	
	x.valor_sql_formato		,	
	x.vinculo_indice	      	,
	x.no_ordenar				,	
	x.mostrar_xls				,	
	x.mostrar_pdf				,	
	x.pdf_propiedades       	,
	x.desabilitado			,	
	x.total			
FROM apex_objeto o,
apex_objeto_cuadro_columna x                       
WHERE o.objeto = x.objeto_cuadro                       
AND o.proyecto = x.objeto_cuadro_proyecto               
AND x.valor_sql IS NOT NULL
AND o.clase = 'objeto_ei_cuadro';                       


---------------------------------------------------
-- objeto_ei_formulario
---------------------------------------------------

INSERT INTO  apex_objeto_ei_formulario_ef
(
	objeto_ei_formulario_proyecto   , 	
	objeto_ei_formulario            , 	
	identificador      				,	
	elemento_formulario     		,	
	columnas                		,	
	obligatorio             		,		
	inicializacion          		,	
	orden                   		,	
	etiqueta                		,	
	descripcion             		,	
	colapsado						,	
	desactivado             		,	
	estilo   				 		,	
	total								
)
SELECT 	
	x.objeto_ut_formulario_proyecto   , 	
	x.objeto_ut_formulario            , 	
	x.identificador      				,	
	x.elemento_formulario     		,	
	x.columnas                		,	
	x.obligatorio             		,		
	x.inicializacion          		,	
	x.orden                   		,	
	x.etiqueta                		,	
	x.descripcion             		,	
	x.colapsado						,	
	x.desactivado             		,	
	x.lista_columna_estilo	 		,	
	x.total								
FROM apex_objeto o,
apex_objeto_ut_formulario_ef x                       
WHERE o.objeto = x.objeto_ut_formulario                       
AND o.proyecto = x.objeto_ut_formulario_proyecto               
AND o.clase IN ('objeto_ei_formulario','objeto_ei_formulario_ml','objeto_ei_filtro');

---------------------------------------------------
-- objeto_ci_pantalla
---------------------------------------------------

INSERT INTO  apex_objeto_ci_pantalla
(
	objeto_ci_proyecto			,
	objeto_ci					,
	identificador				,
	orden						,
	etiqueta					,
	descripcion					,
	tip							,
	imagen_recurso_origen		,
	imagen						,
	objetos						,
	ev_procesar					,
	ev_cancelar					
)
SELECT 	
	x.objeto_mt_me_proyecto		,
	x.objeto_mt_me				,
	x.posicion					,
	CAST(x.posicion	AS smallint),
	x.etiqueta					,
	x.descripcion					,
	x.tip							,
	x.imagen_recurso_origen		,
	x.imagen						,
	x.objetos						,
	x.ev_procesar					,
	x.ev_cancelar					
FROM apex_objeto o,
apex_objeto_mt_me_etapa x                       
WHERE o.objeto = x.objeto_mt_me                       
AND o.proyecto = x.objeto_mt_me_proyecto               
AND o.clase IN ('objeto_ci','ci_cn','ci_abm_dbr','ci_abm_dbt','ci_abm_nav');

--------------------------------------------------
---- 1: Migracion de tablas ----------------------
--------------------------------------------------

