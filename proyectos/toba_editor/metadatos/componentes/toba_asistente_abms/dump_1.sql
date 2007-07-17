------------------------------------------------------------
--[1]--  Test A 
------------------------------------------------------------

------------------------------------------------------------
-- apex_molde_operacion
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion (proyecto, molde, operacion_tipo, nombre, carpeta_item, carpeta_archivos) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'10', --operacion_tipo
	'Test A', --nombre
	'3392', --carpeta_item
	'test_asistentes'  --carpeta_archivos
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_molde_operacion_abms
------------------------------------------------------------
INSERT INTO apex_molde_operacion_abms (proyecto, molde, tabla, gen_usa_filtro, gen_separar_pantallas, cuadro_eof, cuadro_eliminar_filas, cuadro_id, cuadro_carga, cuadro_carga_sql, cuadro_carga_php_include, cuadro_carga_php_clase, cuadro_carga_php_metodo, datos_tabla_validacion, apdb_pre) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'apex_tipo_datos', --tabla
	'0', --gen_usa_filtro
	'0', --gen_separar_pantallas
	'No hay filas', --cuadro_eof
	NULL, --cuadro_eliminar_filas
	'tipo', --cuadro_id
	'ci', --cuadro_carga
	'SELECT tipo, descripcion FROM apex_tipo_datos', --cuadro_carga_sql
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
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, etiqueta, en_cuadro, en_form, en_filtro, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'1', --fila
	'1', --orden
	'tipo', --columna
	'Tipo', --etiqueta
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
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, etiqueta, en_cuadro, en_form, en_filtro, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'3', --fila
	'2', --orden
	'descripcion', --columna
	'Descripcion', --etiqueta
	NULL, --en_cuadro
	NULL, --en_form
	NULL, --en_filtro
	'C', --dt_tipo_dato
	'30', --dt_largo
	NULL, --dt_secuencia
	NULL, --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
--- FIN Grupo de desarrollo 0
