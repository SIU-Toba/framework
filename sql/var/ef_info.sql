------  'apex_objeto_abms_ef'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica) VALUES ('toba', 'apex_objeto_abms_ef', 'pgsql_a13_clase_abms.sql', 3, 'multiproyecto', NULL, '( objeto_abms_proyecto = \'%%\' )', NULL, NULL, NULL, '', '1.0', '0');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'objeto_abms_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'objeto_abms', 2, 'int4			   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'identificador', 3, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'columnas', 4, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'clave_primaria', 5, 'smallint       NULL,			-- El contenido de este EF es parte de una clave primaria?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'obligatorio', 6, 'smallint       NULL,			-- El contenido de este EF es obligatorio?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'elemento_formulario', 7, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'inicializacion', 8, 'varchar        NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'orden', 9, 'float       	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'etiqueta', 10, 'varchar(40)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'descripcion', 11, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_abms_ef', 'desactivado', 12, 'smallint       NULL,');

