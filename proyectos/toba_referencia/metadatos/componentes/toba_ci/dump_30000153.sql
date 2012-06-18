------------------------------------------------------------
--[30000153]--  Certificado y firmado (via configuracion) 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'toba_referencia', --proyecto
	'30000153', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'12000003', --punto_montaje
	'ci_cliente', --subclase
	'servicios/seguro_configuracion/ci_cliente.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Certificado y firmado (via configuracion)', --nombre
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
	'2012-03-21 13:30:27', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'toba_referencia', --objeto_mt_me_proyecto
	'30000153', --objeto_mt_me
	NULL, --ev_procesar_etiq
	NULL, --ev_cancelar_etiq
	NULL, --ancho
	NULL, --alto
	NULL, --posicion_botonera
	NULL, --tipo_navegacion
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
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'toba_referencia', --proyecto
	'30000090', --dep_id
	'30000153', --objeto_consumidor
	'30000111', --objeto_proveedor
	'form', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 30

------------------------------------------------------------
-- apex_objeto_ci_pantalla
------------------------------------------------------------

--- INICIO Grupo de desarrollo 30
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'toba_referencia', --objeto_ci_proyecto
	'30000153', --objeto_ci
	'30000063', --pantalla
	'pant_inicial', --identificador
	'1', --orden
	'Pantalla Inicial', --etiqueta
	'El servidor responde con un agregado a la cadena enviada.
Hay dos niveles de seguridad aplicados:
<ul><li>Capa de transporte: Se utilizan los certificados para garantizar una comunicación segura (firmada y encriptada)
<li>Comunicación Punto a Punto: Se toma el certificado enviado por el cliente y se lo coteja contra la configuración local, si no esta definido se rechaza
</ul>

En este ejemplo ambos niveles son <a href=''http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb/Seguridad#configuracion'' target=''_blank''>configurados con archivos</a> presentes en la carpeta <em>instalacion</em>.<br>
Para ello es necesario ejecutar en consola los siguientes pasos previo correr esta operación:

<ol>
<li>Generar la clave privada y certificado para cada proyecto (en este caso el mismo proyecto es cliente y servidor)
<pre>toba servicios_web generar_cert -p toba_referencia</pre>

<li>En el cliente: Tomar el archivo de salida del comando anterior e importar el certificado del servidor para este consumo de servicio especifico:
<pre>toba servicios_web cli_configurar -p toba_referencia -s cli_seguro_configuracion -c ARCHIVO</pre>


<li>En el servidor: Tomar el archivo de salida del 1er comando e importar el certificado del cliente con un ID especifico:
<pre>toba servicios_web serv_configurar -p toba_referencia -s serv_seguro_configuracion -h dependencia=agronomia -c ARCHIVO</pre>
</ol>', --descripcion
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

------------------------------------------------------------
-- apex_objetos_pantalla
------------------------------------------------------------
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'toba_referencia', --proyecto
	'30000063', --pantalla
	'30000153', --objeto_ci
	'0', --orden
	'30000090'  --dep_id
);
