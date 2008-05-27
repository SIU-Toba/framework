------------------------------------------------------------
--[500]--  Filtro ML 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion) VALUES (
	'toba_referencia', --proyecto
	'500', --objeto
	NULL, --anterior
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ei_filtro', --clase
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Filtro ML', --nombre
	NULL, --titulo
	NULL, --colapsable
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
	'2008-05-20 13:00:36'  --creacion
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_eventos
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda) VALUES (
	'toba_referencia', --proyecto
	'1000952', --evento_id
	'500', --objeto
	'actualizar', --identificador
	'Actualizar', --etiqueta
	'1', --maneja_datos
	NULL, --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	NULL, --imagen_recurso_origen
	NULL, --imagen
	'1', --en_botonera
	NULL, --ayuda
	NULL, --orden
	NULL, --ci_predep
	NULL, --implicito
	NULL, --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	NULL, --accion
	NULL, --accion_imphtml_debug
	NULL, --accion_vinculo_carpeta
	NULL, --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	NULL, --accion_vinculo_popup
	NULL, --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL  --accion_vinculo_celda
);
--- FIN Grupo de desarrollo 1

------------------------------------------------------------
-- apex_objeto_ei_filtro
------------------------------------------------------------
INSERT INTO apex_objeto_ei_filtro (objeto_ei_filtro_proyecto, objeto_ei_filtro, ancho) VALUES (
	'toba_referencia', --objeto_ei_filtro_proyecto
	'500', --objeto_ei_filtro
	'100%'  --ancho
);

------------------------------------------------------------
-- apex_objeto_ei_filtro_col
------------------------------------------------------------
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'0', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'booleano', --tipo
	'campo_booleano', --nombre
	NULL, --alias_tabla
	'Booleano', --etiqueta
	'Ayuda del booleano', --descripcion
	'0', --obligatorio
	'1', --inicial
	'4', --orden
	NULL, --estado_defecto
	NULL, --opciones_es_multiple
	NULL, --opciones_ef
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'1', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'cadena', --tipo
	'campo_cadena', --nombre
	NULL, --alias_tabla
	'Cadena', --etiqueta
	NULL, --descripcion
	'1', --obligatorio
	'1', --inicial
	'1', --orden
	NULL, --estado_defecto
	NULL, --opciones_es_multiple
	NULL, --opciones_ef
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'2', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'fecha', --tipo
	'campo_fecha', --nombre
	NULL, --alias_tabla
	'Fecha', --etiqueta
	'Ayuda de la fecha', --descripcion
	'0', --obligatorio
	'1', --inicial
	'3', --orden
	NULL, --estado_defecto
	NULL, --opciones_es_multiple
	NULL, --opciones_ef
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'3', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'numero', --tipo
	'campo_numero', --nombre
	NULL, --alias_tabla
	'Número', --etiqueta
	NULL, --descripcion
	'0', --obligatorio
	'1', --inicial
	'2', --orden
	NULL, --estado_defecto
	NULL, --opciones_es_multiple
	NULL, --opciones_ef
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'4', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'opciones', --tipo
	'campo_combo', --nombre
	NULL, --alias_tabla
	'Opcion - Combo', --etiqueta
	NULL, --descripcion
	'0', --obligatorio
	'1', --inicial
	'5', --orden
	NULL, --estado_defecto
	'0', --opciones_es_multiple
	'ef_combo', --opciones_ef
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	'a/A,b/B,c/C', --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'5', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'opciones', --tipo
	'campo_radio', --nombre
	NULL, --alias_tabla
	'Opcion - Radio', --etiqueta
	'Opciones con radio', --descripcion
	'0', --obligatorio
	'1', --inicial
	'6', --orden
	NULL, --estado_defecto
	'0', --opciones_es_multiple
	'ef_radio', --opciones_ef
	'get_opciones', --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	'clave', --carga_col_clave
	'valor', --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	'
'  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'6', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'opciones', --tipo
	'campo_popup', --nombre
	NULL, --alias_tabla
	'Opcion - Popup', --etiqueta
	'Opciones con popup', --descripcion
	'0', --obligatorio
	'1', --inicial
	'7', --orden
	NULL, --estado_defecto
	'0', --opciones_es_multiple
	'ef_popup', --opciones_ef
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'7', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'opciones', --tipo
	'campo_multi_check', --nombre
	NULL, --alias_tabla
	'Opciones - Check', --etiqueta
	NULL, --descripcion
	'0', --obligatorio
	'1', --inicial
	'8', --orden
	NULL, --estado_defecto
	'1', --opciones_es_multiple
	'ef_multi_seleccion_check', --opciones_ef
	'get_opciones', --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	'clave', --carga_col_clave
	'valor', --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'8', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'opciones', --tipo
	'campo_multi_lista', --nombre
	NULL, --alias_tabla
	'Opciones - Lista', --etiqueta
	NULL, --descripcion
	'0', --obligatorio
	'1', --inicial
	'9', --orden
	NULL, --estado_defecto
	'1', --opciones_es_multiple
	'ef_multi_seleccion_lista', --opciones_ef
	'get_opciones', --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	'clave', --carga_col_clave
	'valor', --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_col (objeto_ei_filtro_col, objeto_ei_filtro, objeto_ei_filtro_proyecto, tipo, nombre, alias_tabla, etiqueta, descripcion, obligatorio, inicial, orden, estado_defecto, opciones_es_multiple, opciones_ef, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'9', --objeto_ei_filtro_col
	'500', --objeto_ei_filtro
	'toba_referencia', --objeto_ei_filtro_proyecto
	'opciones', --tipo
	'campo_multi_doble', --nombre
	NULL, --alias_tabla
	'Opciones - Doble', --etiqueta
	NULL, --descripcion
	'0', --obligatorio
	'1', --inicial
	'10', --orden
	NULL, --estado_defecto
	'1', --opciones_es_multiple
	'ef_multi_seleccion_doble', --opciones_ef
	'get_opciones', --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	'clave', --carga_col_clave
	'valor', --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
