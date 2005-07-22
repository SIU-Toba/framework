-- Estos ei_cuadros estan mal...
--SELECT o.nombre, x.*
--FROM apex_objeto o,
--apex_objeto_cuadro_columna x                       
--WHERE o.objeto = x.objeto_cuadro                       
--AND o.proyecto = x.objeto_cuadro_proyecto               
--AND x.valor_sql IS NULL                       
--AND o.clase = 'objeto_ei_cuadro';                       

-- Migracion de tablas para el ei_cuadro

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
SELECT 	x.objeto_cuadro_proyecto	,
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



