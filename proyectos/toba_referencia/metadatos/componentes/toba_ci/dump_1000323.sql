------------------------------------------------------------
--[1000323]--  Comportamientos AJAX 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_referencia', --proyecto
	'1000323', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'ci_ajax', --subclase
	'componentes/ajax/ci_ajax.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Comportamientos AJAX', --nombre
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
	'2007-08-22 15:33:44'  --creacion
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_objeto_eventos
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES (
	'toba_referencia', --proyecto
	'1000372', --evento_id
	'1000323', --objeto
	'confirmar', --identificador
	'Confirmar', --etiqueta
	'1', --maneja_datos
	NULL, --sobre_fila
	NULL, --confirmacion
	'ei-boton-izq', --estilo
	'apex', --imagen_recurso_origen
	'aplicar.png', --imagen
	'1', --en_botonera
	NULL, --ayuda
	'1', --orden
	NULL, --ci_predep
	'0', --implicito
	'1', --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	NULL, --accion
	'0', --accion_imphtml_debug
	NULL, --accion_vinculo_carpeta
	NULL, --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	'0', --accion_vinculo_popup
	NULL, --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL  --accion_vinculo_celda
);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES (
	'toba_referencia', --proyecto
	'1000373', --evento_id
	'1000323', --objeto
	'boton', --identificador
	'Traer datos planos', --etiqueta
	'0', --maneja_datos
	NULL, --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	'apex', --imagen_recurso_origen
	'descargar.png', --imagen
	'1', --en_botonera
	NULL, --ayuda
	'2', --orden
	NULL, --ci_predep
	'0', --implicito
	'1', --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	NULL, --accion
	'0', --accion_imphtml_debug
	NULL, --accion_vinculo_carpeta
	NULL, --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	'0', --accion_vinculo_popup
	NULL, --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL  --accion_vinculo_celda
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'1000182', --dep_id
	'1000323', --objeto_consumidor
	'1000324', --objeto_proveedor
	'form_datos_param', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'1000183', --dep_id
	'1000323', --objeto_consumidor
	'1000325', --objeto_proveedor
	'form_datos_resp', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'1000184', --dep_id
	'1000323', --objeto_consumidor
	'1000326', --objeto_proveedor
	'form_flickr', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'1000185', --dep_id
	'1000323', --objeto_consumidor
	'1000327', --objeto_proveedor
	'form_validacion', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'toba_referencia', --objeto_mt_me_proyecto
	'1000323', --objeto_mt_me
	NULL, --ev_procesar_etiq
	NULL, --ev_cancelar_etiq
	'600px', --ancho
	NULL, --alto
	'abajo', --posicion_botonera
	'tab_h', --tipo_navegacion
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
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000323', --objeto_ci
	'1000178', --pantalla
	'api_dato', --identificador
	'1', --orden
	'Búsqueda de Datos', --etiqueta
	'Esta operación cuenta con dos formularios. La idea es que el segundo formulario se actualice via AJAX tomando los datos del primero. La mejor forma de hacer esto es poner la lógica tanto de PHP como de JS en el contenedor de ambos componentes (el CI). Se utiliza la llamada javascript <strong>this.ajax()</strong> construyendo la respuesta en el server con <strong>$respuesta->set(array($cant_dias, $total))</strong>.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	'form_datos_param,form_datos_resp', --objetos
	'', --eventos
	NULL, --subclase
	NULL  --subclase_archivo
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000323', --objeto_ci
	'1000181', --pantalla
	'api_html', --identificador
	'3', --orden
	'HTML dinámico', --etiqueta
	'En esta operación se escucha el evento del botón Buscar y se le pide a PHP que busque en [url:http://www.flickr.com/ Flickr] las fotos relacionadas. Se utiliza la llamada <strong>this.ajax_html()</strong> de javascript construyendo la respuesta en el server con <strong>$respuesta->set($html)</strong>.
<br><br>
<strong>Nota:</strong> Este ejemplo necesita la extensión <strong>curl</strong> de PHP y una conexión a internet.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	'form_flickr', --objetos
	'', --eventos
	NULL, --subclase
	NULL  --subclase_archivo
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000323', --objeto_ci
	'1000182', --pantalla
	'api_bajo_nivel', --identificador
	'4', --orden
	'API Bajo Nivel', --etiqueta
	'Cuando lo que necesitamos comunicar entre servidor y cliente es mucha información que no necesita ser codificada ni decodificada (por ejemplo mucho código HTML, javascript y demás) por una cuestión de eficiencia se utiliza la llamada <strong>this.ajax_plano</strong> construyendo la respuesta en el server con <strong>$respuesta->agregar_string($clave, $valor)</strong>.
<br><br>
En este caso el ejemplo trae un página de wikipedia y un código conteniendo un alert en javascript.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	'', --objetos
	'boton', --eventos
	NULL, --subclase
	NULL  --subclase_archivo
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'1000323', --objeto_ci
	'1000183', --pantalla
	'api_dato_validacion', --identificador
	'2', --orden
	'Validación remota', --etiqueta
	'Esta pantalla presenta dos validaciones pedidas al servidor:<ul>
<li>Ante cada cambio de fecha inmediatamente se hace un pedido asincronico de validación por feriado (ingresar fechas como <a href=\'#\' onclick=\'ejemplo_cambiar_fecha(\"25/12/2007\")\'>25/12/2007</a> o <a href=\'#\' onclick=\'ejemplo_cambiar_fecha(\"9/7/2008\")\'>9/7/2008</a>). 
<li>Cuando el usuario decide confirmar los cambios se envían todas las fechas al servidor para que se validen en conjunto, pudiendo detener el proceso de submit del formulario.
</ul>
Para ambas validaciones se utiliza la llamada javascript <strong>this.ajax()</strong> construyendo la respuesta en el server con <strong>$respuesta->set($salida)</strong>.<br><br>
<strong>Nota:</strong> Este ejemplo necesita la extensiones <strong>soap</strong> y <strong>SimpleXML</strong> de PHP y una conexión a internet para acceder al [url:http://www.mininterior.gov.ar/servicios/wsferiados.asp Web Service de Feriados]. La primera vez que accede puede tardar bastante tiempo...', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	'form_validacion', --objetos
	'confirmar', --eventos
	NULL, --subclase
	NULL  --subclase_archivo
);
--- FIN Grupo de desarrollo 1
