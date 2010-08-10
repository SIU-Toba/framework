------------------------------------------------------------
--[1000224]--  Persistencia 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_referencia', --proyecto
	'1000224', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'12000003', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Persistencia', --nombre
	NULL, --titulo
	'0', --colapsable
	NULL, --descripcion
	NULL, --fuente_datos_proyecto
	NULL, --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2006-12-01 09:04:03', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'toba_referencia', --objeto_mt_me_proyecto
	'1000224', --objeto_mt_me
	NULL, --ev_procesar_etiq
	NULL, --ev_cancelar_etiq
	'80%', --ancho
	NULL, --alto
	'abajo', --posicion_botonera
	'wizard', --tipo_navegacion
	'0', --botonera_barra_item
	'0', --con_toc
	NULL, --incremental
	NULL, --debug_eventos
	NULL, --activacion_procesar
	NULL, --activacion_cancelar
	NULL, --ev_procesar
	NULL, --ev_cancelar
	NULL, --objetos
	NULL, --post_procesar
	NULL, --metodo_despachador
	NULL  --metodo_opciones
);

------------------------------------------------------------
-- apex_objeto_ci_pantalla
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000094', --pantalla
	'definicion', --identificador
	'2', --orden
	'Introducción', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_definicion', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000095', --pantalla
	'def_tablas', --identificador
	'5', --orden
	'[Video] Definición de tablas a partir de la base', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_def_tablas', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000096', --pantalla
	'def_relaciones', --identificador
	'7', --orden
	'[Video] Definición de una relación de tablas', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_def_relaciones', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000097', --pantalla
	'carga', --identificador
	'8', --orden
	'Carga de la relación', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_carga', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000098', --pantalla
	'api', --identificador
	'9', --orden
	'API Tabular de las tablas', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_api', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000099', --pantalla
	'sincronizacion', --identificador
	'10', --orden
	'Sincronización', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_sincronizacion', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000100', --pantalla
	'agenda', --identificador
	'1', --orden
	'Agenda', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_agenda', --subclase
	'tutorial/pant_tutorial.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000101', --pantalla
	'inmediata', --identificador
	'3', --orden
	'Transacción inmediata', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_inmediata', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000102', --pantalla
	'marco', --identificador
	'4', --orden
	'Transacción a nivel operación', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_marco', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000103', --pantalla
	'relaciones', --identificador
	'6', --orden
	'Trabajando con relaciones', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_relaciones', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000224', --objeto_ci
	'1000104', --pantalla
	'mas_info', --identificador
	'11', --orden
	'Más info', --etiqueta
	NULL, --descripcion
	NULL, --tip
	NULL, --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	'pant_masinfo', --subclase
	'tutorial/pant_persistencia.php', --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
--- FIN Grupo de desarrollo 1
