-- Este script mueve el titulo de los Objetos Standard de las tablas especificas a la general

-- objeto_cuadro

UPDATE apex_objeto
SET titulo = x.titulo
FROM apex_objeto_cuadro x
WHERE apex_objeto.objeto = x.objeto_cuadro
AND	apex_objeto.proyecto = x.objeto_cuadro_proyecto;

-- objeto_ut_formulario

UPDATE apex_objeto
SET titulo = x.titulo
FROM apex_objeto_ut_formulario x
WHERE apex_objeto.objeto = x.objeto_ut_formulario
AND	apex_objeto.proyecto = x.objeto_ut_formulario_proyecto;

-- objeto_lista

UPDATE apex_objeto
SET titulo = x.titulo
FROM apex_objeto_lista x
WHERE apex_objeto.objeto = x.objeto_lista
AND	apex_objeto.proyecto = x.objeto_lista_proyecto;
