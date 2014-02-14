
------------------------------------------------------------
-- apex_pagina_tipo
------------------------------------------------------------
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'toba_usuarios', --proyecto
	'toba_sin_menu', --pagina_tipo
	'Pagina para el generador de menus', --descripcion
	'toba_sin_menu', --clase_nombre
	'extension_toba/toba_sin_menu.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'12000004'  --punto_montaje
);
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'toba_usuarios', --proyecto
	'toba_usuarios_normal', --pagina_tipo
	'Pagina con la funcionalidad de logout modificada', --descripcion
	'toba_usuarios_normal', --clase_nombre
	'extension_toba/toba_usuarios_normal.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'12000004'  --punto_montaje
);
