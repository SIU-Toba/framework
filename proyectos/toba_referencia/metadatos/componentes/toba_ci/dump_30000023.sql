------------------------------------------------------------
--[30000023]--  Uso del menú 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_referencia', --proyecto
	'30000023', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'12000003', --punto_montaje
	'ci_uso_del_menu', --subclase
	'varios/uso_del_menu/ci_uso_del_menu.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Uso del menú', --nombre
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
	'2009-02-18 13:42:47', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'toba_referencia', --objeto_mt_me_proyecto
	'30000023', --objeto_mt_me
	NULL, --ev_procesar_etiq
	NULL, --ev_cancelar_etiq
	'600px', --ancho
	'250px', --alto
	'abajo', --posicion_botonera
	'wizard', --tipo_navegacion
	NULL, --botonera_barra_item
	'1', --con_toc
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

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000023', --objeto_ci
	'30000013', --pantalla
	'pant_introduccion', --identificador
	'1', --orden
	'Introducción', --etiqueta
	'En las siguientes pantallas se hará uso del API del menú, tanto en PHP como en JS
<br>
<br>
Como en todos los ejemplos de este proyecto, es posible ver los códigos fuentes de la operación en el recuadro inferior derecho, o navegando el proyecto desde el sistema de archivos.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000023', --objeto_ci
	'30000014', --pantalla
	'pant_agregar_opcion', --identificador
	'2', --orden
	'Agregar /Quitar', --etiqueta
	'Desde PHP es posible agregar o quitar opciones del menú. Por ejemplo en esta página quitamos todas las opciones del <strong>tutorial</strong> y agregamos una entrada llamada <strong>Opción nueva</strong>.
<br>
<br>
Para poder agregar o quitar opciones del menú durante la atención de eventos de un CI en php, es preciso indicar en la prop. básicas de la operación que la misma <em>Cambia de operación o modifica el menú durante los eventos</em>.
<br><br>
Si el cambio se quiere aplicar de forma global, es preciso hacerlo durante el inicio del contexto de ejecución del proyecto y no requiere cambiar las propiedades de cada operación.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000023', --objeto_ci
	'30000015', --pantalla
	'pant_cambiar_opcion', --identificador
	'3', --orden
	'Modificar Opción', --etiqueta
	'En esta pantalla modificamos la opción de acceso a esta misma operación para que tenga otro nombre e imagen, y además tenga código javascript propio (ver menú General)', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000023', --objeto_ci
	'30000016', --pantalla
	'pant_cambiar_comportamiento', --identificador
	'4', --orden
	'Comportamiento global', --etiqueta
	'En esta pantalla modificamos el comportamiento global del menu, agregando una callback al disparador del menú para que pida una confirmación antes de navegar hacia las operaciones.
<br><br>
Para probar el cambio intente navegar a cualquier opción del menú', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'12000003'  --punto_montaje
);
--- FIN Grupo de desarrollo 30
