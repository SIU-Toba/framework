--INSERT INTO apex_objeto_ut_formulario (
--objeto_ut_formulario_proyecto ,      
--objeto_ut_formulario          ,      
--tabla                      	,		
--titulo                     	,		
--ev_mod_eliminar            	,				
--ev_mod_limpiar	            	,		
--auto_reset	)
--SELECT 
--   objeto_abms_proyecto    ,
--   objeto_abms             ,
--   tabla                   ,
--   titulo                  ,
--   ev_mod_eliminar         ,
--   ev_mod_estado_i         ,
--   auto_reset 
--FROM apex_objeto_abms;

INSERT INTO apex_objeto_ut_formulario_ef (
   objeto_ut_formulario_proyecto ,
   objeto_ut_formulario          ,
   identificador          			,
   columnas                		,
   clave_primaria          		,
   obligatorio             		,
   elemento_formulario     		,
   inicializacion          		,
   orden                   		,
   etiqueta                		,
   descripcion             		,
   desactivado)
SELECT
   objeto_abms_proyecto       ,       
   objeto_abms                ,
   identificador              ,
   columnas                   ,
   clave_primaria             ,
   obligatorio                ,
   elemento_formulario        ,
   inicializacion             ,
   orden                      ,
   etiqueta                   ,
   descripcion                ,
   desactivado               
FROM apex_objeto_abms_ef;