-- Actualizacion del valor de las secuencias

	SELECT setval('apex_clase_tipo_seq', max(clase_tipo)) FROM apex_clase_tipo;
	SELECT setval('apex_columna_estilo_seq', max(columna_estilo)) FROM apex_columna_estilo;
	SELECT setval('apex_columna_formato_seq', max(columna_formato)) FROM apex_columna_formato;
	SELECT setval('apex_nota_seq', max(nota)) FROM apex_nota;
	SELECT setval('apex_nucleo_nota_seq', max(nucleo_nota)) FROM apex_nucleo_nota;
	SELECT setval('apex_item_nota_seq', max(item_nota)) FROM apex_item_nota;
	SELECT setval('apex_objeto_nota_seq', max(objeto_nota)) FROM apex_objeto_nota;
	SELECT setval('apex_clase_nota_seq', max(clase_nota)) FROM apex_clase_nota;
	SELECT setval('apex_patron_nota_seq', max(patron_nota)) FROM apex_patron_nota;
	SELECT setval('apex_buffer_seq', max(buffer)) FROM apex_buffer;
	SELECT setval('apex_item_seq', max(item_id)) FROM apex_item;
	SELECT setval('apex_objeto_seq', max(objeto)) FROM apex_objeto;
	SELECT setval('apex_log_sistema_seq', max(log_sistema)) FROM apex_log_sistema;
	SELECT setval('apex_sesion_browser_seq', max(sesion_browser)) FROM apex_sesion_browser;
	SELECT setval('apex_solicitud_seq', max(solicitud)) FROM apex_solicitud;
	SELECT setval('apex_solicitud_observacion_seq', max(solicitud_observacion)) FROM apex_solicitud_observacion;
	SELECT setval('apex_solicitud_obj_obs_seq', max(solicitud_obj_observacion)) FROM apex_solicitud_obj_observacion;
	SELECT setval('apex_log_error_login_seq', max(log_error_login)) FROM apex_log_error_login;
	SELECT setval('apex_ap_tarea_tipo_seq', max(tarea_tipo)) FROM apex_ap_tarea_tipo;
	SELECT setval('apex_ap_tarea_estado_seq', max(tarea_estado)) FROM apex_ap_tarea_estado;
	SELECT setval('apex_ap_tarea_tema_seq', max(tarea_tema)) FROM apex_ap_tarea_tema;
	SELECT setval('apex_ap_tarea_seq', max(tarea)) FROM apex_ap_tarea;
	SELECT setval('apex_objeto_plan_linea_seq', max(linea)) FROM apex_objeto_plan_linea;
	SELECT setval('apex_patron_msg_seq', max(patron_msg)) FROM apex_patron_msg;
	SELECT setval('apex_item_msg_seq', max(item_msg)) FROM apex_item_msg;
	SELECT setval('apex_clase_msg_seq', max(clase_msg)) FROM apex_clase_msg;
	SELECT setval('apex_objeto_msg_seq', max(objeto_msg)) FROM apex_objeto_msg;
	SELECT setval('apex_msg_seq', max(msg)) FROM apex_msg;

