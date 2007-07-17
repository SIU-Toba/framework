------------------------------------------------------------
--[1]--  Test A 
------------------------------------------------------------

------------------------------------------------------------
-- apex_molde_operacion
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion (proyecto, molde, operacion_tipo, nombre, carpeta_item, carpeta_archivos) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'10', --operacion_tipo
	'Test C', --nombre
	'3392', --carpeta_item
	'test_asistentes'  --carpeta_archivos
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_molde_operacion_abms
------------------------------------------------------------
INSERT INTO apex_molde_operacion_abms (proyecto, molde, tabla, gen_usa_filtro, gen_separar_pantallas, cuadro_eof, cuadro_id, cuadro_eliminar_filas, cuadro_datos_origen, cuadro_datos_origen_ci_sql, cuadro_datos_origen_php_archivo, cuadro_datos_origen_php_clase, cuadro_datos_origen_php_metodo, datos_tabla_validacion, apdb_pre) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'apex_usuario', --tabla
	'0', --gen_usa_filtro
	'0', --gen_separar_pantallas
	'No hay filas', --cuadro_eof
	NULL, --cuadro_id
	NULL, --cuadro_eliminar_filas
	NULL, --cuadro_datos_origen
	NULL, --cuadro_datos_origen_ci_sql
	NULL, --cuadro_datos_origen_php_archivo
	NULL, --cuadro_datos_origen_php_clase
	NULL, --cuadro_datos_origen_php_metodo
	NULL, --datos_tabla_validacion
	NULL  --apdb_pre
);

------------------------------------------------------------
-- apex_molde_operacion_abms_fila
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, etiqueta, en_cuadro, en_form, en_filtro, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga, ef_carga_ci_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1', --fila
	'1', --orden
	'usuario', --columna
	'Usuario', --etiqueta
	NULL, --en_cuadro
	NULL, --en_form
	NULL, --en_filtro
	'C', --dt_tipo_dato
	'1', --dt_largo
	NULL, --dt_secuencia
	'1', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga
	NULL, --ef_carga_ci_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL  --ef_carga_php_metodo
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, etiqueta, en_cuadro, en_form, en_filtro, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga, ef_carga_ci_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'2', --fila
	'2', --orden
	'clave', --columna
	'Clave', --etiqueta
	NULL, --en_cuadro
	NULL, --en_form
	NULL, --en_filtro
	'C', --dt_tipo_dato
	'30', --dt_largo
	NULL, --dt_secuencia
	NULL, --dt_pk
	'ef_editable_clave', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga
	NULL, --ef_carga_ci_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL  --ef_carga_php_metodo
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, etiqueta, en_cuadro, en_form, en_filtro, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga, ef_carga_ci_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'3', --fila
	'3', --orden
	'usuario_tipodoc', --columna
	'Tipo Doc.', --etiqueta
	NULL, --en_cuadro
	NULL, --en_form
	NULL, --en_filtro
	'C', --dt_tipo_dato
	'1', --dt_largo
	NULL, --dt_secuencia
	NULL, --dt_pk
	'ef_combo', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga
	'SELECT usuario_tipodoc, descripcion FROM apex_usuario_tipodoc', --ef_carga_ci_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, etiqueta, en_cuadro, en_form, en_filtro, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga, ef_carga_ci_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'4', --fila
	'4', --orden
	'ciu', --columna
	'Documento', --etiqueta
	NULL, --en_cuadro
	NULL, --en_form
	NULL, --en_filtro
	'C', --dt_tipo_dato
	'20', --dt_largo
	NULL, --dt_secuencia
	'1', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga
	NULL, --ef_carga_ci_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL  --ef_carga_php_metodo
);
----- FIN Grupo de desarrollo 0
