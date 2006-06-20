INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'Cronometro', 'toba', 'checkbox', NULL, NULL, 'Cronometrado', 'Filtrar elementos cronometrados', 'valor: 1;
valor_info: SI;
operador: >=;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'Mes', 'toba', 'mes', NULL, NULL, 'Mes posterior a', 'Elegir un mes', 'anio_i: 2000;
anio_f: 2005;
operador: >=;
operador_texo: Mes mayor o igual a;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'buscar_ereg', 'toba', 'texto_operador', NULL, NULL, 'Buscar Cadena (ereg)', 'Busca una expresion regular en un campo', 'tamano: 20;
maximo: 40;
operador: ~*;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'lapso', 'toba', 'mes_lapso', NULL, NULL, 'Lapso de Meses', 'Especificar un lapso de meses
(Lo modificamos, ya que de la forma en la que estaba planteada, no funcionaba correctamente)', 'anio_i: 2004;
anio_f: 2005;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'proyecto', 'toba', 'combo_db', NULL, NULL, 'Proyecto', 'Elegir un proyecto', 'sql: SELECT proyecto, descripcion_corta FROM apex_proyecto;
no_seteado: No Filtrar;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'solicitud_tipo', 'toba', 'combo_db', NULL, NULL, 'Tipo de Solicitud', 'Tipos de solicitud del sistema', 'sql: SELECT usuario
FROM apex_log_sistema
WHERE usuario = \'fantasma\';
', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'solicitud_tipo_r', 'toba', 'combo_db_restric', NULL, NULL, 'Solicitud', 'Solicitudes', 'tab_ref: apex_solicitud_tipo;
tab_ref_clave: solicitud_tipo;
tab_ref_des: descripcion_corta;
tab_ref_where: (r.solicitud_tipo <> \'fantasma\');
tab_restric: apex_dim_restric_soltipo;
', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'tarea_estado', 'toba', 'combo_db', NULL, NULL, 'Estado', 'Estado de la tarea', 'sql: SELECT tarea_estado, descripcion
FROM apex_ap_tarea_estado;
no_seteado: Todas;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'tarea_prioridad', 'toba', 'combo_db', NULL, NULL, 'Prioridad', 'Prioridad de la tarea', 'sql: SELECT tarea_prioridad, descripcion
FROM apex_ap_tarea_prioridad;
no_seteado: Todas;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'tarea_tema', 'toba', 'combo_db', NULL, NULL, 'Tema', 'Tema', 'sql: SELECT tarea_tema, descripcion
FROM apex_ap_tarea_tema;
no_seteado: Todos;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'tarea_tipo', 'toba', 'combo_db', NULL, NULL, 'Tipo', 'Tipo de tarea', 'sql: SELECT tarea_tipo, descripcion
FROM apex_ap_tarea_tipo;
no_seteado: Todos;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'tiempo', 'toba', 'numero_conector', NULL, NULL, 'Tiempo', 'Buscar un tiempo de ejecucion', 'digitos: 4;', 'toba', 'instancia', NULL, NULL, NULL, NULL);
INSERT INTO apex_dimension (proyecto, dimension, dimension_tipo_proyecto, dimension_tipo, dimension_grupo_proyecto, dimension_grupo, nombre, descripcion, inicializacion, fuente_datos_proyecto, fuente_datos, tabla_ref, tabla_ref_clave, tabla_ref_desc, tabla_restric) VALUES ('admin', 'version', 'toba', 'combo_db_proyecto', NULL, NULL, 'Version', 'Version del proyecto', 'sql: SELECT proyecto, version, version
FROM apex_ap_version %w%;
columna_proyecto: proyecto;
no_seteado: Todas;
', 'toba', 'instancia', NULL, NULL, NULL, NULL);
