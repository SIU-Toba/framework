--DELETE FROM apex_objeto_abms_ef WHERE objeto_abms = 220 AND objeto_abms_proyecto = 'toba';

INSERT INTO apex_objeto_abms_ef (
objeto_abms_proyecto ,  
objeto_abms          ,  
identificador        ,  
columnas             ,  
clave_primaria       ,  
obligatorio          ,  
elemento_formulario  ,  
inicializacion       ,  
orden                ,  
etiqueta             ,  
descripcion          ,  
desactivado            
) 
SELECT 
objeto_abms_proyecto ,
'220',
-- objeto_abms          ,
identificador        ,
columnas             ,
clave_primaria       ,
obligatorio          ,
elemento_formulario  ,
inicializacion       ,
orden                ,
etiqueta             ,
descripcion          ,
desactivado          
FROM apex_objeto_abms_ef
WHERE    objeto_abms = 154 AND objeto_abms_proyecto = 'toba';


