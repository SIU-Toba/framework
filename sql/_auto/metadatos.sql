
--- Utilizando instancia: desarrollo


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a00_revision.sql
--####
--######################################################################################

------  'apex_revision'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_revision', 'pgsql_a00_revision.sql', 1, 'proyecto', NULL, NULL, 'revision', NULL, NULL, 'Especifica la revision del SVN con que se creo el proyecto', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_revision', 'revision', 1, 'varchar(20)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_revision', 'creacion', 2, 'timestamp(0) without	time zone	DEFAULT current_timestamp NOT	NULL');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a01_nucleo.sql
--####
--######################################################################################

------  'apex_elemento_infra'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_elemento_infra', 'pgsql_a01_nucleo.sql', 2, 'proyecto', NULL, NULL, 'elemento_infra', NULL, NULL, 'Representa	un	elemento	de	la	infraestructura', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra', 'elemento_infra', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra', 'descripcion', 2, 'varchar(255)	NOT NULL,');

------  'apex_elemento_infra_tabla'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_elemento_infra_tabla', 'pgsql_a01_nucleo.sql', 3, 'proyecto', NULL, NULL, 'elemento_infra, tabla', NULL, NULL, 'Representa	una tabla donde se almacena parte del elemento', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'elemento_infra', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'tabla', 2, 'varchar(30)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'columna_clave_proyecto', 3, 'varchar(40)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'columna_clave', 4, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'orden', 5, 'smallint		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'descripcion', 6, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'dependiente', 7, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'proc_borrar', 8, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'proc_exportar', 9, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'proc_clonar', 10, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_tabla', 'obligatoria', 11, 'smallint		NULL,');

------  'apex_elemento_infra_input'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_elemento_infra_input', 'pgsql_a01_nucleo.sql', 4, 'proyecto', NULL, NULL, 'entrada', NULL, NULL, 'En esta tabla se guardan los elementos toba recibidos desde otras instancias', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'entrada', 1, 'int4			DEFAULT nextval(\'\"apex_elemento_infra_input_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'elemento_infra', 2, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'descripcion', 3, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'ip_origen', 4, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'ip_destino', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'datos', 6, 'text			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'datos2_test', 7, 'text			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_infra_input', 'ingreso', 8, 'timestamp(0) without	time zone	DEFAULT current_timestamp NOT	NULL,');

------  'apex_estilo_paleta'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_estilo_paleta', 'pgsql_a01_nucleo.sql', 5, 'proyecto', NULL, NULL, 'estilo_paleta', NULL, NULL, 'Representa	una serie de colores', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo_paleta', 'estilo_paleta', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo_paleta', 'color_1', 2, 'char(6)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo_paleta', 'color_2', 3, 'char(6)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo_paleta', 'color_3', 4, 'char(6)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo_paleta', 'color_4', 5, 'char(6)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo_paleta', 'color_5', 6, 'char(6)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo_paleta', 'color_6', 7, 'char(6)			NULL,');

------  'apex_estilo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_estilo', 'pgsql_a01_nucleo.sql', 6, 'proyecto', NULL, NULL, 'estilo', NULL, NULL, 'Estilos	CSS', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo', 'estilo', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo', 'descripcion', 2, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo', 'estilo_paleta_p', 3, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo', 'estilo_paleta_s', 4, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo', 'estilo_paleta_n', 5, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_estilo', 'estilo_paleta_e', 6, 'varchar(15)		NULL,');

------  'apex_menu'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_menu', 'pgsql_a01_nucleo.sql', 7, 'proyecto', NULL, NULL, 'menu', NULL, NULL, 'Tipos de menues', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_menu', 'menu', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_menu', 'descripcion', 2, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_menu', 'archivo', 3, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_menu', 'soporta_frames', 4, 'smallint		NULL,');

------  'apex_proyecto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_proyecto', 'pgsql_a01_nucleo.sql', 8, 'multiproyecto', NULL, NULL, 'proyecto', NULL, NULL, 'Tabla maestra	de	proyectos', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'descripcion', 2, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'descripcion_corta', 3, 'varchar(40)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'estilo', 4, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'frames_clase', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'frames_archivo', 6, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'menu', 7, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'path_includes', 8, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'path_browser', 9, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'administrador', 10, 'varchar(60)		NULL,--NOT');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'listar_multiproyecto', 11, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'orden', 12, 'float				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_proyecto', 'palabra_vinculo_std', 13, 'varchar(30)		NULL,');

------  'apex_log_sistema_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_log_sistema_tipo', 'pgsql_a01_nucleo.sql', 9, 'proyecto', NULL, NULL, 'log_sistema_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_sistema_tipo', 'log_sistema_tipo', 1, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_sistema_tipo', 'descripcion', 2, 'varchar(255)	NOT NULL,');

------  'apex_instancia'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_instancia', 'pgsql_a01_nucleo.sql', 10, 'proyecto', NULL, NULL, 'instancia', NULL, NULL, 'Datos de la instancia', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'instancia', 1, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'version', 2, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'institucion', 3, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'observaciones', 4, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'administrador_1', 5, 'varchar(60)		NULL,--NOT');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'administrador_2', 6, 'varchar(60)		NULL,--NOT');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'administrador_3', 7, 'varchar(60)		NULL,--NOT');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_instancia', 'creacion', 8, 'timestamp(0) without	time zone	DEFAULT current_timestamp NOT	NULL,');

------  'apex_fuente_datos_motor'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_fuente_datos_motor', 'pgsql_a01_nucleo.sql', 11, 'proyecto', NULL, NULL, 'fuente_datos_motor', NULL, NULL, 'DBMS	soportados', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos_motor', 'fuente_datos_motor', 1, 'varchar(30)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos_motor', 'nombre', 2, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos_motor', 'version', 3, 'varchar(30)		NOT NULL,');

------  'apex_fuente_datos'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_fuente_datos', 'pgsql_a01_nucleo.sql', 12, 'multiproyecto', NULL, NULL, 'fuente_datos', NULL, NULL, 'Bases de datos a	las que se puede acceder', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'fuente_datos', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'fuente_datos_motor', 3, 'varchar(30)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'descripcion', 4, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'descripcion_corta', 5, 'varchar(40)		NULL,	--	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'host', 6, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'usuario', 7, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'clave', 8, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'base', 9, 'varchar(30)		NULL,	--	NOT? ODBC e	instancia no la utilizan...');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'administrador', 10, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'link_instancia', 11, 'smallint		NULL,	--	En	vez de abrir una conexion,	utilizar	la	conexion	a la intancia');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'instancia_id', 12, 'varchar(30)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'subclase_archivo', 13, 'varchar(255) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'subclase_nombre', 14, 'varchar(60) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_fuente_datos', 'orden', 15, 'smallint		NULL,');

------  'apex_grafico'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_grafico', 'pgsql_a01_nucleo.sql', 13, 'proyecto', NULL, NULL, 'grafico', NULL, NULL, 'Tipo	de	grafico', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_grafico', 'grafico', 1, 'varchar(30)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_grafico', 'descripcion_corta', 2, 'varchar(40)			NULL,	--NOT');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_grafico', 'descripcion', 3, 'varchar(255)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_grafico', 'parametros', 4, 'varchar				NULL,');

------  'apex_recurso_origen'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_recurso_origen', 'pgsql_a01_nucleo.sql', 14, 'proyecto', NULL, NULL, 'recurso_origen', NULL, NULL, 'Origen del	recurso', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_recurso_origen', 'recurso_origen', 1, 'varchar(10)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_recurso_origen', 'descripcion', 2, 'varchar(255)		NOT NULL,');

------  'apex_repositorio'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_repositorio', 'pgsql_a01_nucleo.sql', 15, 'proyecto', NULL, NULL, 'repositorio', NULL, NULL, 'Listado	de	repositorios a	los que me puedo conectar', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_repositorio', 'repositorio', 1, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_repositorio', 'descripcion', 2, 'varchar(255)	NULL,');

------  'apex_nivel_acceso'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nivel_acceso', 'pgsql_a01_nucleo.sql', 16, 'proyecto', NULL, NULL, 'nivel_acceso', NULL, NULL, 'Categoria organizadora	de	niveles de seguridad	(redobla	la	cualificaciond	e elementos	para fortalecer chequeos)', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nivel_acceso', 'nivel_acceso', 1, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nivel_acceso', 'nombre', 2, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nivel_acceso', 'descripcion', 3, 'varchar			NULL,');

------  'apex_nivel_ejecucion'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nivel_ejecucion', 'pgsql_a01_nucleo.sql', 17, 'proyecto', NULL, NULL, 'nivel_ejecucion', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nivel_ejecucion', 'nivel_ejecucion', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nivel_ejecucion', 'descripcion', 2, 'varchar(255)	NOT NULL,');

------  'apex_solicitud_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_tipo', 'pgsql_a01_nucleo.sql', 18, 'proyecto', NULL, NULL, 'solicitud_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_tipo', 'solicitud_tipo', 1, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_tipo', 'descripcion', 2, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_tipo', 'descripcion_corta', 3, 'varchar(40)		NULL,	--	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_tipo', 'icono', 4, 'varchar(30)		NULL,');

------  'apex_elemento_formulario'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_elemento_formulario', 'pgsql_a01_nucleo.sql', 19, 'multiproyecto', NULL, NULL, 'padre, elemento_formulario', NULL, NULL, 'Elementos de formulario soportados', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_formulario', 'elemento_formulario', 1, 'varchar(30)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_formulario', 'padre', 2, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_formulario', 'descripcion', 3, 'text			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_formulario', 'parametros', 4, 'varchar			NULL,	--	Lista de los parametros	que recibe este EF');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_formulario', 'proyecto', 5, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_elemento_formulario', 'exclusivo_toba', 6, 'smallint		NULL,');

------  'apex_solicitud_obs_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_obs_tipo', 'pgsql_a01_nucleo.sql', 20, 'multiproyecto', NULL, NULL, 'solicitud_obs_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obs_tipo', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obs_tipo', 'solicitud_obs_tipo', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obs_tipo', 'descripcion', 3, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obs_tipo', 'criterio', 4, 'varchar(20)		NOT NULL,');

------  'apex_pagina_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_pagina_tipo', 'pgsql_a01_nucleo.sql', 21, 'multiproyecto', NULL, NULL, 'pagina_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'pagina_tipo', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'descripcion', 3, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'clase_nombre', 4, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'clase_archivo', 5, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'include_arriba', 6, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'include_abajo', 7, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'exclusivo_toba', 8, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pagina_tipo', 'contexto', 9, 'varchar(255)	NULL,	--	Establece variables de CONTEXTO?	Cuales?');

------  'apex_columna_estilo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_columna_estilo', 'pgsql_a01_nucleo.sql', 22, 'proyecto', NULL, NULL, 'columna_estilo', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_estilo', 'columna_estilo', 1, 'int4				DEFAULT nextval(\'\"apex_columna_estilo_seq\"\'::text)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_estilo', 'css', 2, 'varchar(40)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_estilo', 'descripcion', 3, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_estilo', 'descripcion_corta', 4, 'varchar(40)	  NULL,');

------  'apex_columna_formato'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_columna_formato', 'pgsql_a01_nucleo.sql', 23, 'proyecto', NULL, NULL, 'columna_formato', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_formato', 'columna_formato', 1, 'int4				DEFAULT nextval(\'\"apex_columna_formato_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_formato', 'funcion', 2, 'varchar(40)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_formato', 'archivo', 3, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_formato', 'descripcion', 4, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_formato', 'descripcion_corta', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_formato', 'parametros', 6, 'varchar(255)	NULL,');

------  'apex_columna_proceso'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_columna_proceso', 'pgsql_a01_nucleo.sql', 24, 'proyecto', NULL, NULL, 'columna_proceso', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_proceso', 'columna_proceso', 1, 'int4				DEFAULT nextval(\'\"apex_columna_proceso_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_proceso', 'funcion', 2, 'varchar(40)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_proceso', 'archivo', 3, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_proceso', 'descripcion', 4, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_proceso', 'descripcion_corta', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_columna_proceso', 'parametros', 6, 'varchar(255)	NULL,');

------  'apex_pdf_propiedad'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_pdf_propiedad', 'pgsql_a01_nucleo.sql', 25, 'multiproyecto', NULL, NULL, 'pdf_propiedad', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pdf_propiedad', 'pdf_propiedad', 1, 'varchar(30) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pdf_propiedad', 'descripcion', 2, 'varchar(255) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pdf_propiedad', 'requerido', 3, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pdf_propiedad', 'proyecto', 4, 'varchar(15) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pdf_propiedad', 'exclusiva_columna', 5, 'smallint 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_pdf_propiedad', 'exclusiva_tabla', 6, 'smallint 		NULL,');

------  'apex_usuario_tipodoc'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_usuario_tipodoc', 'pgsql_a01_nucleo.sql', 26, 'proyecto', NULL, NULL, 'usuario_tipodoc', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_tipodoc', 'usuario_tipodoc', 1, 'varchar(10)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_tipodoc', 'descripcion', 2, 'varchar(40)		NOT NULL,');

