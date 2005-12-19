-- Actualizacion del valor de las secuencias

	SELECT setval('apex_sesion_browser_seq', max(sesion_browser)) FROM apex_sesion_browser;
	SELECT setval('apex_solicitud_seq', max(solicitud)) FROM apex_solicitud; 
	SELECT setval('apex_solicitud_observacion_seq', max(solicitud_observacion)) FROM apex_solicitud_observacion; 
	SELECT setval('apex_solicitud_obj_obs_seq', max(solicitud_obj_observacion)) FROM apex_solicitud_obj_observacion; 