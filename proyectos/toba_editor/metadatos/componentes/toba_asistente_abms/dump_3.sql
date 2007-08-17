------------------------------------------------------------
--[3]--  Test C 
------------------------------------------------------------

------------------------------------------------------------
-- apex_molde_operacion
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion (proyecto, molde, operacion_tipo, nombre, carpeta_item, prefijo_clases, carpeta_archivos) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'10', --operacion_tipo
	'Test C', --nombre
	'3392', --carpeta_item
	'test_c_', --prefijo_clases
	'test_asistentes'  --carpeta_archivos
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_molde_operacion_abms
------------------------------------------------------------
INSERT INTO apex_molde_operacion_abms (proyecto, molde, tabla, gen_usa_filtro, gen_separar_pantallas, filtro_comprobar_parametros, cuadro_eof, cuadro_eliminar_filas, cuadro_id, cuadro_forzar_filtro, cuadro_carga_origen, cuadro_carga_sql, cuadro_carga_php_include, cuadro_carga_php_clase, cuadro_carga_php_metodo, datos_tabla_validacion, apdb_pre) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'apex_usuario', --tabla
	'1', --gen_usa_filtro
	'0', --gen_separar_pantallas
	NULL, --filtro_comprobar_parametros
	'No hay filas', --cuadro_eof
	'0', --cuadro_eliminar_filas
	'usuario', --cuadro_id
	NULL, --cuadro_forzar_filtro
	'consulta_php', --cuadro_carga_origen
	'SELECT
	au.usuario,
	au.clave,
	au.nombre,
	aut.descripcion as usuario_tipodoc,
	au.pre,
	au.ciu,
	au.suf,
	au.email,
	au.telefono,
	au.vencimiento,
	au.dias,
	au.hora_entrada,
	au.hora_salida,
	au.ip_permitida,
	au.solicitud_registrar,
	au.solicitud_obs_tipo_proyecto,
	au.solicitud_obs_tipo,
	au.solicitud_observacion,
	au.parametro_a,
	au.parametro_b,
	au.parametro_c,
	au.autentificacion
FROM
	apex_usuario as au,
	apex_usuario_tipodoc as aut
WHERE
	au.usuario_tipodoc = aut.usuario_tipodoc', --cuadro_carga_sql
	'{toba_modelo}/info/toba_info_editores.php', --cuadro_carga_php_include
	'toba_info_editores', --cuadro_carga_php_clase
	'otro', --cuadro_carga_php_metodo
	NULL, --datos_tabla_validacion
	NULL  --apdb_pre
);

------------------------------------------------------------
-- apex_molde_operacion_abms_fila
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000110', --fila
	'1', --orden
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
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000111', --fila
	'2', --orden
	'clave', --columna
	'1000001', --asistente_tipo_dato
	'Clave', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'128', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000112', --fila
	'3', --orden
	'nombre', --columna
	'1000001', --asistente_tipo_dato
	'Nombre', --etiqueta
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
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000113', --fila
	'4', --orden
	'usuario_tipodoc', --columna
	'1000008', --asistente_tipo_dato
	'Usuario Tipodoc', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'1', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'10', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	'SELECT usuario_tipodoc, descripcion FROM apex_usuario_tipodoc', --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	'apex_usuario_tipodoc', --ef_carga_tabla
	'usuario_tipodoc', --ef_carga_col_clave
	'descripcion'  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000114', --fila
	'5', --orden
	'pre', --columna
	'1000001', --asistente_tipo_dato
	'Pre', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'2', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000115', --fila
	'6', --orden
	'ciu', --columna
	'1000001', --asistente_tipo_dato
	'Ciu', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'18', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000116', --fila
	'7', --orden
	'suf', --columna
	'1000001', --asistente_tipo_dato
	'Suf', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'1', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000117', --fila
	'8', --orden
	'email', --columna
	'1000001', --asistente_tipo_dato
	'Email', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'1', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'80', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000118', --fila
	'9', --orden
	'telefono', --columna
	'1000001', --asistente_tipo_dato
	'Telefono', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'1', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'18', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000119', --fila
	'10', --orden
	'vencimiento', --columna
	'1000001', --asistente_tipo_dato
	'Vencimiento', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'-1', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000120', --fila
	'11', --orden
	'dias', --columna
	'1000003', --asistente_tipo_dato
	'Dias', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'0', --cuadro_estilo
	'7', --cuadro_formato
	NULL, --dt_tipo_dato
	'-1', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000121', --fila
	'12', --orden
	'hora_entrada', --columna
	'1000001', --asistente_tipo_dato
	'Hora Entrada', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'0', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000122', --fila
	'13', --orden
	'hora_salida', --columna
	'1000001', --asistente_tipo_dato
	'Hora Salida', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'0', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000123', --fila
	'14', --orden
	'ip_permitida', --columna
	'1000001', --asistente_tipo_dato
	'Ip Permitida', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'20', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000124', --fila
	'15', --orden
	'solicitud_registrar', --columna
	'1000003', --asistente_tipo_dato
	'Solicitud Registrar', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'0', --cuadro_estilo
	'7', --cuadro_formato
	NULL, --dt_tipo_dato
	'-1', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000125', --fila
	'16', --orden
	'solicitud_obs_tipo_proyecto', --columna
	'1000001', --asistente_tipo_dato
	'Solicitud Obs Tipo Proyecto', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'15', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000126', --fila
	'17', --orden
	'solicitud_obs_tipo', --columna
	'1000001', --asistente_tipo_dato
	'Solicitud Obs Tipo', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'20', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000127', --fila
	'18', --orden
	'solicitud_observacion', --columna
	'1000001', --asistente_tipo_dato
	'Solicitud Observacion', --etiqueta
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
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000128', --fila
	'19', --orden
	'parametro_a', --columna
	'1000001', --asistente_tipo_dato
	'Parametro A', --etiqueta
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
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000129', --fila
	'20', --orden
	'parametro_b', --columna
	'1000001', --asistente_tipo_dato
	'Parametro B', --etiqueta
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
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000130', --fila
	'21', --orden
	'parametro_c', --columna
	'1000001', --asistente_tipo_dato
	'Parametro C', --etiqueta
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
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'toba_editor', --proyecto
	'3', --molde
	'1000131', --fila
	'22', --orden
	'autentificacion', --columna
	'1000001', --asistente_tipo_dato
	'Autentificacion', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'10', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	NULL, --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL  --ef_carga_col_desc
);
--- FIN Grupo de desarrollo 1