------  'apex_usuario'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_usuario', 'pgsql_a01_nucleo.sql', 27, 'proyecto', NULL, NULL, 'usuario', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'usuario', 1, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'clave', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'nombre', 3, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'usuario_tipodoc', 4, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'pre', 5, 'varchar(2)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'ciu', 6, 'varchar(18)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'suf', 7, 'varchar(1)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'email', 8, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'telefono', 9, 'varchar(18)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'vencimiento', 10, 'date				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'dias', 11, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'hora_entrada', 12, 'time(0) without time	zone NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'hora_salida', 13, 'time(0) without time	zone NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'ip_permitida', 14, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'solicitud_registrar', 15, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'solicitud_obs_tipo_proyecto', 16, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'solicitud_obs_tipo', 17, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'solicitud_observacion', 18, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'parametro_a', 19, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'parametro_b', 20, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario', 'parametro_c', 21, 'varchar(100)	NULL,');

------  'apex_usuario_perfil_datos'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_usuario_perfil_datos', 'pgsql_a01_nucleo.sql', 28, 'multiproyecto', NULL, NULL, 'usuario_perfil_datos', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_perfil_datos', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_perfil_datos', 'usuario_perfil_datos', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_perfil_datos', 'nombre', 3, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_perfil_datos', 'descripcion', 4, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_perfil_datos', 'listar', 5, 'smallint			NULL,');

------  'apex_usuario_grupo_acc'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_usuario_grupo_acc', 'pgsql_a01_nucleo.sql', 29, 'multiproyecto', NULL, NULL, 'usuario_grupo_acc', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'usuario_grupo_acc', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'nombre', 3, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'nivel_acceso', 4, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'descripcion', 5, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'vencimiento', 6, 'date				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'dias', 7, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'hora_entrada', 8, 'time(0) without time	zone NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'hora_salida', 9, 'time(0) without time	zone NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc', 'listar', 10, 'smallint			NULL,');

------  'apex_usuario_proyecto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_usuario_proyecto', 'pgsql_a01_nucleo.sql', 30, 'multiproyecto', NULL, NULL, 'usuario', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_proyecto', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_proyecto', 'usuario', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_proyecto', 'usuario_grupo_acc', 3, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_proyecto', 'usuario_perfil_datos', 4, 'varchar(20)		NOT NULL,');

------  'apex_patron'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_patron', 'pgsql_a01_nucleo.sql', 31, 'multiproyecto', NULL, NULL, 'patron', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron', 'patron', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron', 'archivo', 3, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron', 'descripcion', 4, 'varchar(250)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron', 'descripcion_corta', 5, 'varchar(40)		NULL,	--	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron', 'exclusivo_toba', 6, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron', 'autodoc', 7, 'smallint			NULL,');

------  'apex_patron_info'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_patron_info', 'pgsql_a01_nucleo.sql', 32, 'multiproyecto', NULL, '(	patron_proyecto =	\'%%\' )', 'patron', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_info', 'patron_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_info', 'patron', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_info', 'descripcion_breve', 3, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_info', 'descripcion_larga', 4, 'text			NULL,');

------  'apex_buffer'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_buffer', 'pgsql_a01_nucleo.sql', 33, 'multiproyecto', NULL, NULL, 'buffer', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_buffer', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_buffer', 'buffer', 2, 'int4			DEFAULT nextval(\'\"apex_buffer_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_buffer', 'descripcion_corta', 3, 'varchar(40)		NULL,	--	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_buffer', 'descripcion', 4, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_buffer', 'cuerpo', 5, 'text			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_buffer', 'archivo_origen', 6, 'varchar(150)	NULL,');

------  'apex_item_zona'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_item_zona', 'pgsql_a01_nucleo.sql', 34, 'multiproyecto', NULL, NULL, 'zona', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_zona', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_zona', 'zona', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_zona', 'nombre', 3, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_zona', 'clave_editable', 4, 'varchar(100)	NULL,	--	Clave	del EDITABLE manejado en la ZONA');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_zona', 'archivo', 5, 'varchar(80)		NOT NULL, -- Archivo	donde	reside la clase que representa la ZONA');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_zona', 'descripcion', 6, 'varchar			NULL,');

------  'apex_item'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_item', 'pgsql_a01_nucleo.sql', 35, 'multiproyecto', NULL, NULL, 'item', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'item_id', 1, 'int4			DEFAULT nextval(\'\"apex_item_seq\"\'::text) NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'proyecto', 2, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'item', 3, 'varchar(60)		DEFAULT nextval(\'\"apex_item_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'padre_id', 4, 'int4			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'padre_proyecto', 5, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'padre', 6, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'carpeta', 7, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'nivel_acceso', 8, 'smallint		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'solicitud_tipo', 9, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'pagina_tipo_proyecto', 10, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'pagina_tipo', 11, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'nombre', 12, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'descripcion', 13, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'actividad_buffer_proyecto', 14, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'actividad_buffer', 15, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'actividad_patron_proyecto', 16, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'actividad_patron', 17, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'actividad_accion', 18, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'menu', 19, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'orden', 20, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'solicitud_registrar', 21, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'solicitud_obs_tipo_proyecto', 22, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'solicitud_obs_tipo', 23, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'solicitud_observacion', 24, 'varchar(90)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'solicitud_registrar_cron', 25, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'prueba_directorios', 26, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'zona_proyecto', 27, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'zona', 28, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'zona_orden', 29, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'zona_listar', 30, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'imagen_recurso_origen', 31, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'imagen', 32, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'parametro_a', 33, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'parametro_b', 34, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'parametro_c', 35, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'publico', 36, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'usuario', 37, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item', 'creacion', 38, 'timestamp(0)	without time zone	DEFAULT current_timestamp NULL,');

------  'apex_item_info'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_item_info', 'pgsql_a01_nucleo.sql', 36, 'multiproyecto', NULL, '(	item_proyecto = \'%%\'	)', 'item', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_info', 'item_id', 1, 'int4				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_info', 'item_proyecto', 2, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_info', 'item', 3, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_info', 'descripcion_breve', 4, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_info', 'descripcion_larga', 5, 'text				NULL,');

------  'apex_clase_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_tipo', 'pgsql_a01_nucleo.sql', 37, 'proyecto', NULL, NULL, 'clase_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_tipo', 'clase_tipo', 1, 'int4				DEFAULT nextval(\'\"apex_clase_tipo_seq\"\'::text) NOT	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_tipo', 'descripcion_corta', 2, 'varchar(40)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_tipo', 'descripcion', 3, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_tipo', 'icono', 4, 'varchar(30)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_tipo', 'orden', 5, 'float				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_tipo', 'metodologia', 6, 'varchar(10)			NULL, --NOT');

------  'apex_clase'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase', 'pgsql_a01_nucleo.sql', 38, 'multiproyecto', NULL, NULL, 'clase', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'clase', 2, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'clase_tipo', 3, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'archivo', 4, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'descripcion', 5, 'varchar(250)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'descripcion_corta', 6, 'varchar(40)		NULL,	--	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'icono', 7, 'varchar(60)		NOT NULL, --> Icono con	el	que los objetos de la clase aparecen representados	en	las listas');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'screenshot', 8, 'varchar(60)		NULL,	--> Path a una imagen de la clase');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'ancestro_proyecto', 9, 'varchar(15)		NULL,	--> Ancestro a	considerar para incluir	dependencias');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'ancestro', 10, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'instanciador_id', 11, 'int4			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'instanciador_proyecto', 12, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'instanciador_item', 13, 'varchar(60)		NULL,	--> Item	del catalogo a	invocar como instanciador de objetos de esta	clase');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'editor_id', 14, 'int4			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'editor_proyecto', 15, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'editor_item', 16, 'varchar(60)		NULL,	--> Item	del catalogo a	invocar como editor de objetos de esta	clase');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'editor_ancestro_proyecto', 17, 'varchar(15)		NULL,	--> Ancestro a	considerar para el EDITOR');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'editor_ancestro', 18, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'plan_dump_objeto', 19, 'varchar(255)	NULL, --> Lista ordenada de tablas	que poseen la definicion del objeto	(respetar FK!)');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'sql_info', 20, 'text			NULL, --> SQL	que DUMPEA el estado	del objeto');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'doc_clase', 21, 'varchar(255)	NULL,			--> GIF donde hay	un	Diagrama	de	clases.');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'doc_db', 22, 'varchar(255)	NULL,			--> GIF donde hay	un	DER de las tablas	que necesita la clase.');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'doc_sql', 23, 'varchar(255)	NULL,			--> path	al	archivo que	crea las	tablas.');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'vinculos', 24, 'smallint		NULL,			--> Indica si los	objetos generados	pueden tener vinculos');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'autodoc', 25, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'parametro_a', 26, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'parametro_b', 27, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'parametro_c', 28, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase', 'exclusivo_toba', 29, 'smallint		NULL,');

------  'apex_clase_info'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_info', 'pgsql_a01_nucleo.sql', 39, 'multiproyecto', NULL, '(	clase_proyecto	= \'%%\' )', 'clase', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_info', 'clase_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_info', 'clase', 2, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_info', 'descripcion_breve', 3, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_info', 'descripcion_larga', 4, 'text			NULL,');

------  'apex_clase_dependencias'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_dependencias', 'pgsql_a01_nucleo.sql', 40, 'multiproyecto', NULL, '(	clase_consumidora_proyecto	= \'%%\' )', 'clase_consumidora, identificador', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_dependencias', 'clase_consumidora_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_dependencias', 'clase_consumidora', 2, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_dependencias', 'identificador', 3, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_dependencias', 'descripcion', 4, 'varchar(250)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_dependencias', 'clase_proveedora_proyecto', 5, 'varchar(15)			NOT NULL,	--	Las dependencias pueden	ser de esta	clase	o de una	heredada');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_dependencias', 'clase_proveedora', 6, 'varchar(60)		NOT NULL,');

------  'apex_patron_dependencias'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_patron_dependencias', 'pgsql_a01_nucleo.sql', 41, 'multiproyecto', NULL, '(	patron_proyecto =	\'%%\' )', 'patron, clase', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_dependencias', 'patron_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_dependencias', 'patron', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_dependencias', 'clase_proyecto', 3, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_dependencias', 'clase', 4, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_dependencias', 'cantidad_minima', 5, 'smallint		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_dependencias', 'cantidad_maxima', 6, 'smallint		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_dependencias', 'descripcion', 7, 'varchar(250)	NULL,');

------  'apex_objeto_categoria'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_categoria', 'pgsql_a01_nucleo.sql', 42, 'multiproyecto', NULL, NULL, 'objeto_categoria', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_categoria', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_categoria', 'objeto_categoria', 2, 'varchar(30)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_categoria', 'descripcion', 3, 'varchar(255)	NULL,');

------  'apex_solicitud_obj_obs_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_obj_obs_tipo', 'pgsql_a01_nucleo.sql', 43, 'multiproyecto', NULL, '(	clase_proyecto	= \'%%\' )', 'solicitud_obj_obs_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_obs_tipo', 'solicitud_obj_obs_tipo', 1, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_obs_tipo', 'descripcion', 2, 'varchar(255)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_obs_tipo', 'clase_proyecto', 3, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_obs_tipo', 'clase', 4, 'varchar(60)		NULL,');

------  'apex_objeto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto', 'pgsql_a01_nucleo.sql', 44, 'multiproyecto', NULL, NULL, 'objeto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'objeto', 2, 'int4			DEFAULT nextval(\'\"apex_objeto_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'anterior', 3, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'reflexivo', 4, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'clase_proyecto', 5, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'clase', 6, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'subclase', 7, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'subclase_archivo', 8, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'objeto_categoria_proyecto', 9, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'objeto_categoria', 10, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'nombre', 11, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'titulo', 12, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'colapsable', 13, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'descripcion', 14, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'fuente_datos_proyecto', 15, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'fuente_datos', 16, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'solicitud_registrar', 17, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'solicitud_obj_obs_tipo', 18, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'solicitud_obj_observacion', 19, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'parametro_a', 20, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'parametro_b', 21, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'parametro_c', 22, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'parametro_d', 23, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'parametro_e', 24, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'parametro_f', 25, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'usuario', 26, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto', 'creacion', 27, 'timestamp(0)	without time zone	DEFAULT current_timestamp NULL,');

------  'apex_objeto_info'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_info', 'pgsql_a01_nucleo.sql', 45, 'multiproyecto', NULL, '(	objeto_proyecto =	\'%%\' )', 'objeto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_info', 'objeto_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_info', 'objeto', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_info', 'descripcion_breve', 3, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_info', 'descripcion_larga', 4, 'text				NULL,');

------  'apex_objeto_dependencias'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_dependencias', 'pgsql_a01_nucleo.sql', 46, 'multiproyecto', NULL, '', 'objeto_consumidor, identificador', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'dep_id', 2, 'int4				DEFAULT nextval(\'\"apex_objeto_dep_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'objeto_consumidor', 3, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'objeto_proveedor', 4, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'identificador', 5, 'varchar(20)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'parametros_a', 6, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'parametros_b', 7, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'parametros_c', 8, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_dependencias', 'inicializar', 9, 'smallint			NULL,');

------  'apex_objeto_eventos'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_eventos', 'pgsql_a01_nucleo.sql', 47, 'multiproyecto', NULL, '', 'objeto, orden, identificador', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'objeto', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'identificador', 3, 'varchar(20)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'etiqueta', 4, 'varchar(60)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'maneja_datos', 5, 'smallint			NULL DEFAULT 1,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'sobre_fila', 6, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'confirmacion', 7, 'varchar(160)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'estilo', 8, 'varchar(40)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'imagen_recurso_origen', 9, 'varchar(10)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'imagen', 10, 'varchar(60)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'en_botonera', 11, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'ayuda', 12, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'orden', 13, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'ci_predep', 14, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'implicito', 15, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'display_datos_cargados', 16, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_eventos', 'grupo', 17, 'varchar(80)			NULL,');

------  'apex_item_objeto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_item_objeto', 'pgsql_a01_nucleo.sql', 48, 'multiproyecto', NULL, NULL, 'item, objeto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_objeto', 'item_id', 1, 'int4			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_objeto', 'proyecto', 2, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_objeto', 'item', 3, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_objeto', 'objeto', 4, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_objeto', 'orden', 5, 'smallint		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_objeto', 'inicializar', 6, 'smallint		NULL,');

------  'apex_vinculo_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_vinculo_tipo', 'pgsql_a01_nucleo.sql', 49, 'proyecto', NULL, NULL, 'vinculo_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo_tipo', 'vinculo_tipo', 1, 'varchar(10)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo_tipo', 'descripcion_corta', 2, 'varchar(40)		NULL,	--	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo_tipo', 'descripcion', 3, 'varchar(255)	NOT NULL,');

------  'apex_vinculo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_vinculo', 'pgsql_a01_nucleo.sql', 50, 'multiproyecto', NULL, '(	origen_item_proyecto	= \'%%\' )', 'origen_item, origen_objeto, destino_item, destino_objeto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'origen_item_id', 1, 'int4				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'origen_item_proyecto', 2, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'origen_item', 3, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'origen_objeto_proyecto', 4, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'origen_objeto', 5, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'destino_item_id', 6, 'int4				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'destino_item_proyecto', 7, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'destino_item', 8, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'destino_objeto_proyecto', 9, 'varchar(15)		NOT NULL,	--	Objeto que tiene que	recibir el valor');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'destino_objeto', 10, 'int4				NOT NULL,	--');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'frame', 11, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'canal', 12, 'varchar(40)		NULL,			--	Clave	utilizada para	expandir	el	valor');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'indice', 13, 'varchar(20)		NOT NULL,	--	Indice para	que el consumidor	recupere	el	vinculo');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'vinculo_tipo', 14, 'varchar(10)		NOT NULL,	--	Como se habre el vinculo? popup,	zoom,	etc');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'inicializacion', 15, 'varchar(100)	NULL,			--	En	el	caso de un POPUP,	tamao, etc.');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'operacion', 16, 'smallint			NULL,			--	flag que	indica si el vinculo	implica una	propagacion	de	la	operacion o	no	(util	para determinar permisos en cascada)');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'texto', 17, 'varchar(60)		NULL,			--	Texto	del LINK');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'imagen_recurso_origen', 18, 'varchar(10)		NULL,			--	Lugar	donde	se	guardo la imagen:	toba o proyecto');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_vinculo', 'imagen', 19, 'varchar(60)		NULL,			--	path a la imagen');

