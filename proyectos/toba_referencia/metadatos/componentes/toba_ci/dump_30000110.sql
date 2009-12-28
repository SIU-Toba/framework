------------------------------------------------------------
--[30000110]--  Servicios Web 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_referencia', --proyecto
	'30000110', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'ci_servicios', --subclase
	'servicios/ci_servicios.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Servicios Web', --nombre
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
	'2009-11-05 13:35:00', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'30000063', --dep_id
	'30000110', --objeto_consumidor
	'30000116', --objeto_proveedor
	'form_adjunto', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'30000064', --dep_id
	'30000110', --objeto_consumidor
	'30000118', --objeto_proveedor
	'form_datos_password', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'30000059', --dep_id
	'30000110', --objeto_consumidor
	'30000111', --objeto_proveedor
	'form_echo', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'30000065', --dep_id
	'30000110', --objeto_consumidor
	'30000119', --objeto_proveedor
	'form_echo_seguro', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'30000066', --dep_id
	'30000110', --objeto_consumidor
	'30000121', --objeto_proveedor
	'form_secuencia', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'toba_referencia', --objeto_mt_me_proyecto
	'30000110', --objeto_mt_me
	NULL, --ev_procesar_etiq
	NULL, --ev_cancelar_etiq
	NULL, --ancho
	NULL, --alto
	NULL, --posicion_botonera
	'tab_h', --tipo_navegacion
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

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000110', --objeto_ci
	'30000045', --pantalla
	'pant_echo', --identificador
	'1', --orden
	'Hola Mundo', --etiqueta
	'El servidor responde exactamente lo que envia el cliente', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL  --template
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000110', --objeto_ci
	'30000047', --pantalla
	'pant_adjuntos', --identificador
	'2', --orden
	'Adjuntos', --etiqueta
	'El cliente envia una imagen y un texto. El servidor toma el texto y lo agrega a la imagen y la retorna. 
<br>
Para la comunicaci�n se usa la especificaci�n MTOM/XOP
<br>
Requiere instalar la extensi�n GD de PHP en el servidor.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL  --template
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000110', --objeto_ci
	'30000048', --pantalla
	'pant_datos_password', --identificador
	'3', --orden
	'Arreglos (c/autenticaci�n de password)', --etiqueta
	'Se envia un arreglo de N-dimensiones con claves asociativas, en el servidor se reduce a un arreglo num�rico. 
<ul>
<li>Para enviar un arreglo simplemente se lo pasa al constructor de <em>toba_servicio_web_mensaje</em></li>
<li>Para transformar la respuesta XML en un arreglo se usa la api <em>$mensaje->get_array()</em>
<li>Se usa una autenticaci�n con password  (ws-security y ws-addressing)
</ul>', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL  --template
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000110', --objeto_ci
	'30000049', --pantalla
	'pant_encriptacion_firma', --identificador
	'4', --orden
	'Encriptaci�n y firma de mensajes', --etiqueta
	'En la comunicaci�n se envia un mensaje encriptado desde el cliente. Tambi�n el cliente firma el mensaje para asegurar su identidad.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL  --template
);
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000110', --objeto_ci
	'30000050', --pantalla
	'pant_secuencia', --identificador
	'5', --orden
	'Secuencia de mensajes', --etiqueta
	'<p>Se envian tres mensajes, el primero espera respuesta (m�todo request) mientras que los otros dos no (m�todo send). Esta secuencia no constituye una transacci�n ya que cada mensaje implica un request HTTP individual y por lo tanto cada servicio tiene una sesi�n propia de la base de datos. Para poder usar una transacci�n de base de datos en el servidor es necesario wrappear las tres operaciones en una �nica</p>', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL  --template
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objetos_pantalla
------------------------------------------------------------
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'toba_referencia', --proyecto
	'30000045', --pantalla
	'30000110', --objeto_ci
	'0', --orden
	'30000059'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'toba_referencia', --proyecto
	'30000047', --pantalla
	'30000110', --objeto_ci
	'0', --orden
	'30000063'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'toba_referencia', --proyecto
	'30000048', --pantalla
	'30000110', --objeto_ci
	'0', --orden
	'30000064'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'toba_referencia', --proyecto
	'30000049', --pantalla
	'30000110', --objeto_ci
	'0', --orden
	'30000065'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'toba_referencia', --proyecto
	'30000050', --pantalla
	'30000110', --objeto_ci
	'0', --orden
	'30000066'  --dep_id
);
