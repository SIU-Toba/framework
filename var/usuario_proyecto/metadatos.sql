-- %proyecto %				ID del proyecto
-- %item %					ID del ITEM
-- %path_subclase %			Lugar donde esta la sublcase del MT (ej: acciones/admin/usuarios/usuario2_clases.php)
-- %fuente %				ID de la fuente de datos

----------> OBJETOS 

---- UT (propiedades usuario y usuario-proyecto )

INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, usuario, creacion) VALUES ('%proyecto%','610',NULL,NULL,'toba','objeto_ut_formulario',NULL,NULL,NULL,NULL,'USUARIO - Propiedades',NULL,NULL,'%proyecto%','%fuente%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2004-09-08 20:36:23');
INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, usuario, creacion) VALUES ('%proyecto%','611',NULL,NULL,'toba','objeto_ut_formulario',NULL,NULL,NULL,NULL,'USUARIO - Proyecto',NULL,NULL,'%proyecto%','%fuente%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2004-09-08 20:36:41');

INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_mod_eliminar, ev_mod_clave, ev_mod_limpiar, clase_proyecto, clase, auto_reset, ancho, campo_bl) VALUES ('%proyecto%','610','apex_usuario','Propiedades del usuario','1',NULL,'1',NULL,NULL,NULL,NULL,NULL);
INSERT INTO apex_objeto_ut_formulario (objeto_ut_formulario_proyecto, objeto_ut_formulario, tabla, titulo, ev_mod_eliminar, ev_mod_clave, ev_mod_limpiar, clase_proyecto, clase, auto_reset, ancho, campo_bl) VALUES ('%proyecto%','611','apex_usuario_proyecto','Relacion con el Proyecto',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO apex_objeto_ut_formulario_ef (objeto_ut_formulario_proyecto, objeto_ut_formulario, identificador, columnas, clave_primaria, obligatorio, elemento_formulario, inicializacion, orden, etiqueta, descripcion, desactivado, clave_primaria_padre, listar, lista_cabecera, lista_orden, lista_columna_estilo, lista_valor_sql, lista_valor_sql_formato, lista_valor_sql_esp, lista_ancho) VALUES ('%proyecto%','610','clave','clave',NULL,'1','ef_editable_clave','','3','Clave',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO apex_objeto_ut_formulario_ef (objeto_ut_formulario_proyecto, objeto_ut_formulario, identificador, columnas, clave_primaria, obligatorio, elemento_formulario, inicializacion, orden, etiqueta, descripcion, desactivado, clave_primaria_padre, listar, lista_cabecera, lista_orden, lista_columna_estilo, lista_valor_sql, lista_valor_sql_formato, lista_valor_sql_esp, lista_ancho) VALUES ('%proyecto%','610','nombre','nombre',NULL,'1','ef_editable','tamano: 40;','2','Nombre',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO apex_objeto_ut_formulario_ef (objeto_ut_formulario_proyecto, objeto_ut_formulario, identificador, columnas, clave_primaria, obligatorio, elemento_formulario, inicializacion, orden, etiqueta, descripcion, desactivado, clave_primaria_padre, listar, lista_cabecera, lista_orden, lista_columna_estilo, lista_valor_sql, lista_valor_sql_formato, lista_valor_sql_esp, lista_ancho) VALUES ('%proyecto%','610','usuario','usuario','1','1','ef_editable','tamano: 20;
','1','Identificador',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO apex_objeto_ut_formulario_ef (objeto_ut_formulario_proyecto, objeto_ut_formulario, identificador, columnas, clave_primaria, obligatorio, elemento_formulario, inicializacion, orden, etiqueta, descripcion, desactivado, clave_primaria_padre, listar, lista_cabecera, lista_orden, lista_columna_estilo, lista_valor_sql, lista_valor_sql_formato, lista_valor_sql_esp, lista_ancho) VALUES ('%proyecto%','611','grupo_acceso','proyecto, usuario_grupo_acc',NULL,NULL,'ef_combo_db_proyecto','sql: SELECT proyecto, usuario_grupo_acc, nombre 
FROM apex_usuario_grupo_acc %w% ORDER BY 3;
columna_proyecto: proyecto;
','1','Perfil de ACCESO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO apex_objeto_ut_formulario_ef (objeto_ut_formulario_proyecto, objeto_ut_formulario, identificador, columnas, clave_primaria, obligatorio, elemento_formulario, inicializacion, orden, etiqueta, descripcion, desactivado, clave_primaria_padre, listar, lista_cabecera, lista_orden, lista_columna_estilo, lista_valor_sql, lista_valor_sql_formato, lista_valor_sql_esp, lista_ancho) VALUES ('%proyecto%','611','perfil_datos','proyecto, usuario_perfil_datos',NULL,NULL,'ef_combo_db_proyecto','sql: SELECT proyecto, usuario_perfil_datos, nombre 
FROM apex_usuario_perfil_datos %w% ORDER BY 3;
columna_proyecto: proyecto;
','2','Perfil de DATOS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO apex_objeto_ut_formulario_ef (objeto_ut_formulario_proyecto, objeto_ut_formulario, identificador, columnas, clave_primaria, obligatorio, elemento_formulario, inicializacion, orden, etiqueta, descripcion, desactivado, clave_primaria_padre, listar, lista_cabecera, lista_orden, lista_columna_estilo, lista_valor_sql, lista_valor_sql_formato, lista_valor_sql_esp, lista_ancho) VALUES ('%proyecto%','611','proyecto','proyecto','1','1','ef_oculto','','0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO apex_objeto_ut_formulario_ef (objeto_ut_formulario_proyecto, objeto_ut_formulario, identificador, columnas, clave_primaria, obligatorio, elemento_formulario, inicializacion, orden, etiqueta, descripcion, desactivado, clave_primaria_padre, listar, lista_cabecera, lista_orden, lista_columna_estilo, lista_valor_sql, lista_valor_sql_formato, lista_valor_sql_esp, lista_ancho) VALUES ('%proyecto%','611','usuario','usuario','1','1','ef_oculto','','0.5',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

---- MT

INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, usuario, creacion) VALUES ('%proyecto%','612',NULL,NULL,'toba','objeto_mt_mds','objeto_mt_mds_usuario','%path_subclase%',NULL,NULL,'USUARIO',NULL,NULL,'%proyecto%','%fuente%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2004-09-08 20:37:25');

INSERT INTO apex_objeto_dependencias (proyecto, objeto_consumidor, objeto_proveedor, identificador, inicializar) VALUES ('%proyecto%','612','611','detalle_1',NULL);
INSERT INTO apex_objeto_dependencias (proyecto, objeto_consumidor, objeto_proveedor, identificador, inicializar) VALUES ('%proyecto%','612','610','maestro',NULL);

-- CUADRO

INSERT INTO apex_objeto (proyecto, objeto, anterior, reflexivo, clase_proyecto, clase, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, usuario, creacion) VALUES ('%proyecto%','615',NULL,NULL,'toba','objeto_cuadro',NULL,NULL,NULL,NULL,'USUARIOS',NULL,NULL,'%proyecto%','%fuente%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2004-09-09 17:28:52');
INSERT INTO apex_objeto_cuadro (objeto_cuadro_proyecto, objeto_cuadro, titulo, subtitulo, sql, columnas_clave, archivos_callbacks, ancho, ordenar, paginar, tamano_pagina, eof_invisible, eof_customizado, exportar, exportar_rtf, pdf_propiedades, pdf_respetar_paginacion, asociacion_columnas) VALUES ('%proyecto%','615',NULL,NULL,'SELECT u.usuario as usuario, 
u.nombre as nombre,
ga.nombre as grupo_acceso,
pd.nombre as perfil_datos
FROM apex_usuario u,
apex_usuario_proyecto up,
apex_usuario_perfil_datos pd,
apex_usuario_grupo_acc ga
WHERE u.usuario = up.usuario
AND up.proyecto = pd.proyecto
AND up.usuario_perfil_datos = pd.usuario_perfil_datos
AND up.proyecto = ga.proyecto
AND up.usuario_grupo_acc = ga.usuario_grupo_acc
AND up.proyecto = \'%proyecto%\'
%w%;','usuario',NULL,'600','1',NULL,NULL,NULL,'No existen usuarios','1',NULL,'',NULL,NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('%proyecto%','615','1','Identificador','4',NULL,NULL,NULL,'usuario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','',NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('%proyecto%','615','2','Nombre','4',NULL,NULL,NULL,'nombre',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','',NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('%proyecto%','615','3','Grupo Acceso','4',NULL,NULL,NULL,'grupo_acceso',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','',NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('%proyecto%','615','4','Perfil Datos','4',NULL,NULL,NULL,'perfil_datos',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1','',NULL);
INSERT INTO apex_objeto_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, orden, titulo, columna_estilo, columna_ancho, ancho_html, total, valor_sql, valor_sql_formato, valor_fijo, valor_proceso, valor_proceso_esp, valor_proceso_parametros, vinculo_indice, par_dimension_proyecto, par_dimension, par_tabla, par_columna, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado) VALUES ('%proyecto%','615','5','Ed.','4',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'usu',NULL,NULL,NULL,NULL,'1','1','1','',NULL);

-- Vinculo

INSERT INTO apex_vinculo (origen_item_id, origen_item_proyecto, origen_item, origen_objeto_proyecto, origen_objeto, destino_item_id, destino_item_proyecto, destino_item, destino_objeto_proyecto, destino_objeto, frame, canal, indice, vinculo_tipo, inicializacion, operacion, texto, imagen_recurso_origen, imagen) VALUES (NULL,'%proyecto%','/autovinculo','%proyecto%','615',NULL,'%proyecto%','/autovinculo','%proyecto%','612',NULL,NULL,'usu','normal',NULL,NULL,'Editar Usuario','apex','doc.gif');

--- Relacion con items

INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (NULL,'%proyecto%','%item%','612','0',NULL);
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (NULL,'%proyecto%','%item%','615','0',NULL);
