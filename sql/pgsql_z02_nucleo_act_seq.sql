-- Actualizacion del valor de las secuencias

	SELECT setval('apex_sesion_browser_seq', max(sesion_browser)) FROM apex_sesion_browser;
