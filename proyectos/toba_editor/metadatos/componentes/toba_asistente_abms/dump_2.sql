------------------------------------------------------------
--[2]--   
------------------------------------------------------------

------------------------------------------------------------
-- apex_molde_operacion
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion (proyecto, molde, operacion_tipo, nombre, item, carpeta_archivos, prefijo_clases) VALUES (
	'toba_editor', --proyecto
	'2', --molde
	'10', --operacion_tipo
	NULL, --nombre
	'3401', --item
	'eeeee', --carpeta_archivos
	'eeeee'  --prefijo_clases
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_molde_operacion_abms
------------------------------------------------------------
INSERT INTO apex_molde_operacion_abms (proyecto, molde, tabla, gen_usa_filtro, gen_separar_pantallas, filtro_comprobar_parametros, fuente, cuadro_eof, cuadro_eliminar_filas, cuadro_id, cuadro_forzar_filtro, cuadro_carga_origen, cuadro_carga_sql, cuadro_carga_php_include, cuadro_carga_php_clase, cuadro_carga_php_metodo, datos_tabla_validacion, apdb_pre) VALUES (
	'toba_editor', --proyecto
	'2', --molde
	'apex_admin_param_previsualizazion', --tabla
	'0', --gen_usa_filtro
	NULL, --gen_separar_pantallas
	NULL, --filtro_comprobar_parametros
	NULL, --fuente
	NULL, --cuadro_eof
	NULL, --cuadro_eliminar_filas
	'proyecto,usuario', --cuadro_id
	NULL, --cuadro_forzar_filtro
	'datos_tabla', --cuadro_carga_origen
	'SELECT
	aapp.proyecto,
	aapp.usuario,
	aapp.grupo_acceso,
	aapp.punto_acceso
FROM
	apex_admin_param_previsualizazion as aapp', --cuadro_carga_sql
	NULL, --cuadro_carga_php_include
	NULL, --cuadro_carga_php_clase
	NULL, --cuadro_carga_php_metodo
	NULL, --datos_tabla_validacion
	NULL  --apdb_pre
);

------------------------------------------------------------
-- apex_molde_operacion_abms_fila
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'2', --molde
	'1', --fila
	'1', --orden
	'proyecto', --columna
	'1000001', --asistente_tipo_dato
	'Proyecto', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'15', --dt_largo
	'', --dt_secuencia
	'1', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_origen
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'2', --molde
	'2', --fila
	'2', --orden
	'usuario', --columna
	'1000001', --asistente_tipo_dato
	'Usuario', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'60', --dt_largo
	'', --dt_secuencia
	'1', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_origen
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'2', --molde
	'3', --fila
	'3', --orden
	'grupo_acceso', --columna
	'1000001', --asistente_tipo_dato
	'Grupo Acceso', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'255', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_origen
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'2', --molde
	'4', --fila
	'4', --orden
	'punto_acceso', --columna
	'1000001', --asistente_tipo_dato
	'Punto Acceso', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'100', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_origen
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
--- FIN Grupo de desarrollo 0
