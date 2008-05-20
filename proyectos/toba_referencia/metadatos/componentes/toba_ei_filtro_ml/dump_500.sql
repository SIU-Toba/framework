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
	'toba_ei_filtro_ml', --clase
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
-- apex_objeto_ei_filtro_ml
------------------------------------------------------------
INSERT INTO apex_objeto_ei_filtro_ml (objeto_ei_filtro_ml_proyecto, objeto_ei_filtro_ml, ancho) VALUES (
	'toba_referencia', --objeto_ei_filtro_ml_proyecto
	'500', --objeto_ei_filtro_ml
	NULL  --ancho
);

------------------------------------------------------------
-- apex_objeto_ei_filtro_ml_col
------------------------------------------------------------
INSERT INTO apex_objeto_ei_filtro_ml_col (objeto_ei_filtro_ml_col, objeto_ei_filtro_ml, objeto_ei_filtro_ml_proyecto, tipo, nombre, etiqueta, descripcion, inicial, orden, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, edit_maximo, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'0', --objeto_ei_filtro_ml_col
	'500', --objeto_ei_filtro_ml
	'toba_referencia', --objeto_ei_filtro_ml_proyecto
	'booleano', --tipo
	'Booleano', --nombre
	'Campo1', --etiqueta
	NULL, --descripcion
	'0', --inicial
	'0', --orden
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --edit_maximo
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_ml_col (objeto_ei_filtro_ml_col, objeto_ei_filtro_ml, objeto_ei_filtro_ml_proyecto, tipo, nombre, etiqueta, descripcion, inicial, orden, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, edit_maximo, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'1', --objeto_ei_filtro_ml_col
	'500', --objeto_ei_filtro_ml
	'toba_referencia', --objeto_ei_filtro_ml_proyecto
	'cadena', --tipo
	'Cadena', --nombre
	'Campo2', --etiqueta
	NULL, --descripcion
	'0', --inicial
	'0', --orden
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --edit_maximo
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_ml_col (objeto_ei_filtro_ml_col, objeto_ei_filtro_ml, objeto_ei_filtro_ml_proyecto, tipo, nombre, etiqueta, descripcion, inicial, orden, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, edit_maximo, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'2', --objeto_ei_filtro_ml_col
	'500', --objeto_ei_filtro_ml
	'toba_referencia', --objeto_ei_filtro_ml_proyecto
	'fecha', --tipo
	'Fecha', --nombre
	'Campo3', --etiqueta
	NULL, --descripcion
	'0', --inicial
	'0', --orden
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --edit_maximo
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
INSERT INTO apex_objeto_ei_filtro_ml_col (objeto_ei_filtro_ml_col, objeto_ei_filtro_ml, objeto_ei_filtro_ml_proyecto, tipo, nombre, etiqueta, descripcion, inicial, orden, carga_metodo, carga_clase, carga_include, carga_dt, carga_consulta_php, carga_lista, carga_col_clave, carga_col_desc, carga_no_seteado, edit_maximo, popup_item, popup_proyecto, popup_editable, popup_ventana, popup_carga_desc_metodo, popup_carga_desc_clase, popup_carga_desc_include) VALUES (
	'3', --objeto_ei_filtro_ml_col
	'500', --objeto_ei_filtro_ml
	'toba_referencia', --objeto_ei_filtro_ml_proyecto
	'numero', --tipo
	'Número', --nombre
	'Campo4', --etiqueta
	NULL, --descripcion
	'0', --inicial
	'0', --orden
	NULL, --carga_metodo
	NULL, --carga_clase
	NULL, --carga_include
	NULL, --carga_dt
	NULL, --carga_consulta_php
	NULL, --carga_lista
	NULL, --carga_col_clave
	NULL, --carga_col_desc
	NULL, --carga_no_seteado
	NULL, --edit_maximo
	NULL, --popup_item
	NULL, --popup_proyecto
	NULL, --popup_editable
	NULL, --popup_ventana
	NULL, --popup_carga_desc_metodo
	NULL, --popup_carga_desc_clase
	NULL  --popup_carga_desc_include
);