------  'apex_usuario_grupo_acc_item'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_usuario_grupo_acc_item', 'pgsql_a01_nucleo.sql', 51, 'multiproyecto', NULL, '', 'usuario_grupo_acc, item', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc_item', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc_item', 'usuario_grupo_acc', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc_item', 'item_id', 3, 'int4				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_usuario_grupo_acc_item', 'item', 4, 'varchar(60)		NOT NULL,');

------  'apex_arbol_items_fotos'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_arbol_items_fotos', 'pgsql_a01_nucleo.sql', 52, 'multiproyecto', NULL, '', 'usuario, foto_nombre', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_arbol_items_fotos', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_arbol_items_fotos', 'usuario', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_arbol_items_fotos', 'foto_nombre', 3, 'varchar(100)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_arbol_items_fotos', 'foto_nodos_visibles', 4, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_arbol_items_fotos', 'foto_opciones', 5, 'varchar			NULL,');

------  'apex_admin_album_fotos'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_admin_album_fotos', 'pgsql_a01_nucleo.sql', 53, 'multiproyecto', NULL, '', 'usuario, foto_tipo, foto_nombre', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_album_fotos', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_album_fotos', 'usuario', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_album_fotos', 'foto_tipo', 3, 'varchar(20)		NOT NULL,	--cat_item u cat_objeto');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_album_fotos', 'foto_nombre', 4, 'varchar(100)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_album_fotos', 'foto_nodos_visibles', 5, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_album_fotos', 'foto_opciones', 6, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_album_fotos', 'predeterminada', 7, 'smallint	NULL,');

------  'apex_nucleo_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nucleo_tipo', 'pgsql_a01_nucleo.sql', 54, 'proyecto', NULL, NULL, 'nucleo_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_tipo', 'nucleo_tipo', 1, 'int4				DEFAULT nextval(\'\"apex_nucleo_tipo_seq\"\'::text)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_tipo', 'descripcion_corta', 2, 'varchar(40)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_tipo', 'descripcion', 3, 'varchar(250)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_tipo', 'orden', 4, 'float				NULL,');

------  'apex_nucleo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nucleo', 'pgsql_a01_nucleo.sql', 55, 'multiproyecto', NULL, NULL, 'nucleo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'nucleo', 2, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'nucleo_tipo', 3, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'archivo', 4, 'varchar(80)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'descripcion', 5, 'varchar(250)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'descripcion_corta', 6, 'varchar(40)		NULL,	--	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'doc_nucleo', 7, 'varchar(255)	NULL,			--> GIF donde hay	un	Diagrama');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'doc_db', 8, 'varchar(60)		NULL,			--> GIF donde hay	un	DER de las tablas	que necesita la nucleo.');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'doc_sql', 9, 'varchar(60)		NULL,			--> path	al	archivo que	crea las	tablas.');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'autodoc', 10, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo', 'orden', 11, 'float				NULL,');

------  'apex_nucleo_info'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nucleo_info', 'pgsql_a01_nucleo.sql', 56, 'multiproyecto', NULL, '(	nucleo_proyecto =	\'%%\' )', 'nucleo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_info', 'nucleo_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_info', 'nucleo', 2, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_info', 'descripcion_breve', 3, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_info', 'descripcion_larga', 4, 'text				NULL,');

------  'apex_conversion'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_conversion', 'pgsql_a01_nucleo.sql', 57, 'multiproyecto', NULL, '(	proyecto =	\'%%\' )', 'proyecto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_conversion', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_conversion', 'conversion_aplicada', 2, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_conversion', 'fecha', 3, 'timestamp		NOT NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_elemento_infra_input_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_columna_estilo_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_columna_formato_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_columna_proceso_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_buffer_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_item_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_clase_tipo_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_objeto_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_objeto_dep_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_nucleo_tipo_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a021_prototipacion.sql
--####
--######################################################################################

------  'apex_item_proto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_item_proto', 'pgsql_a021_prototipacion.sql', 58, 'multiproyecto', NULL, '(	item_proyecto =	\'%%\' )', 'item', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_proto', 'item_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_proto', 'item', 2, 'varchar(60)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_proto', 'descripcion', 3, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_proto', 'logica', 4, 'varchar			NULL,');

------  'apex_clase_proto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_proto', 'pgsql_a021_prototipacion.sql', 59, 'multiproyecto', NULL, '(	clase_proyecto =	\'%%\' )', 'clase', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto', 'clase_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto', 'clase', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto', 'descripcion', 3, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto', 'logica', 4, 'varchar			NULL,');

------  'apex_clase_proto_metodo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_proto_metodo', 'pgsql_a021_prototipacion.sql', 60, 'multiproyecto', NULL, '(	clase_proyecto =	\'%%\' )', 'clase', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'clase_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'clase', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'metodo', 3, 'varchar(50)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'orden', 4, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'acceso', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'descripcion', 6, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'parametros', 7, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'retorno', 8, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'logica', 9, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'php', 10, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_metodo', 'auto_subclase', 11, 'smallint		NULL,');

------  'apex_clase_proto_propiedad'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_proto_propiedad', 'pgsql_a021_prototipacion.sql', 61, 'multiproyecto', NULL, '(	clase_proyecto =	\'%%\' )', 'clase', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_propiedad', 'clase_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_propiedad', 'clase', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_propiedad', 'propiedad', 3, 'varchar(50)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_propiedad', 'orden', 4, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_propiedad', 'tipo', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_proto_propiedad', 'descripcion', 6, 'varchar			NULL,');

------  'apex_objeto_proto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_proto', 'pgsql_a021_prototipacion.sql', 62, 'multiproyecto', NULL, '(	objeto_proyecto =	\'%%\' )', 'objeto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto', 'objeto_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto', 'objeto', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto', 'descripcion', 3, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto', 'logica', 4, 'varchar			NULL,');

------  'apex_objeto_proto_metodo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_proto_metodo', 'pgsql_a021_prototipacion.sql', 63, 'multiproyecto', NULL, '(	objeto_proyecto =	\'%%\' )', 'objeto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'objeto_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'objeto', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'metodo', 3, 'varchar(50)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'orden', 4, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'acceso', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'descripcion', 6, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'parametros', 7, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'retorno', 8, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'logica', 9, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_metodo', 'php', 10, 'varchar			NULL,');

------  'apex_objeto_proto_propiedad'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_proto_propiedad', 'pgsql_a021_prototipacion.sql', 64, 'multiproyecto', NULL, '(	objeto_proyecto =	\'%%\' )', 'objeto', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_propiedad', 'objeto_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_propiedad', 'objeto', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_propiedad', 'propiedad', 3, 'varchar(50)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_propiedad', 'orden', 4, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_propiedad', 'tipo', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_proto_propiedad', 'descripcion', 6, 'varchar			NULL,');

------  'apex_nucleo_proto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nucleo_proto', 'pgsql_a021_prototipacion.sql', 65, 'multiproyecto', NULL, '(	nucleo_proyecto =	\'%%\' )', 'nucleo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto', 'nucleo_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto', 'nucleo', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto', 'descripcion', 3, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto', 'logica', 4, 'varchar			NULL,');

