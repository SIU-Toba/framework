------------------------------------------------------------
--[6]--  ABM de Personas 
------------------------------------------------------------

------------------------------------------------------------
-- apex_molde_operacion
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_molde_operacion (proyecto, molde, operacion_tipo, nombre, carpeta_item, prefijo_clases, carpeta_archivos) VALUES (
	'jaiio', --proyecto
	'6', --molde
	'10', --operacion_tipo
	'ABM de Personas', --nombre
	'__raiz__', --carpeta_item
	'persona_', --prefijo_clases
	'personas'  --carpeta_archivos
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_molde_operacion_abms
------------------------------------------------------------
INSERT INTO apex_molde_operacion_abms (proyecto, molde, tabla, gen_usa_filtro, gen_separar_pantallas, filtro_comprobar_parametros, cuadro_eof, cuadro_eliminar_filas, cuadro_id, cuadro_forzar_filtro, cuadro_carga_origen, cuadro_carga_sql, cuadro_carga_php_include, cuadro_carga_php_clase, cuadro_carga_php_metodo, datos_tabla_validacion, apdb_pre) VALUES (
	'jaiio', --proyecto
	'6', --molde
	'personas', --tabla
	'0', --gen_usa_filtro
	NULL, --gen_separar_pantallas
	NULL, --filtro_comprobar_parametros
	NULL, --cuadro_eof
	'0', --cuadro_eliminar_filas
	'persona', --cuadro_id
	NULL, --cuadro_forzar_filtro
	'datos_tabla', --cuadro_carga_origen
	'SELECT
	p.persona,
	p.apellido,
	p.nombre,
	n.descripcion as nacionalidad,
	p.fecha_nacimiento
FROM
	personas as p	LEFT OUTER JOIN nacionalidades as n ON (p.nacionalidad = n.nacionalidad)', --cuadro_carga_sql
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
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'jaiio', --proyecto
	'6', --molde
	'4', --fila
	'1', --orden
	'persona', --columna
	'1000003', --asistente_tipo_dato
	'Persona', --etiqueta
	'0', --en_cuadro
	'0', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'0', --cuadro_estilo
	'7', --cuadro_formato
	NULL, --dt_tipo_dato
	'-1', --dt_largo
	'personas_persona_seq', --dt_secuencia
	'1', --dt_pk
	'ef_editable_numero', --elemento_formulario
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
	'jaiio', --proyecto
	'6', --molde
	'5', --fila
	'2', --orden
	'apellido', --columna
	'1000001', --asistente_tipo_dato
	'Apellido', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'30', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_editable', --elemento_formulario
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
	'jaiio', --proyecto
	'6', --molde
	'6', --fila
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
	'30', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_editable', --elemento_formulario
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
	'jaiio', --proyecto
	'6', --molde
	'7', --fila
	'4', --orden
	'nacionalidad', --columna
	'1000008', --asistente_tipo_dato
	'Nacionalidad', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	NULL, --filtro_operador
	'1', --cuadro_estilo
	'1', --cuadro_formato
	NULL, --dt_tipo_dato
	'-1', --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_combo', --elemento_formulario
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	'SELECT nacionalidad, descripcion FROM nacionalidades', --ef_carga_sql
	'consultas_jaiio.php', --ef_carga_php_include
	'consultas_jaiio', --ef_carga_php_clase
	'get_nacionalidades', --ef_carga_php_metodo
	'nacionalidades', --ef_carga_tabla
	'nacionalidad', --ef_carga_col_clave
	'descripcion'  --ef_carga_col_desc
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc) VALUES (
	'jaiio', --proyecto
	'6', --molde
	'8', --fila
	'5', --orden
	'fecha_nacimiento', --columna
	'1000004', --asistente_tipo_dato
	'Fecha Nacimiento', --etiqueta
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
	'ef_editable', --elemento_formulario
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
--- FIN Grupo de desarrollo 0
