INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item)
SELECT i.proyecto, g.usuario_grupo_acc, i.item
FROM apex_item i, apex_usuario_grupo_acc g
WHERE i.proyecto = 'sipefco'
AND g.proyecto = 'sipefco'