------  'apex_nucleo_proto_metodo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nucleo_proto_metodo', 'pgsql_a021_prototipacion.sql', 66, 'multiproyecto', NULL, '(	nucleo_proyecto =	\'%%\' )', 'nucleo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'nucleo_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'nucleo', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'metodo', 3, 'varchar(50)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'orden', 4, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'acceso', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'descripcion', 6, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'parametros', 7, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'retorno', 8, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'logica', 9, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_metodo', 'php', 10, 'varchar			NULL,');

------  'apex_nucleo_proto_propiedad'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nucleo_proto_propiedad', 'pgsql_a021_prototipacion.sql', 67, 'multiproyecto', NULL, '(	nucleo_proyecto =	\'%%\' )', 'nucleo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_propiedad', 'nucleo_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_propiedad', 'nucleo', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_propiedad', 'propiedad', 3, 'varchar(50)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_propiedad', 'orden', 4, 'float			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_propiedad', 'tipo', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_proto_propiedad', 'descripcion', 6, 'varchar			NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a02_dimensiones.sql
--####
--######################################################################################

------  'apex_dimension_tipo_perfil'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_dimension_tipo_perfil', 'pgsql_a02_dimensiones.sql', 68, 'proyecto', NULL, NULL, 'dimension_tipo_perfil', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo_perfil', 'dimension_tipo_perfil', 1, 'varchar(10)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo_perfil', 'descripcion', 2, 'varchar(255)   NOT NULL,');

------  'apex_dimension_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_dimension_tipo', 'pgsql_a02_dimensiones.sql', 69, 'multiproyecto', NULL, NULL, 'dimension_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'dimension_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'nombre', 3, 'varchar(40)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'descripcion', 4, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'parametros', 5, 'varchar   		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'dimension_tipo_perfil', 6, 'varchar(10)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'editor_restric_id', 7, 'int4        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'item_editor_restric_proyecto', 8, 'varchar(15)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'item_editor_restric', 9, 'varchar(60)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'ventana_editor_x', 10, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'ventana_editor_y', 11, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_tipo', 'exclusivo_toba', 12, 'smallint			NULL,');

------  'apex_dimension_grupo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_dimension_grupo', 'pgsql_a02_dimensiones.sql', 70, 'multiproyecto', NULL, NULL, 'dimension_grupo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_grupo', 'proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_grupo', 'dimension_grupo', 2, 'varchar(10)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_grupo', 'nombre', 3, 'varchar(80)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_grupo', 'descripcion', 4, 'varchar(80)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_grupo', 'orden', 5, 'float          NULL,');

------  'apex_dimension'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_dimension', 'pgsql_a02_dimensiones.sql', 71, 'multiproyecto', NULL, NULL, 'dimension', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'dimension', 2, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'dimension_tipo_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'dimension_tipo', 4, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'dimension_grupo_proyecto', 5, 'varchar(15)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'dimension_grupo', 6, 'varchar(10)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'nombre', 7, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'descripcion', 8, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'inicializacion', 9, 'varchar		   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'fuente_datos_proyecto', 10, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'fuente_datos', 11, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'tabla_ref', 12, 'varchar(80)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'tabla_ref_clave', 13, 'varchar(80)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'tabla_ref_desc', 14, 'varchar(80)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension', 'tabla_restric', 15, 'varchar(80)    NULL,');

------  'apex_comparacion'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_comparacion', 'pgsql_a02_dimensiones.sql', 72, 'proyecto', NULL, NULL, 'comparacion', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'comparacion', 1, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'descripcion', 2, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'plan_sql', 3, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'valor_1_des', 4, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'valor_2_des', 5, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'valor_3_des', 6, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'valor_4_des', 7, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_comparacion', 'valor_5_des', 8, 'varchar(255)   NULL,');

