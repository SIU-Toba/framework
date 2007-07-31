------------------------------------------------------------
--[1]--  Test A 
------------------------------------------------------------

------------------------------------------------------------
-- apex_molde_operacion
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion (proyecto, molde, operacion_tipo, nombre, carpeta_item, prefijo_clases, carpeta_archivos) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'10', --operacion_tipo
	'Test A', --nombre
	'3392', --carpeta_item
	'test_a_', --prefijo_clases
	'test_asistentes'  --carpeta_archivos
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_molde_operacion_abms
------------------------------------------------------------
INSERT INTO apex_molde_operacion_abms (proyecto, molde, tabla, gen_usa_filtro, gen_separar_pantallas, filtro_comprobar_parametros, cuadro_eof, cuadro_eliminar_filas, cuadro_id, cuadro_forzar_filtro, cuadro_carga_origen, cuadro_carga_sql, cuadro_carga_php_include, cuadro_carga_php_clase, cuadro_carga_php_metodo, datos_tabla_validacion, apdb_pre) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'apex_tipo_datos', --tabla
	'1', --gen_usa_filtro
	'0', --gen_separar_pantallas
	NULL, --filtro_comprobar_parametros
	'No hay filas', --cuadro_eof
	'1', --cuadro_eliminar_filas
	'tipo', --cuadro_id
	NULL, --cuadro_forzar_filtro
	'consulta_php', --cuadro_carga_origen
	'SELECT tipo, descripcion FROM apex_tipo_datos', --cuadro_carga_sql
	'test_asistentes/test_consulta_php2.php', --cuadro_carga_php_include
	'test_consulta_php2', --cuadro_carga_php_clase
	'get_tipos_dato', --cuadro_carga_php_metodo
	NULL, --datos_tabla_validacion
	NULL  --apdb_pre
);

------------------------------------------------------------
-- apex_molde_operacion_abms_fila
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'1', --fila
	'1', --orden
	'tipo', --columna
	NULL, --asistente_tipo_dato
	'Tipo', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	NULL, --cuadro_estilo
	NULL, --cuadro_formato
	'C', --dt_tipo_dato
	'1', --dt_largo
	NULL, --dt_secuencia
	'1', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'1', --molde
	'3', --fila
	'2', --orden
	'descripcion', --columna
	NULL, --asistente_tipo_dato
	'Descripcion', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	NULL, --cuadro_estilo
	NULL, --cuadro_formato
	'C', --dt_tipo_dato
	'30', --dt_largo
	NULL, --dt_secuencia
	'0', --dt_pk
	'ef_editable', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
--- FIN Grupo de desarrollo 0
