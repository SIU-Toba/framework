-- Controles ITEM -> PADRE_ITEM
-- Los registro que aparecen estan MAL!

SELECT proyecto, item, substr(item,0,length(padre)+1), padre FROM apex_item
WHERE substr(item,0,length(padre)+1) != padre;

SELECT proyecto, item, padre FROM apex_item
WHERE substr(item,0,2) != substr(padre,0,2) AND padre != '';

SELECT proyecto, item, padre FROM apex_item
WHERE item = padre AND item != '';

SELECT proyecto, item, padre FROM apex_item
WHERE (position(padre in item)) != 1 ;