------  'apex_dimension_perfil_datos'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_dimension_perfil_datos', 'pgsql_a02_dimensiones.sql', 73, 'multiproyecto', NULL, '( usuario_perfil_datos_proyecto = \'%%\' )', 'usuario_perfil_datos, dimension', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'usuario_perfil_datos_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'usuario_perfil_datos', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'dimension_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'dimension', 4, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'comparacion', 5, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'valor_1', 6, 'varchar(30)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'valor_2', 7, 'varchar(30)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'valor_3', 8, 'varchar(30)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'valor_4', 9, 'varchar(30)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dimension_perfil_datos', 'valor_5', 10, 'varchar(30)    NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a03_solicitudes.sql
--####
--######################################################################################

------  'apex_solicitud'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud', 'pgsql_a03_solicitudes.sql', 74, 'multiproyecto', NULL, NULL, 'solicitud', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'solicitud', 2, 'int4			DEFAULT nextval(\'\"apex_solicitud_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'solicitud_tipo', 3, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'item_proyecto', 4, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'item', 5, 'varchar(60)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'item_id', 6, 'int4        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'momento', 7, 'timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud', 'tiempo_respuesta', 8, 'float			NULL,');

------  'apex_sesion_browser'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_sesion_browser', 'pgsql_a03_solicitudes.sql', 75, 'multiproyecto', NULL, NULL, 'sesion_browser', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'sesion_browser', 1, 'int4			DEFAULT nextval(\'\"apex_sesion_browser_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'proyecto', 2, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'usuario', 3, 'varchar(20) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'ingreso', 4, 'timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'egreso', 5, 'timestamp(0) 	without time zone		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'observaciones', 6, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'php_id', 7, 'varchar(100)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'ip', 8, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_sesion_browser', 'punto_acceso', 9, 'varchar(80) 	NULL,');

------  'apex_solicitud_browser'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_browser', 'pgsql_a03_solicitudes.sql', 76, 'multiproyecto', 'apex_solicitud', '(apex_solicitud.solicitud = dd.solicitud_browser) AND (apex_solicitud.proyecto =\'%%\')', 'solicitud_browser', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_browser', 'solicitud_browser', 1, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_browser', 'sesion_browser', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_browser', 'ip', 3, 'varchar(20)		NULL,');

------  'apex_solicitud_wddx'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_wddx', 'pgsql_a03_solicitudes.sql', 77, 'multiproyecto', 'apex_solicitud', '((apex_solicitud.solicitud = dd.solicitud_wddx) AND (apex_solicitud.proyecto =\'%%\'))', 'solicitud_wddx', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_wddx', 'solicitud_wddx', 1, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_wddx', 'usuario', 2, 'varchar(20) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_wddx', 'ip', 3, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_wddx', 'instancia', 4, 'varchar(80) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_wddx', 'instancia_usuario', 5, 'varchar(20) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_wddx', 'paquete', 6, 'text			NULL,');

------  'apex_solicitud_consola'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_consola', 'pgsql_a03_solicitudes.sql', 78, 'multiproyecto', 'apex_solicitud', '((apex_solicitud.solicitud = dd.solicitud_consola) AND (apex_solicitud.proyecto =\'%%\'))', 'solicitud_consola', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_consola', 'solicitud_consola', 1, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_consola', 'usuario', 2, 'varchar(20)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_consola', 'ip', 3, 'varchar(20)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_consola', 'llamada', 4, 'varchar				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_consola', 'entorno', 5, 'text				NULL,');

------  'apex_solicitud_cronometro'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_cronometro', 'pgsql_a03_solicitudes.sql', 79, 'multiproyecto', 'apex_solicitud', '((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.proyecto =\'%%\'))', 'solicitud', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_cronometro', 'solicitud', 1, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_cronometro', 'marca', 2, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_cronometro', 'nivel_ejecucion', 3, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_cronometro', 'texto', 4, 'varchar(120)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_cronometro', 'tiempo', 5, 'float				NULL,');

------  'apex_solicitud_observacion'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_observacion', 'pgsql_a03_solicitudes.sql', 80, 'multiproyecto', 'apex_solicitud', '((apex_solicitud.solicitud = dd.solicitud_observacion) AND (apex_solicitud.proyecto =\'%%\'))', 'solicitud_observacion', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_observacion', 'solicitud_observacion', 1, 'int4				DEFAULT nextval(\'\"apex_solicitud_observacion_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_observacion', 'solicitud_obs_tipo_proyecto', 2, 'varchar(15)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_observacion', 'solicitud_obs_tipo', 3, 'varchar(20)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_observacion', 'solicitud', 4, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_observacion', 'observacion', 5, 'varchar				NULL,');

------  'apex_solicitud_obj_observacion'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_solicitud_obj_observacion', 'pgsql_a03_solicitudes.sql', 81, 'multiproyecto', 'apex_solicitud', '((apex_solicitud.solicitud = dd.solicitud) AND (apex_solicitud.proyecto =\'%%\'))', 'solicitud_obj_observacion', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_observacion', 'solicitud_obj_observacion', 1, 'int4			DEFAULT nextval(\'\"apex_solicitud_obj_obs_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_observacion', 'solicitud_obj_obs_tipo', 2, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_observacion', 'solicitud', 3, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_observacion', 'objeto_proyecto', 4, 'varchar(15)  	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_observacion', 'objeto', 5, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_solicitud_obj_observacion', 'observacion', 6, 'varchar			NULL,');

------  'apex_log_objeto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_log_objeto', 'pgsql_a03_solicitudes.sql', 82, 'multiproyecto', NULL, 'objeto_proyecto =\'%%\'', 'log_objeto', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_objeto', 'log_objeto', 1, 'int4			DEFAULT nextval(\'\"apex_log_objeto_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_objeto', 'momento', 2, 'timestamp(0) 	without time zone	DEFAULT current_timestamp NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_objeto', 'usuario', 3, 'varchar(20) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_objeto', 'objeto_proyecto', 4, 'varchar(15)  	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_objeto', 'objeto', 5, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_objeto', 'observacion', 6, 'varchar			NULL,');

------  'apex_log_sistema'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_log_sistema', 'pgsql_a03_solicitudes.sql', 83, 'proyecto', NULL, NULL, 'log_sistema', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_sistema', 'log_sistema', 1, 'int4				DEFAULT nextval(\'\"apex_log_sistema_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_sistema', 'momento', 2, 'timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_sistema', 'usuario', 3, 'varchar(20) 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_sistema', 'log_sistema_tipo', 4, 'varchar(20) 		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_sistema', 'observaciones', 5, 'text				NULL,');

------  'apex_log_error_login'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_log_error_login', 'pgsql_a03_solicitudes.sql', 84, 'proyecto', NULL, NULL, 'log_error_login', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'log_error_login', 1, 'int4				DEFAULT nextval(\'\"apex_log_error_login_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'momento', 2, 'timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'usuario', 3, 'varchar(20) 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'clave', 4, 'varchar(20) 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'ip', 5, 'varchar(20)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'gravedad', 6, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'mensaje', 7, 'text				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_error_login', 'punto_acceso', 8, 'varchar(80) 		NULL,');

------  'apex_log_ip_rechazada'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_log_ip_rechazada', 'pgsql_a03_solicitudes.sql', 85, 'proyecto', NULL, NULL, 'ip', NULL, NULL, '', '1.0', '1', '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_ip_rechazada', 'ip', 1, 'varchar(20)								NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_log_ip_rechazada', 'momento', 2, 'timestamp(0) without time zone	DEFAULT current_timestamp NOT NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_solicitud_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_sesion_browser_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_solicitud_observacion_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_solicitud_obj_obs_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_log_objeto_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_log_sistema_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_log_error_login_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a04_notas.sql
--####
--######################################################################################

------  'apex_nota_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nota_tipo', 'pgsql_a04_notas.sql', 86, 'proyecto', NULL, NULL, 'nota_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota_tipo', 'nota_tipo', 1, 'varchar(20)    	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota_tipo', 'descripcion', 2, 'varchar(255)   	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota_tipo', 'icono', 3, 'varchar(30)    	NULL,');

------  'apex_nota'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nota', 'pgsql_a04_notas.sql', 87, 'multiproyecto', NULL, NULL, 'nota', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'nota', 1, 'int4           DEFAULT nextval(\'\"apex_nota_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'nota_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'usuario_origen', 4, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'usuario_destino', 5, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'titulo', 6, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'texto', 7, 'text           NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'leido', 8, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'bl', 9, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nota', 'creacion', 10, 'timestamp(0)   without time zone DEFAULT current_timestamp NULL,');

------  'apex_patron_nota'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_patron_nota', 'pgsql_a04_notas.sql', 88, 'multiproyecto', NULL, '( patron_proyecto = \'%%\' )', 'patron_nota', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'patron_nota', 1, 'int4           DEFAULT nextval(\'\"apex_patron_nota_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'nota_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'patron_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'patron', 4, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'usuario_origen', 5, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'usuario_destino', 6, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'titulo', 7, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'texto', 8, 'text           NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'leido', 9, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'bl', 10, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_nota', 'creacion', 11, 'timestamp(0)   without time zone DEFAULT current_timestamp NULL,');

------  'apex_item_nota'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_item_nota', 'pgsql_a04_notas.sql', 89, 'multiproyecto', NULL, '( item_proyecto = \'%%\' )', 'item_nota', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'item_nota', 1, 'int4           DEFAULT nextval(\'\"apex_item_nota_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'nota_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'item_id', 3, 'int4        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'item_proyecto', 4, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'item', 5, 'varchar(60)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'usuario_origen', 6, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'usuario_destino', 7, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'titulo', 8, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'texto', 9, 'text           NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'leido', 10, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'bl', 11, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_nota', 'creacion', 12, 'timestamp(0)   without time zone DEFAULT current_timestamp NULL,');

------  'apex_clase_nota'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_nota', 'pgsql_a04_notas.sql', 90, 'multiproyecto', NULL, '( clase_proyecto = \'%%\' )', 'clase_nota', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'clase_nota', 1, 'int4           DEFAULT nextval(\'\"apex_clase_nota_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'nota_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'clase_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'clase', 4, 'varchar(60)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'usuario_origen', 5, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'usuario_destino', 6, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'titulo', 7, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'texto', 8, 'text           NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'bl', 9, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'leido', 10, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_nota', 'creacion', 11, 'timestamp(0)   without time zone DEFAULT current_timestamp NULL,');

------  'apex_objeto_nota'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_nota', 'pgsql_a04_notas.sql', 91, 'multiproyecto', NULL, '( objeto_proyecto = \'%%\' )', 'objeto_nota', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'objeto_nota', 1, 'int4           DEFAULT nextval(\'\"apex_objeto_nota_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'nota_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'objeto_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'objeto', 4, 'int4           NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'usuario_origen', 5, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'usuario_destino', 6, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'titulo', 7, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'texto', 8, 'text           NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'bl', 9, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'leido', 10, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_nota', 'creacion', 11, 'timestamp(0)   without time zone DEFAULT current_timestamp NULL,');

------  'apex_nucleo_nota'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_nucleo_nota', 'pgsql_a04_notas.sql', 92, 'multiproyecto', NULL, '( nucleo_proyecto = \'%%\' )', 'nucleo_nota', NULL, NULL, '', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'nucleo_nota', 1, 'int4           DEFAULT nextval(\'\"apex_nucleo_nota_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'nota_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'nucleo_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'nucleo', 4, 'varchar(60)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'usuario_origen', 5, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'usuario_destino', 6, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'titulo', 7, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'texto', 8, 'text           NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'bl', 9, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'leido', 10, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_nucleo_nota', 'creacion', 11, 'timestamp(0)   without time zone DEFAULT current_timestamp NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_nota_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_patron_nota_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_item_nota_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_clase_nota_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_objeto_nota_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_nucleo_nota_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a05_mensajes.sql
--####
--######################################################################################

------  'apex_msg_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_msg_tipo', 'pgsql_a05_mensajes.sql', 93, 'proyecto', NULL, NULL, 'msg_tipo', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg_tipo', 'msg_tipo', 1, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg_tipo', 'descripcion', 2, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg_tipo', 'icono', 3, 'varchar(30)    NULL,');

------  'apex_msg'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_msg', 'pgsql_a05_mensajes.sql', 94, 'multiproyecto', NULL, NULL, 'msg', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'msg', 1, 'int4           DEFAULT nextval(\'\"apex_msg_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'indice', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'msg_tipo', 4, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'descripcion_corta', 5, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'mensaje_a', 6, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'mensaje_b', 7, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'mensaje_c', 8, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_msg', 'mensaje_customizable', 9, 'varchar        NULL,');

------  'apex_patron_msg'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_patron_msg', 'pgsql_a05_mensajes.sql', 95, 'multiproyecto', NULL, '( patron_proyecto = \'%%\' )', 'patron_msg', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'patron_msg', 1, 'int4           DEFAULT nextval(\'\"apex_patron_msg_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'msg_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'indice', 3, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'patron_proyecto', 4, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'patron', 5, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'descripcion_corta', 6, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'mensaje_a', 7, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'mensaje_b', 8, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'mensaje_c', 9, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_patron_msg', 'mensaje_customizable', 10, 'varchar        NULL,');

------  'apex_item_msg'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_item_msg', 'pgsql_a05_mensajes.sql', 96, 'multiproyecto', NULL, '( item_proyecto = \'%%\' )', 'item_msg', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'item_msg', 1, 'int4           DEFAULT nextval(\'\"apex_item_msg_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'msg_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'indice', 3, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'item_id', 4, 'int4        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'item_proyecto', 5, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'item', 6, 'varchar(60)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'descripcion_corta', 7, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'mensaje_a', 8, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'mensaje_b', 9, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'mensaje_c', 10, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'mensaje_customizable', 11, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_item_msg', 'parametro_patron', 12, 'varchar(100)	NULL,');

------  'apex_clase_msg'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_clase_msg', 'pgsql_a05_mensajes.sql', 97, 'multiproyecto', NULL, '( clase_proyecto = \'%%\' )', 'clase_msg', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'clase_msg', 1, 'int4           DEFAULT nextval(\'\"apex_clase_msg_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'msg_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'indice', 3, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'clase_proyecto', 4, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'clase', 5, 'varchar(60)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'descripcion_corta', 6, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'mensaje_a', 7, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'mensaje_b', 8, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'mensaje_c', 9, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_clase_msg', 'mensaje_customizable', 10, 'varchar        NULL,');

------  'apex_objeto_msg'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_msg', 'pgsql_a05_mensajes.sql', 98, 'multiproyecto', NULL, '( objeto_proyecto = \'%%\' )', 'objeto_msg', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'objeto_msg', 1, 'int4           DEFAULT nextval(\'\"apex_objeto_msg_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'msg_tipo', 2, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'indice', 3, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'objeto_proyecto', 4, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'objeto', 5, 'varchar(60)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'descripcion_corta', 6, 'varchar(50)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'mensaje_a', 7, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'mensaje_b', 8, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'mensaje_c', 9, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'mensaje_customizable', 10, 'varchar        NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_msg', 'parametro_clase', 11, 'varchar(100)	NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_msg_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_patron_msg_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_item_msg_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_clase_msg_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_objeto_msg_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a06_mod_datos.sql
--####
--######################################################################################

------  'apex_mod_datos_zona'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_mod_datos_zona', 'pgsql_a06_mod_datos.sql', 99, 'multiproyecto', NULL, NULL, 'zona', NULL, NULL, 'Organizadores conceptuales de tablas', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_zona', 'proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_zona', 'zona', 2, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_zona', 'descripcion', 3, 'varchar(255)   NULL,');

------  'apex_mod_datos_dump'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_mod_datos_dump', 'pgsql_a06_mod_datos.sql', 100, 'proyecto', NULL, NULL, 'dump', NULL, NULL, 'Modalidades de dumpeo', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_dump', 'dump', 1, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_dump', 'descripcion', 2, 'varchar(255)   NULL,');

------  'apex_mod_datos_tabla'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_mod_datos_tabla', 'pgsql_a06_mod_datos.sql', 101, 'multiproyecto', NULL, NULL, 'tabla', NULL, NULL, 'Tablas que componen el modelo de datos', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'tabla', 2, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'script', 3, 'varchar(80)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'orden', 4, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'descripcion', 5, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'version', 6, 'varchar(15)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'historica', 7, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'instancia', 8, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'dump', 9, 'varchar(20)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'dump_where', 10, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'dump_from', 11, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'dump_order_by', 12, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'dump_order_by_from', 13, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'dump_order_by_where', 14, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'extra_1', 15, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla', 'extra_2', 16, 'varchar(255)   NULL,');

------  'apex_mod_datos_tabla_columna'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_mod_datos_tabla_columna', 'pgsql_a06_mod_datos.sql', 102, 'multiproyecto', NULL, '( tabla_proyecto = \'%%\' )', 'tabla, columna', NULL, NULL, 'Columnas de la tabla', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_columna', 'tabla_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_columna', 'tabla', 2, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_columna', 'columna', 3, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_columna', 'orden', 4, 'float				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_columna', 'dump', 5, 'smallint			DEFAULT 1   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_columna', 'definicion', 6, 'varchar		   NULL,');

------  'apex_mod_datos_tabla_restric'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_mod_datos_tabla_restric', 'pgsql_a06_mod_datos.sql', 103, 'multiproyecto', NULL, '( tabla_proyecto = \'%%\' )', 'tabla, restriccion', NULL, NULL, 'Constraints de la tabla', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_restric', 'tabla_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_restric', 'tabla', 2, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_restric', 'restriccion', 3, 'varchar(30)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_tabla_restric', 'definicion', 4, 'varchar		   NULL,');

------  'apex_mod_datos_secuencia'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_mod_datos_secuencia', 'pgsql_a06_mod_datos.sql', 104, 'multiproyecto', NULL, NULL, 'secuencia', NULL, NULL, 'Secuencias', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_secuencia', 'proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_secuencia', 'secuencia', 2, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_secuencia', 'definicion', 3, 'varchar(255)    NULL,');

------  'apex_mod_datos_zona_tabla'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_mod_datos_zona_tabla', 'pgsql_a06_mod_datos.sql', 105, 'multiproyecto', NULL, '( tabla_proyecto = \'%%\' )', 'zona, tabla', NULL, NULL, 'Asociacion de tablas con zonas', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_zona_tabla', 'zona_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_zona_tabla', 'zona', 2, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_zona_tabla', 'tabla_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_mod_datos_zona_tabla', 'tabla', 4, 'varchar(30)    NOT NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a07_admin_proy.sql
--####
--######################################################################################

------  'apex_ap_version'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_ap_version', 'pgsql_a07_admin_proy.sql', 106, 'multiproyecto', NULL, NULL, 'version', NULL, NULL, 'Tabla de manejo de versiones', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_version', 'proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_version', 'version', 2, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_version', 'descripcion', 3, 'varchar(255)   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_version', 'fecha', 4, 'date				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_version', 'observaciones', 5, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_version', 'actual', 6, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_version', 'cerrada', 7, 'smallint			NULL,');

------  'apex_ap_tarea_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_ap_tarea_tipo', 'pgsql_a07_admin_proy.sql', 107, 'proyecto', NULL, NULL, 'tarea_tipo', NULL, NULL, 'Tipos de tarea', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_tipo', 'tarea_tipo', 1, 'int4				DEFAULT nextval(\'\"apex_ap_tarea_tipo_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_tipo', 'descripcion', 2, 'varchar(70)   	NOT NULL,');

------  'apex_ap_tarea_estado'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_ap_tarea_estado', 'pgsql_a07_admin_proy.sql', 108, 'proyecto', NULL, NULL, 'tarea_estado', NULL, NULL, 'Estados de Tarea', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_estado', 'tarea_estado', 1, 'int4           DEFAULT nextval(\'\"apex_ap_tarea_estado_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_estado', 'descripcion', 2, 'varchar(70)   NOT NULL,');

------  'apex_ap_tarea_prioridad'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_ap_tarea_prioridad', 'pgsql_a07_admin_proy.sql', 109, 'proyecto', NULL, NULL, 'tarea_prioridad', NULL, NULL, 'Prioridad de Tarea', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_prioridad', 'tarea_prioridad', 1, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_prioridad', 'descripcion', 2, 'varchar(70)		NOT NULL,');

------  'apex_ap_tarea_tema'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_ap_tarea_tema', 'pgsql_a07_admin_proy.sql', 110, 'proyecto', NULL, NULL, 'tarea_tema', NULL, NULL, 'Tipos de tarea', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_tema', 'tarea_tema', 1, 'int4				DEFAULT nextval(\'\"apex_ap_tarea_tema_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_tema', 'descripcion', 2, 'varchar(70)   	NOT NULL,');

------  'apex_ap_tarea'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_ap_tarea', 'pgsql_a07_admin_proy.sql', 111, 'multiproyecto', NULL, NULL, 'tarea', NULL, NULL, 'Estados de Tarea', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'proyecto', 1, 'varchar(15)   			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'tarea', 2, 'int4           		DEFAULT nextval(\'\"apex_ap_tarea_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'tarea_tipo', 3, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'tarea_estado', 4, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'tarea_prioridad', 5, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'tarea_tema', 6, 'int4				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'descripcion', 7, 'varchar(400)  		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'version_proyecto', 8, 'varchar(15)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'version', 9, 'varchar(15)    		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea', 'grado_avance', 10, 'smallint			NULL,');

------  'apex_ap_tarea_usuario'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_ap_tarea_usuario', 'pgsql_a07_admin_proy.sql', 112, 'multiproyecto', 'apex_ap_tarea', '(apex_ap_tarea.tarea = dd.tarea) AND (apex_ap_tarea.proyecto =\'%%\')', 'tarea, usuario', NULL, NULL, 'Prioridad de Tarea', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_usuario', 'tarea', 1, 'int4           NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_usuario', 'usuario', 2, 'varchar(20) 	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_usuario', 'fecha_inicio', 3, 'date				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_usuario', 'fecha_fin', 4, 'date				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_ap_tarea_usuario', 'observacion', 5, 'varchar(255)	NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_ap_tarea_tipo_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_ap_tarea_estado_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_ap_tarea_tema_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_ap_tarea_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a08_tareas_programadas.sql
--####
--######################################################################################

------  'apex_tp_tarea_tipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_tp_tarea_tipo', 'pgsql_a08_tareas_programadas.sql', 113, 'proyecto', NULL, NULL, 'tarea_tipo', NULL, NULL, 'Tipos de tarea', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea_tipo', 'tarea_tipo', 1, 'int4			DEFAULT nextval(\'\"tpex_tp_tarea_tipo_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea_tipo', 'descripcion', 2, 'varchar(70)   	NOT NULL,');

------  'apex_tp_tarea'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_tp_tarea', 'pgsql_a08_tareas_programadas.sql', 114, 'multiproyecto', NULL, NULL, 'tarea', NULL, NULL, 'Tabla de manejo de versiones', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'proyecto', 1, 'varchar(15)    		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'tarea', 2, 'int4          		DEFAULT nextval(\'\"apex_tp_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'item_id', 3, 'int4				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'item_proyecto', 4, 'varchar(15)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'item', 5, 'varchar(60)			NULL,	--> Item	del catalogo a	invocar como instanciador de objetos de esta	clase');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'activada', 6, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'descripcion', 7, 'varchar(255)   		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'tarea_tipo', 8, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'fecha', 9, 'date				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tp_tarea', 'hora', 10, 'time				NOT NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_tp_tarea_tipo_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_tp_tarea_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a09_entorno_trabajo.sql
--####
--######################################################################################

------  'apex_et_item'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_et_item', 'pgsql_a09_entorno_trabajo.sql', 115, 'multiproyecto', NULL, '(item_proyecto =\'%%\')', 'usuario, item', NULL, NULL, 'Portafolios de items', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_item', 'item_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_item', 'item', 2, 'varchar(60)   	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_item', 'usuario', 3, 'varchar(20)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_item', 'creacion', 4, 'timestamp(0) without time zone DEFAULT current_timestamp NOT NULL,');

------  'apex_et_objeto'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_et_objeto', 'pgsql_a09_entorno_trabajo.sql', 116, 'multiproyecto', NULL, '(objeto_proyecto =\'%%\')', 'usuario, objeto', NULL, NULL, 'Portafolios de objetos', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_objeto', 'objeto_proyecto', 1, 'varchar(15)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_objeto', 'objeto', 2, 'int4	   	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_objeto', 'usuario', 3, 'varchar(20)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_objeto', 'creacion', 4, 'timestamp(0) without time zone DEFAULT current_timestamp NOT NULL,');

------  'apex_et_preferencias'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_et_preferencias', 'pgsql_a09_entorno_trabajo.sql', 117, 'multiproyecto', NULL, '(usuario_proyecto =\'%%\')', 'usuario', NULL, NULL, 'Portafolios de Item', '1.0', NULL, '1');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_preferencias', 'usuario_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_preferencias', 'usuario', 2, 'varchar(20)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_preferencias', 'listado_obj_pref', 3, 'varchar(20) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_preferencias', 'listado_item_pref', 4, 'varchar(20)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_preferencias', 'item_proyecto', 5, 'varchar(15)		NOT NULL, -- Item inicial');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_et_preferencias', 'item', 6, 'varchar(60)   	NOT NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a10_clase_hoja.sql
--####
--######################################################################################

------  'apex_objeto_hoja'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_hoja', 'pgsql_a10_clase_hoja.sql', 118, 'multiproyecto', NULL, '( objeto_hoja_proyecto = \'%%\' )', 'objeto_hoja', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'objeto_hoja_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'objeto_hoja', 2, 'int4           NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'sql', 3, 'text           NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'ancho', 4, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'total_y', 5, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'total_x', 6, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'total_x_formato', 7, 'int4				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'columna_entrada', 8, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'ordenable', 9, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'grafico', 10, 'varchar(30)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'graf_columnas', 11, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'graf_filas', 12, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'graf_gen_invertir', 13, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'graf_gen_invertible', 14, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'graf_gen_ancho', 15, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja', 'graf_gen_alto', 16, 'smallint       NULL,');

------  'apex_objeto_hoja_directiva_ti'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_hoja_directiva_ti', 'pgsql_a10_clase_hoja.sql', 119, 'proyecto', NULL, NULL, 'objeto_hoja_directiva_tipo', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva_ti', 'objeto_hoja_directiva_tipo', 1, 'smallint       NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva_ti', 'nombre', 2, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva_ti', 'descripcion', 3, 'varchar(255)   NOT NULL,');

------  'apex_objeto_hoja_directiva'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_hoja_directiva', 'pgsql_a10_clase_hoja.sql', 120, 'multiproyecto', NULL, '( objeto_hoja_proyecto = \'%%\' )', 'objeto_hoja, columna', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'objeto_hoja_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'objeto_hoja', 2, 'int4           NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'columna', 3, 'smallint       NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'objeto_hoja_directiva_tipo', 4, 'smallint       NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'nombre', 5, 'varchar(40)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'columna_formato', 6, 'int4		      NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'columna_estilo', 7, 'int4		      NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'par_dimension_proyecto', 8, 'varchar(15)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'par_dimension', 9, 'varchar(30)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'par_tabla', 10, 'varchar(40)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_hoja_directiva', 'par_columna', 11, 'varchar(80)    NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a11_clase_filtro.sql
--####
--######################################################################################

------  'apex_objeto_filtro'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_filtro', 'pgsql_a11_clase_filtro.sql', 121, 'multiproyecto', NULL, '( objeto_filtro_proyecto = \'%%\' )', 'objeto_filtro', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'objeto_filtro_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'objeto_filtro', 2, 'int4           NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'dimension_proyecto', 3, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'dimension', 4, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'etiqueta', 5, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'tabla', 6, 'varchar(300)   NULL,  -- Puede ser una subconsulta.');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'columna', 7, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'orden', 8, 'float          NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'requerido', 9, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'no_interactivo', 10, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_filtro', 'predeterminado', 11, 'varchar(100)	NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a14_clase_lista.sql
--####
--######################################################################################

------  'apex_objeto_lista'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_lista', 'pgsql_a14_clase_lista.sql', 122, 'multiproyecto', NULL, '( objeto_lista_proyecto = \'%%\' )', 'objeto_lista', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'objeto_lista_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'objeto_lista', 2, 'int4			   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'titulo', 3, 'varchar(80)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'subtitulo', 4, 'varchar(80)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'sql', 5, 'varchar        NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'col_ver', 6, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'col_titulos', 7, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'col_formato', 8, 'varchar(255)   NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'ancho', 9, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'ordenar', 10, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'exportar', 11, 'smallint       NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'vinculo_clave', 12, 'varchar(80)   NULL,       -- Columnas que poseen la clave, separadas por comas');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_lista', 'vinculo_indice', 13, 'varchar(20)    NULL,       -- Titulo de la columna que tiene');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a15_clase_grafico.sql
--####
--######################################################################################

------  'apex_objeto_grafico'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_grafico', 'pgsql_a15_clase_grafico.sql', 123, 'multiproyecto', NULL, '( objeto_grafico_proyecto = \'%%\' )', 'objeto_grafico', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_grafico', 'objeto_grafico_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_grafico', 'objeto_grafico', 2, 'int4			   NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_grafico', 'grafico', 3, 'varchar(30)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_grafico', 'sql', 4, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_grafico', 'inicializacion', 5, 'varchar			NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a16_clase_cuadro.sql
--####
--######################################################################################

------  'apex_objeto_cuadro'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_cuadro', 'pgsql_a16_clase_cuadro.sql', 124, 'multiproyecto', NULL, '( objeto_cuadro_proyecto = \'%%\' )', 'objeto_cuadro', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'objeto_cuadro_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'objeto_cuadro', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'titulo', 3, 'varchar(80) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'subtitulo', 4, 'varchar(80) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'sql', 5, 'varchar     	NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'columnas_clave', 6, 'varchar(255)	NULL,   -- Columnas que poseen la clave, separadas por comas');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'clave_dbr', 7, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'archivos_callbacks', 8, 'varchar(100)	NULL,			-- Archivos donde estan las callbacks llamadas en las columnas');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'ancho', 9, 'varchar(10) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'ordenar', 10, 'smallint    	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'paginar', 11, 'smallint    	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'tamano_pagina', 12, 'smallint    	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'tipo_paginado', 13, 'varchar(1)  	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'eof_invisible', 14, 'smallint    	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'eof_customizado', 15, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'exportar', 16, 'smallint       	NULL,		-- Exportar XLS');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'exportar_rtf', 17, 'smallint       	NULL,		-- Exportar PDF');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'pdf_propiedades', 18, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'pdf_respetar_paginacion', 19, 'smallint       	NULL,  		-- ATENCION - Eliminar a futuro');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'asociacion_columnas', 20, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'ev_seleccion', 21, 'smallint		NULL,		-- EI cuadro, lupa -> seleccion');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'ev_eliminar', 22, 'smallint		NULL,		-- EI cuadro, tacho -> eliminacion');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'dao_nucleo_proyecto', 23, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'dao_nucleo', 24, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'dao_metodo', 25, 'varchar(80)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'dao_parametros', 26, 'varchar(150)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'desplegable', 27, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'desplegable_activo', 28, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'scroll', 29, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'scroll_alto', 30, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'cc_modo', 31, 'varchar(1)		NULL,		-- Tipo de cortes de control');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'cc_modo_anidado_colap', 32, 'smallint		NULL,		-- Tipo anidado: colapsar niveles');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'cc_modo_anidado_totcol', 33, 'smallint		NULL,		-- Tipo anidado: Desplegar columnas horizontalmente');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro', 'cc_modo_anidado_totcua', 34, 'smallint		NULL,		-- Tipo anidado: El total del ultimo nivel adosarlo al cuadro');

------  'apex_objeto_cuadro_columna'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_cuadro_columna', 'pgsql_a16_clase_cuadro.sql', 125, 'multiproyecto', NULL, '( objeto_cuadro_proyecto = \'%%\' )', 'objeto_cuadro, orden', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'objeto_cuadro_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'objeto_cuadro', 2, 'int4       		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'orden', 3, 'float      		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'titulo', 4, 'varchar(100)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'columna_estilo', 5, 'int4		    NOT NULL,	-- Estilo de la columna');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'columna_ancho', 6, 'varchar(10)		NULL,			-- Ancho de columna para RTF');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'ancho_html', 7, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'total', 8, 'smallint		NULL,			-- La columna lleva un total al final?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'total_cc', 9, 'varchar(100)	NULL,			-- La columna lleva un total al final?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'valor_sql', 10, 'varchar(30)    	NULL,			-- El valor de la columna HAY que tomarlo de RECORDSET');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'valor_sql_formato', 11, 'int4		    NULL,			-- El valor del RECORDSET debe ser formateado');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'valor_fijo', 12, 'varchar(30)    	NULL,			-- La columna tomo un valor FIJO');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'valor_proceso', 13, 'int4			NULL,			-- El valor de la columna es el resultado de procesar el registro');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'valor_proceso_esp', 14, 'varchar(40)		NULL,			-- La callback de procesamiento es custom');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'valor_proceso_parametros', 15, 'varchar(155)	NULL,			-- Parametros al procesamiento del registro');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'vinculo_indice', 16, 'varchar(20) 	NULL,       -- Que vinculo asociado tengo que utilizar??');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'par_dimension_proyecto', 17, 'varchar(15) 	NULL,			-- Hay una dimension asociada??');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'par_dimension', 18, 'varchar(30) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'par_tabla', 19, 'varchar(40) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'par_columna', 20, 'varchar(80) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'no_ordenar', 21, 'smallint		NULL,			-- No aplicarle interface de orden a la columna');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'mostrar_xls', 22, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'mostrar_pdf', 23, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'pdf_propiedades', 24, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_columna', 'desabilitado', 25, 'smallint		NULL,');

------  'apex_objeto_cuadro_cc'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_cuadro_cc', 'pgsql_a16_clase_cuadro.sql', 126, 'multiproyecto', NULL, '( objeto_cuadro_proyecto = \'%%\' )', 'objeto_cuadro, objeto_cuadro_cc', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'objeto_cuadro_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'objeto_cuadro', 2, 'int4       		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'objeto_cuadro_cc', 3, 'int4			DEFAULT nextval(\'\"apex_obj_ei_cuadro_cc_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'identificador', 4, 'varchar(15)		NULL,			-- Para declarar funciones que redefinan la cabecera o el pie del corte');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'descripcion', 5, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'orden', 6, 'float      		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'columnas_id', 7, 'varchar(200)	NOT NULL,		-- Columnas utilizada para cortar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'columnas_descripcion', 8, 'varchar(200)	NOT NULL,		-- Columnas utilizada como titulo del corte');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'pie_contar_filas', 9, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'pie_mostrar_titulos', 10, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_cuadro_cc', 'imp_paginar', 11, 'smallint		NULL,');

------  'apex_objeto_ei_cuadro_columna'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'pgsql_a16_clase_cuadro.sql', 127, 'multiproyecto', NULL, '( objeto_cuadro_proyecto = \'%%\' )', 'objeto_cuadro, objeto_cuadro_col', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'objeto_cuadro_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'objeto_cuadro', 2, 'int4       		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'objeto_cuadro_col', 3, 'int4			DEFAULT nextval(\'\"apex_obj_ei_cuadro_col_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'clave', 4, 'varchar(40)    	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'orden', 5, 'float      		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'titulo', 6, 'varchar(100)	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'estilo_titulo', 7, 'varchar(100)	DEFAULT \'lista-col-titulo\' NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'estilo', 8, 'int4		    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'ancho', 9, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'formateo', 10, 'int4		    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'vinculo_indice', 11, 'varchar(20) 	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'no_ordenar', 12, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'mostrar_xls', 13, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'mostrar_pdf', 14, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'pdf_propiedades', 15, 'varchar			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'desabilitado', 16, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'total', 17, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_cuadro_columna', 'total_cc', 18, 'varchar(100)	NULL,			-- La columna lleva un total al final?');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_obj_ei_cuadro_cc_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_obj_ei_cuadro_col_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a17_clase_mapa.sql
--####
--######################################################################################

------  'apex_objeto_mapa'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_mapa', 'pgsql_a17_clase_mapa.sql', 128, 'multiproyecto', NULL, '( objeto_mapa_proyecto = \'%%\' )', 'objeto_mapa', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mapa', 'objeto_mapa_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mapa', 'objeto_mapa', 2, 'int4		   	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mapa', 'sql', 3, 'varchar        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mapa', 'descripcion', 4, 'varchar(255)	NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a20_clase_plan.sql
--####
--######################################################################################

------  'apex_objeto_plan'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_plan', 'pgsql_a20_clase_plan.sql', 129, 'multiproyecto', NULL, '( objeto_plan_proyecto = \'%%\' )', 'objeto_plan', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan', 'objeto_plan_proyecto', 1, 'varchar(15)					NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan', 'objeto_plan', 2, 'int4						NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan', 'descripcion', 3, 'varchar(255)			NOT NULL,');

------  'apex_objeto_plan_activ'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_plan_activ', 'pgsql_a20_clase_plan.sql', 130, 'multiproyecto', NULL, '( objeto_plan_proyecto = \'%%\' )', 'objeto_plan, posicion', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'objeto_plan_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'objeto_plan', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'posicion', 3, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'descripcion_corta', 4, 'varchar(50)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'descripcion', 5, 'varchar				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'fecha_inicio', 6, 'date				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'fecha_fin', 7, 'date				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'duracion', 8, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'anotacion', 9, 'varchar(50)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ', 'altura', 10, 'float				NULL,');

------  'apex_objeto_plan_activ_usu'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_plan_activ_usu', 'pgsql_a20_clase_plan.sql', 131, 'multiproyecto', NULL, '( objeto_plan_proyecto = \'%%\' )', 'objeto_plan, posicion', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ_usu', 'objeto_plan_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ_usu', 'objeto_plan', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ_usu', 'posicion', 3, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ_usu', 'usuario', 4, 'varchar(20)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_activ_usu', 'observaciones', 5, 'varchar				NULL,');

------  'apex_objeto_plan_hito'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_plan_hito', 'pgsql_a20_clase_plan.sql', 132, 'multiproyecto', NULL, '( objeto_plan_proyecto = \'%%\' )', 'objeto_plan, posicion', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_hito', 'objeto_plan_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_hito', 'objeto_plan', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_hito', 'posicion', 3, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_hito', 'descripcion_corta', 4, 'varchar(50)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_hito', 'descripcion', 5, 'varchar				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_hito', 'fecha', 6, 'date				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_hito', 'anotacion', 7, 'varchar(50)			NULL,');

------  'apex_objeto_plan_linea'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_plan_linea', 'pgsql_a20_clase_plan.sql', 133, 'multiproyecto', NULL, '( objeto_plan_proyecto = \'%%\' )', 'objeto_plan, linea', NULL, NULL, '', '1.0', NULL, NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'objeto_plan_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'objeto_plan', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'linea', 3, 'int4				DEFAULT nextval(\'\"apex_objeto_plan_linea_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'descripcion_corta', 4, 'varchar(50)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'descripcion', 5, 'varchar				NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'fecha', 6, 'date				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'color', 7, 'varchar(20)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'ancho', 8, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_plan_linea', 'estilo', 9, 'varchar(20)			NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_objeto_plan_linea_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a21_clase_db_registros.sql
--####
--######################################################################################

------  'apex_admin_persistencia'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_admin_persistencia', 'pgsql_a21_clase_db_registros.sql', 134, 'proyecto', NULL, NULL, 'ap', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_persistencia', 'ap', 1, 'int4				DEFAULT nextval(\'\"apex_admin_persistencia_seq\"\'::text) 		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_persistencia', 'clase', 2, 'varchar(60)			NOT	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_persistencia', 'archivo', 3, 'varchar(60)			NOT	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_persistencia', 'descripcion', 4, 'varchar(60)			NOT	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_admin_persistencia', 'categoria', 5, 'varchar(20)			NULL,		-- Indica si es un AP de tablas o relaciones');

------  'apex_tipo_datos'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_tipo_datos', 'pgsql_a21_clase_db_registros.sql', 135, 'proyecto', NULL, NULL, 'tipo', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tipo_datos', 'tipo', 1, 'varchar(1)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_tipo_datos', 'descripcion', 2, 'varchar(30)			NOT	NULL,');

------  'apex_objeto_db_registros'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_db_registros', 'pgsql_a21_clase_db_registros.sql', 136, 'multiproyecto', NULL, '( objeto_proyecto = \'%%\' )', 'objeto', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'objeto_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'objeto', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'max_registros', 3, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'min_registros', 4, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'ap', 5, 'int4			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'ap_clase', 6, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'ap_archivo', 7, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'tabla', 8, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'alias', 9, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros', 'modificar_claves', 10, 'smallint		NULL,');

------  'apex_objeto_db_registros_col'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_db_registros_col', 'pgsql_a21_clase_db_registros.sql', 137, 'multiproyecto', NULL, '( objeto_proyecto = \'%%\' )', 'objeto, col_id', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'objeto_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'objeto', 2, 'int4       		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'col_id', 3, 'int4			DEFAULT nextval(\'\"apex_objeto_dbr_columna_seq\"\'::text) 		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'columna', 4, 'varchar(40)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'tipo', 5, 'varchar(1)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'pk', 6, 'smallint 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'secuencia', 7, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'largo', 8, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'no_nulo', 9, 'smallint 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'no_nulo_db', 10, 'smallint 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_db_registros_col', 'externa', 11, 'smallint		NULL,');

------  'apex_objeto_datos_rel'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_datos_rel', 'pgsql_a21_clase_db_registros.sql', 138, 'multiproyecto', NULL, '( proyecto = \'%%\' )', 'objeto', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel', 'proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel', 'objeto', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel', 'clave', 3, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel', 'ap', 4, 'int4			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel', 'ap_clase', 5, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel', 'ap_archivo', 6, 'varchar(60)		NULL,');

------  'apex_objeto_datos_rel_asoc'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'pgsql_a21_clase_db_registros.sql', 139, 'multiproyecto', NULL, '( proyecto = \'%%\' )', 'objeto, asoc_id', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'objeto', 2, 'int4       			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'asoc_id', 3, 'int4				DEFAULT nextval(\'\"apex_objeto_datos_rel_asoc_seq\"\'::text) 		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'identificador', 4, 'varchar(40)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'padre_proyecto', 5, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'padre_objeto', 6, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'padre_id', 7, 'varchar(20)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'padre_clave', 8, 'varchar(60)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'hijo_proyecto', 9, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'hijo_objeto', 10, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'hijo_id', 11, 'varchar(20)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'hijo_clave', 12, 'varchar(60)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'cascada', 13, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_datos_rel_asoc', 'orden', 14, 'float				NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_admin_persistencia_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_objeto_dbr_columna_seq');
INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_objeto_datos_rel_asoc_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a50_clase_ut_formulario.sql
--####
--######################################################################################

------  'apex_objeto_ut_formulario'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_ut_formulario', 'pgsql_a50_clase_ut_formulario.sql', 140, 'multiproyecto', NULL, '( objeto_ut_formulario_proyecto = \'%%\' )', 'objeto_ut_formulario', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'objeto_ut_formulario_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'objeto_ut_formulario', 2, 'int4  			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'tabla', 3, 'varchar(100)   	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'titulo', 4, 'varchar(80)    	NULL,       -- Titulo de la interface');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_agregar', 5, 'smallint		NULL,		-- Proponer agregar si no hay estado');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_agregar_etiq', 6, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_mod_modificar', 7, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_mod_modificar_etiq', 8, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_mod_eliminar', 9, 'smallint       	NULL,       -- Pantalla de modificacion: Se permite eliminar registros ?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_mod_eliminar_etiq', 10, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_mod_limpiar', 11, 'smallint       	NULL,       -- Pantalla de modificacion: Se permite limpiar el formulario?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_mod_limpiar_etiq', 12, 'varchar(30)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_mod_clave', 13, 'smallint       	NULL,       -- Se permite modificar la clave??');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'clase_proyecto', 14, 'varchar(15)		NULL,  -- Que tipo de UT hay que wrappear?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'clase', 15, 'varchar(60)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'auto_reset', 16, 'smallint       	NULL,       -- Se resetea el formulario despues de transaccionar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ancho', 17, 'varchar(10)    	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ancho_etiqueta', 18, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'campo_bl', 19, 'varchar(40)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'scroll', 20, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'filas', 21, 'smallint       	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'filas_agregar', 22, 'smallint       	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'filas_agregar_online', 23, 'smallint		NULL DEFAULT 1,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'filas_undo', 24, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'filas_ordenar', 25, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'columna_orden', 26, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'filas_numerar', 27, 'smallint 		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'ev_seleccion', 28, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'alto', 29, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario', 'analisis_cambios', 30, 'varchar(10)		NULL,');

------  'apex_objeto_ut_formulario_ef'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'pgsql_a50_clase_ut_formulario.sql', 141, 'multiproyecto', NULL, '( objeto_ut_formulario_proyecto = \'%%\' )', 'objeto_ut_formulario, identificador', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'objeto_ut_formulario_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'objeto_ut_formulario', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'identificador', 3, 'varchar(30)    	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'columnas', 4, 'varchar(255)   	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'clave_primaria', 5, 'smallint       	NULL,			-- El contenido de este EF es parte de una clave primaria?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'obligatorio', 6, 'smallint       	NULL,			-- El contenido de este EF es obligatorio?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'elemento_formulario', 7, 'varchar(30)    	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'inicializacion', 8, 'varchar        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'orden', 9, 'float       	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'etiqueta', 10, 'varchar(80)    	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'descripcion', 11, 'varchar        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'colapsado', 12, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'desactivado', 13, 'smallint       	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'no_sql', 14, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'total', 15, 'smallint		NULL,			-- Indica si el EF aparece en la fila de total');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'clave_primaria_padre', 16, 'smallint       	NULL,			-- El contenido de este EF es parte de una clave primaria?');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'listar', 17, 'smallint       	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'lista_cabecera', 18, 'varchar(40)    	NULL,			-- Titulo del campo en la lista');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'lista_orden', 19, 'float       	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'lista_columna_estilo', 20, 'int4		    NULL,			-- Estilo de la columna');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'lista_valor_sql', 21, 'varchar(40)    	NULL,			-- Campo SQL alternativo');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'lista_valor_sql_formato', 22, 'int4		    NULL,			-- El valor del debe ser formateado');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'lista_valor_sql_esp', 23, 'varchar(40)	    NULL,			-- El valor del debe ser formateado CUSTOM');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ut_formulario_ef', 'lista_ancho', 24, 'varchar(10)		NULL,');

------  'apex_objeto_ei_formulario_ef'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'pgsql_a50_clase_ut_formulario.sql', 142, 'multiproyecto', NULL, '( objeto_ei_formulario_proyecto = \'%%\' )', 'objeto_ei_formulario, identificador', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'objeto_ei_formulario_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'objeto_ei_formulario', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'objeto_ei_formulario_fila', 3, 'int4			DEFAULT nextval(\'\"apex_obj_ei_form_fila_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'identificador', 4, 'varchar(30)    	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'elemento_formulario', 5, 'varchar(30)    	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'columnas', 6, 'varchar(255)   	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'obligatorio', 7, 'smallint       	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'inicializacion', 8, 'varchar        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'orden', 9, 'float       	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'etiqueta', 10, 'varchar(80)    	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'descripcion', 11, 'varchar        	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'colapsado', 12, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'desactivado', 13, 'smallint       	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'estilo', 14, 'int4		    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ei_formulario_ef', 'total', 15, 'smallint		NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_obj_ei_form_fila_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a51_clase_ut_multicheq.sql
--####
--######################################################################################

------  'apex_objeto_multicheq'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_multicheq', 'pgsql_a51_clase_ut_multicheq.sql', 143, 'multiproyecto', NULL, '( objeto_multicheq_proyecto = \'%%\' )', 'objeto_multicheq', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_multicheq', 'objeto_multicheq_proyecto', 1, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_multicheq', 'objeto_multicheq', 2, 'int4           NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_multicheq', 'sql', 3, 'varchar			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_multicheq', 'claves', 4, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_multicheq', 'descripcion', 5, 'varchar(255)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_multicheq', 'chequeado', 6, 'varchar(100)	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_multicheq', 'forzar_chequeo', 7, 'smallint		NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a52_clase_mt_me.sql
--####
--######################################################################################

------  'apex_objeto_mt_me_tipo_nav'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_mt_me_tipo_nav', 'pgsql_a52_clase_mt_me.sql', 144, 'proyecto', NULL, NULL, 'tipo_navegacion', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_tipo_nav', 'tipo_navegacion', 1, 'varchar(10)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_tipo_nav', 'descripcion', 2, 'varchar(30)			NOT	NULL,');

------  'apex_objeto_mt_me'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_mt_me', 'pgsql_a52_clase_mt_me.sql', 145, 'multiproyecto', NULL, '(	objeto_mt_me_proyecto =	\'%%\' )', 'objeto_mt_me', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'objeto_mt_me_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'objeto_mt_me', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'ev_procesar_etiq', 3, 'varchar(30)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'ev_cancelar_etiq', 4, 'varchar(30)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'ancho', 5, 'varchar(20)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'alto', 6, 'varchar(20)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'posicion_botonera', 7, 'varchar(10)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'tipo_navegacion', 8, 'varchar(10)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'con_toc', 9, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'incremental', 10, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'debug_eventos', 11, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'activacion_procesar', 12, 'varchar(40)			NULL, --> DEPRECADO CN: Indica cuando procesar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'activacion_cancelar', 13, 'varchar(40)			NULL, --> DEPRECADO CN: Indica cuando se puede cancelar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'ev_procesar', 14, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'ev_cancelar', 15, 'smallint			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'objetos', 16, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'post_procesar', 17, 'varchar(40)			NULL, --> CN: Informacion posterior al proceso');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'metodo_despachador', 18, 'varchar(40)			NULL,  --> CN: Indica la etapa activa');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me', 'metodo_opciones', 19, 'varchar(40)			NULL,  --> CN: Indica los posibles caminos de la operacion');

------  'apex_objeto_mt_me_etapa'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'pgsql_a52_clase_mt_me.sql', 146, 'multiproyecto', NULL, '(	objeto_mt_me_proyecto =	\'%%\' )', 'objeto_mt_me,	posicion', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'objeto_mt_me_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'objeto_mt_me', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'posicion', 3, 'smallint			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'orden', 4, 'smallint			NULL,	-- Hay que ponerlo como NOT NULL');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'etiqueta', 5, 'varchar(80)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'descripcion', 6, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'tip', 7, 'varchar(80)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'imagen_recurso_origen', 8, 'varchar(10)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'imagen', 9, 'varchar(60)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'objetos', 10, 'varchar(80)			NULL, 	-- ya no se usan!');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'objetos_adhoc', 11, 'varchar(80)			NULL, 	-- ya no se usan!');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'pre_condicion', 12, 'varchar(40)			NULL,	-- ya no se usan!');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'post_condicion', 13, 'varchar(40)			NULL,	-- ya no se usan!');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'gen_interface_pre', 14, 'varchar(40)			NULL,	-- ya no se usan!');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'gen_interface_post', 15, 'varchar(40)			NULL,	-- ya no se usan!');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'ev_procesar', 16, 'smallint			NULL, 	-- Esta etapa muestra el boton procesar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_mt_me_etapa', 'ev_cancelar', 17, 'smallint			NULL, 	-- Esta etapa muestra el boton cancelar');

