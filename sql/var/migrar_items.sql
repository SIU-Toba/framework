
-------- MIGRACION de CLAVE de ITEMS
---
--- FALTA:  crear FKs, Llevar a NOT NULL, eliminar anterior
---
---
---- Padres
--UPDATE apex_item
--SET padre_id = x.item_id
--FROM apex_item x
--WHERE apex_item.padre = x.item
--AND apex_item.padre_proyecto = x.proyecto;
--
---- Info
--UPDATE apex_item_info
--SET item_id = x.item_id
--FROM apex_item x
--WHERE apex_item_info.item = x.item
--AND apex_item_info.item_proyecto = x.proyecto;
--
---- Objetos asociados
--UPDATE apex_item_objeto
--SET item_id = x.item_id
--FROM apex_item x
--WHERE apex_item_objeto.item = x.item
--AND apex_item_objeto.proyecto = x.proyecto;
--
---- Grupo ACCESO
--UPDATE apex_usuario_grupo_acc_item
--SET item_id = x.item_id
--FROM apex_item x
--WHERE apex_usuario_grupo_acc_item.item = x.item
--AND apex_usuario_grupo_acc_item.proyecto = x.proyecto;

-- CLASES

-- VINCULOS

-- DIMENSION_TIPO

-- SOLICITUD

-- NOTA

-- MENSAJE