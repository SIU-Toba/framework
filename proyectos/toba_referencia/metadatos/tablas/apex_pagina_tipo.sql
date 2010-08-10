
------------------------------------------------------------
-- apex_pagina_tipo
------------------------------------------------------------
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'toba_referencia', --proyecto
	'referencia', --pagina_tipo
	'Páginas de operaciones de referencia', --descripcion
	'tp_referencia', --clase_nombre
	'tp_referencia.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'12000003'  --punto_montaje
);
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'toba_referencia', --proyecto
	'tutorial', --pagina_tipo
	'Páginas del tutorial', --descripcion
	'tp_tutorial', --clase_nombre
	'tutorial/tp_tutorial.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'12000003'  --punto_montaje
);