------  'apex_objeto_ci_pantalla'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_ci_pantalla', 'pgsql_a52_clase_mt_me.sql', 147, 'multiproyecto', NULL, '(	objeto_ci_proyecto =	\'%%\' )', 'objeto_ci_proyecto, objeto_ci, pantalla', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'objeto_ci_proyecto', 1, 'varchar(15)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'objeto_ci', 2, 'int4				NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'pantalla', 3, 'int4				DEFAULT nextval(\'\"apex_obj_ci_pantalla_seq\"\'::text) NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'identificador', 4, 'varchar(20)			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'orden', 5, 'smallint			NULL,	-- Hay que ponerlo como NOT NULL');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'etiqueta', 6, 'varchar(80)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'descripcion', 7, 'varchar(255)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'tip', 8, 'varchar(80)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'imagen_recurso_origen', 9, 'varchar(10)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'imagen', 10, 'varchar(60)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'objetos', 11, 'varchar(80)			NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_ci_pantalla', 'eventos', 12, 'varchar(80)			NULL,');

INSERT INTO apex_mod_datos_secuencia (proyecto,secuencia) VALUES ('toba','apex_obj_ci_pantalla_seq');

--######################################################################################
--####
--####    ARCHIVO:  pgsql_a53_clase_negocio.sql
--####
--######################################################################################

------  'apex_objeto_negocio'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_negocio', 'pgsql_a53_clase_negocio.sql', 148, 'multiproyecto', NULL, '( objeto_negocio_proyecto = \'%%\' )', 'objeto_negocio', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio', 'objeto_negocio_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio', 'objeto_negocio', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio', 'descripcion', 3, 'varchar(255)    NOT NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar');

------  'apex_objeto_negocio_regla'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_negocio_regla', 'pgsql_a53_clase_negocio.sql', 149, 'multiproyecto', NULL, '( objeto_negocio_proyecto = \'%%\' )', 'objeto_negocio, nombre', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio_regla', 'objeto_negocio_proyecto', 1, 'varchar(15)   	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio_regla', 'objeto_negocio', 2, 'int4          	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio_regla', 'nombre', 3, 'varchar(80)    	NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio_regla', 'descripcion', 4, 'varchar(255)    NOT NULL,       -- SQL que arma el cuadro que permite elegir un registro a modificar');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio_regla', 'activada', 5, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio_regla', 'mensaje_a', 6, 'varchar(255)    NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_negocio_regla', 'mensaje_b', 7, 'varchar(255)    NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a66_clase_esquema.sql
--####
--######################################################################################

------  'apex_objeto_esquema'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_esquema', 'pgsql_a66_clase_esquema.sql', 150, 'multiproyecto', NULL, '( objeto_esquema_proyecto = \'%%\' )', 'objeto_esquema', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'objeto_esquema_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'objeto_esquema', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'parser', 3, 'varchar(30)  	NULL, -- NEATO, DOT, ETC');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'descripcion', 4, 'varchar(80)  	NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'dot', 5, 'varchar			NULL, --Descripcion del grafico en sintaxis DOT');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'debug', 6, 'smallint		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'formato', 7, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'modelo_ejecucion', 8, 'varchar(15)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'modelo_ejecucion_cache', 9, 'smallint		NULL, -- Usar el cache??');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'tipo_incrustacion', 10, 'varchar(15)		NULL, -- IMG o IFRAME');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'ancho', 11, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'alto', 12, 'varchar(10)		NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_esquema', 'sql', 13, 'varchar			NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a67_clase_html.sql
--####
--######################################################################################

------  'apex_objeto_html'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_objeto_html', 'pgsql_a67_clase_html.sql', 151, 'multiproyecto', NULL, '( objeto_html_proyecto = \'%%\' )', 'objeto_html', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_html', 'objeto_html_proyecto', 1, 'varchar(15)		NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_html', 'objeto_html', 2, 'int4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_objeto_html', 'html', 3, 'varchar			NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a98_test.sql
--####
--######################################################################################

------  'apex_test_paises'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_test_paises', 'pgsql_a98_test.sql', 152, 'proyecto', NULL, NULL, 'pais', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_test_paises', 'pais', 1, 'INT4			NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_test_paises', 'nombre', 2, 'VARCHAR(40)		NOT NULL,');


--######################################################################################
--####
--####    ARCHIVO:  pgsql_a99_perfiles.sql
--####
--######################################################################################

------  'apex_dim_restric_soltipo'  -----------------------------------
INSERT INTO apex_mod_datos_tabla (proyecto,tabla,script,orden,dump,dump_from,dump_where,dump_order_by,dump_order_by_from,dump_order_by_where,descripcion,version,historica,instancia) VALUES ('toba', 'apex_dim_restric_soltipo', 'pgsql_a99_perfiles.sql', 153, 'proyecto', NULL, NULL, 'solicitud_tipo, usuario_perfil_datos', NULL, NULL, '', '1.0', '0', NULL);
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dim_restric_soltipo', 'solicitud_tipo', 1, 'varchar(20)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dim_restric_soltipo', 'usuario_perfil_datos_proyecto', 2, 'varchar(15)    NOT NULL,');
INSERT INTO apex_mod_datos_tabla_columna (tabla_proyecto,tabla,columna,orden,definicion) VALUES ('toba', 'apex_dim_restric_soltipo', 'usuario_perfil_datos', 3, 'varchar(20)    NOT NULL,');

